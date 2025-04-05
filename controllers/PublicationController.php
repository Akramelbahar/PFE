<?php
require_once './core/Controller.php';
require_once './models/publications/Publication.php';
require_once './models/publications/Article.php';
require_once './models/publications/Livre.php';
require_once './models/publications/Chapitre.php';
require_once './utils/FileManager.php';

/**
 * Publication Controller
 */
class PublicationController extends Controller {
    /**
     * Publications index page
     */
    public function index() {
        // Get filter parameters
        $type = $this->getInput('type');
        $author = $this->getInput('author');
        $year = $this->getInput('year');
        $search = $this->getInput('search');

        // Get publications with filters
        $publications = $this->getPublications($type, $author, $year, $search);

        // Get available filters
        $filters = $this->getPublicationFilters();

        $this->render('publications/index', [
            'pageTitle' => 'Publications',
            'publications' => $publications,
            'filters' => $filters,
            'currentFilters' => [
                'type' => $type,
                'author' => $author,
                'year' => $year,
                'search' => $search
            ]
        ]);
    }


    private function getPublications($type = null, $author = null, $year = null, $search = null) {
        $db = Db::getInstance();

        $params = [];
        $conditions = [];

        $query = "
            SELECT p.*, u.nom as auteurNom, u.prenom as auteurPrenom,
                CASE 
                    WHEN a.publicationId IS NOT NULL THEN 'Article' 
                    WHEN l.publicationId IS NOT NULL THEN 'Livre'
                    WHEN c.publicationId IS NOT NULL THEN 'Chapitre'
                    ELSE 'Standard'
                END as type
            FROM Publication p
            LEFT JOIN Article a ON p.id = a.publicationId
            LEFT JOIN Livre l ON p.id = l.publicationId
            LEFT JOIN Chapitre c ON p.id = c.publicationId
            LEFT JOIN Utilisateur u ON p.auteurId = u.id
        ";

        // Add filters
        if ($type) {
            if ($type === 'Article') {
                $conditions[] = "a.publicationId IS NOT NULL";
            } elseif ($type === 'Livre') {
                $conditions[] = "l.publicationId IS NOT NULL";
            } elseif ($type === 'Chapitre') {
                $conditions[] = "c.publicationId IS NOT NULL";
            }
        }

        if ($author) {
            $conditions[] = "p.auteurId = :author";
            $params['author'] = $author;
        }

        if ($year) {
            $conditions[] = "YEAR(p.datePublication) = :year";
            $params['year'] = $year;
        }

        if ($search) {
            $conditions[] = "(p.titre LIKE :search OR p.contenu LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        // Add WHERE clause if there are conditions
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        // Add ORDER BY
        $query .= " ORDER BY p.datePublication DESC";

        $stmt = $db->prepare($query);

        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    private function getPublicationFilters() {
        $db = Db::getInstance();
        $filters = [];

        // Types
        $filters['types'] = ['Article', 'Livre', 'Chapitre'];

        // Authors
        $stmt = $db->query("
            SELECT DISTINCT u.id, u.nom, u.prenom
            FROM Utilisateur u
            JOIN Publication p ON u.id = p.auteurId
            ORDER BY u.nom, u.prenom
        ");
        $filters['authors'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Years
        $stmt = $db->query("
            SELECT DISTINCT YEAR(datePublication) as year
            FROM Publication
            ORDER BY year DESC
        ");
        $filters['years'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $filters;
    }


    public function view($id) {
        // Get publication with author
        $publicationModel = new Publication();
        $publication = $publicationModel->findWithAuthor($id);

        if (!$publication) {
            $this->renderNotFound();
            return;
        }

        // Get publication type and specific details
        $publicationType = $this->getPublicationType($id);
        $publicationDetails = $this->getPublicationDetails($id, $publicationType);

        // Get related publications
        $relatedPublications = $this->getRelatedPublications($publication);

        $this->render('publications/view', [
            'pageTitle' => $publication['titre'],
            'publication' => $publication,
            'publicationType' => $publicationType,
            'publicationDetails' => $publicationDetails,
            'relatedPublications' => $relatedPublications
        ]);
    }

    private function getPublicationType($id) {
        $db = Db::getInstance();

        $stmt = $db->prepare("SELECT COUNT(*) FROM Article WHERE publicationId = :id");
        $stmt->execute(['id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            return 'Article';
        }

        $stmt = $db->prepare("SELECT COUNT(*) FROM Livre WHERE publicationId = :id");
        $stmt->execute(['id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            return 'Livre';
        }

        $stmt = $db->prepare("SELECT COUNT(*) FROM Chapitre WHERE publicationId = :id");
        $stmt->execute(['id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            return 'Chapitre';
        }

        return 'Standard';
    }


    private function getPublicationDetails($id, $type) {
        $db = Db::getInstance();

        if ($type === 'Article') {
            $articleModel = new Article();
            return null;
        } elseif ($type === 'Livre') {
            $livreModel = new Livre();

            $stmt = $db->prepare("
                SELECT c.*, p.titre, p.datePublication
                FROM Chapitre c
                JOIN Publication p ON c.publicationId = p.id
                WHERE c.LivrePere = :id
                ORDER BY p.datePublication
            ");
            $stmt->execute(['id' => $id]);

            return ['chapters' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } elseif ($type === 'Chapitre') {
            $chapitreModel = new Chapitre();

            $stmt = $db->prepare("
                SELECT l.*, p.titre, p.datePublication, u.nom as auteurNom, u.prenom as auteurPrenom
                FROM Chapitre c
                JOIN Livre l ON c.LivrePere = l.publicationId
                JOIN Publication p ON l.publicationId = p.id
                LEFT JOIN Utilisateur u ON p.auteurId = u.id
                WHERE c.publicationId = :id
            ");
            $stmt->execute(['id' => $id]);

            return ['book' => $stmt->fetch(PDO::FETCH_ASSOC)];
        }

        return null;
    }

    private function getRelatedPublications($publication) {
        $db = Db::getInstance();

        // Get publications from same author
        if ($publication['auteurId']) {
            $stmt = $db->prepare("
                SELECT p.*, 
                    CASE 
                        WHEN a.publicationId IS NOT NULL THEN 'Article' 
                        WHEN l.publicationId IS NOT NULL THEN 'Livre'
                        WHEN c.publicationId IS NOT NULL THEN 'Chapitre'
                        ELSE 'Standard'
                    END as type
                FROM Publication p
                LEFT JOIN Article a ON p.id = a.publicationId
                LEFT JOIN Livre l ON p.id = l.publicationId
                LEFT JOIN Chapitre c ON p.id = c.publicationId
                WHERE p.auteurId = :auteurId AND p.id != :id
                ORDER BY p.datePublication DESC
                LIMIT 5
            ");

            $stmt->execute([
                'auteurId' => $publication['auteurId'],
                'id' => $publication['id']
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    }


    public function create() {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        // Ensure user has permission
        if (!$this->requirePermission('add_publication')) {
            return;
        }

        $this->render('publications/create', [
            'pageTitle' => 'Créer une publication'
        ]);
    }


    public function store() {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        // Ensure user has permission
        if (!$this->requirePermission('add_publication')) {
            return;
        }

        // Check if form was submitted
        if (!$this->isPost()) {
            $this->redirect('publications/create');
            return;
        }

        // Get form data
        $titre = $this->getInput('titre');
        $contenu = $this->getInput('contenu');
        $type = $this->getInput('type');
        $evenementId = $this->getInput('evenement_id') ?: null;
        $projetId = $this->getInput('projet_id') ?: null;

        // Validate input
        $validation = $this->validate(
            [
                'titre' => $titre,
                'contenu' => $contenu,
                'type' => $type
            ],
            [
                'titre' => 'required|max:255',
                'contenu' => 'required',
                'type' => 'required'
            ]
        );

        if ($validation !== true) {
            $this->render('publications/create', [
                'pageTitle' => 'Créer une publication',
                'errors' => $validation,
                'titre' => $titre,
                'contenu' => $contenu,
                'type' => $type,
                'evenement_id' => $evenementId,
                'projet_id' => $projetId
            ]);
            return;
        }

        // Handle document uploads
        $documents = $this->handleDocumentUploads();

        // Create publication
        $publicationData = [
            'titre' => $titre,
            'contenu' => $contenu,
            'auteurId' => $this->auth->getUser()['id'],
            'datePublication' => date('Y-m-d H:i:s'),
            'evenementId' => $evenementId,
            'projetId' => $projetId,
            'documents' => json_encode($documents),
            'mediaUrl' => null
        ];

        $publicationModel = new Publication();
        $publicationId = $publicationModel->create($publicationData);

        if (!$publicationId) {
            $this->render('publications/create', [
                'pageTitle' => 'Créer une publication',
                'errorMessage' => 'Une erreur est survenue lors de la création de la publication.',
                'titre' => $titre,
                'contenu' => $contenu,
                'type' => $type,
                'evenement_id' => $evenementId,
                'projet_id' => $projetId
            ]);
            return;
        }

        // Create specific publication type
        switch ($type) {
            case 'Article':
                $articleModel = new Article();
                $articleModel->createFromPublication($publicationId);
                break;

            case 'Livre':
                $livreModel = new Livre();
                $livreModel->createFromPublication($publicationId);
                break;

            case 'Chapitre':
                $livrePere = $this->getInput('livre_pere') ?: null;
                $chapitreModel = new Chapitre();
                $chapitreModel->createFromPublication($publicationId, $livrePere);
                break;
        }

        $this->setFlash('success', 'La publication a été créée avec succès.');
        $this->redirect('publications/' . $publicationId);
    }


    public function edit($id) {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        // Get publication
        $publicationModel = new Publication();
        $publication = $publicationModel->find($id);

        if (!$publication) {
            $this->renderNotFound();
            return;
        }

        // Check if user is author or has permission
        $isAuthor = $publication['auteurId'] === $this->auth->getUser()['id'];
        $canEdit = $isAuthor ?
            $this->auth->hasPermission('edit_own_publication') :
            $this->auth->hasPermission('edit_publication');

        if (!$canEdit) {
            $this->renderForbidden();
            return;
        }

        // Get publication type and specific details
        $publicationType = $this->getPublicationType($id);
        $publicationDetails = $this->getPublicationDetails($id, $publicationType);

        $this->render('publications/edit', [
            'pageTitle' => 'Modifier la publication',
            'publication' => $publication,
            'publicationType' => $publicationType,
            'publicationDetails' => $publicationDetails
        ]);
    }


    public function update($id) {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        // Get publication
        $publicationModel = new Publication();
        $publication = $publicationModel->find($id);

        if (!$publication) {
            $this->renderNotFound();
            return;
        }

        // Check if user is author or has permission
        $isAuthor = $publication['auteurId'] === $this->auth->getUser()['id'];
        $canEdit = $isAuthor ?
            $this->auth->hasPermission('edit_own_publication') :
            $this->auth->hasPermission('edit_publication');

        if (!$canEdit) {
            $this->renderForbidden();
            return;
        }

        // Check if form was submitted
        if (!$this->isPost()) {
            $this->redirect('publications/edit/' . $id);
            return;
        }

        // Get form data
        $titre = $this->getInput('titre');
        $contenu = $this->getInput('contenu');
        $evenementId = $this->getInput('evenement_id') ?: null;
        $projetId = $this->getInput('projet_id') ?: null;

        // Validate input
        $validation = $this->validate(
            [
                'titre' => $titre,
                'contenu' => $contenu
            ],
            [
                'titre' => 'required|max:255',
                'contenu' => 'required'
            ]
        );

        if ($validation !== true) {
            $this->setFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
            $this->redirect('publications/edit/' . $id);
            return;
        }

        // Handle document uploads
        $newDocuments = $this->handleDocumentUploads();

        // Get existing documents
        $existingDocuments = json_decode($publication['documents'] ?? '[]', true);

        // Merge documents
        $documents = array_merge($existingDocuments, $newDocuments);

        // Update publication
        $publicationData = [
            'titre' => $titre,
            'contenu' => $contenu,
            'evenementId' => $evenementId,
            'projetId' => $projetId,
            'documents' => json_encode($documents)
        ];

        $updated = $publicationModel->update($id, $publicationData);

        if ($updated) {
            // Update specific details based on publication type
            $publicationType = $this->getPublicationType($id);

            if ($publicationType === 'Chapitre') {
                $livrePere = $this->getInput('livre_pere') ?: null;
                $chapitreModel = new Chapitre();
                $chapitreModel->update($id, ['LivrePere' => $livrePere]);
            }

            $this->setFlash('success', 'La publication a été mise à jour avec succès.');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de la mise à jour de la publication.');
        }

        $this->redirect('publications/' . $id);
    }


    public function delete($id) {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        // Get publication
        $publicationModel = new Publication();
        $publication = $publicationModel->find($id);

        if (!$publication) {
            $this->renderNotFound();
            return;
        }

        // Check if user is author or has permission
        $isAuthor = $publication['auteurId'] === $this->auth->getUser()['id'];
        $canDelete = $isAuthor ?
            $this->auth->hasPermission('delete_own_publication') :
            $this->auth->hasPermission('delete_publication');

        if (!$canDelete) {
            $this->renderForbidden();
            return;
        }

        // Check if form was submitted
        if (!$this->isPost()) {
            $this->redirect('publications/' . $id);
            return;
        }

        // Delete files
        $this->deletePublicationFiles($publication);

        // Delete publication
        $deleted = $publicationModel->delete($id);

        if ($deleted) {
            $this->setFlash('success', 'La publication a été supprimée avec succès.');
            $this->redirect('publications');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de la suppression de la publication.');
            $this->redirect('publications/' . $id);
        }
    }


    public function deleteDocument($id, $filename) {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        // Get publication
        $publicationModel = new Publication();
        $publication = $publicationModel->find($id);

        if (!$publication) {
            $this->renderNotFound();
            return;
        }

        // Check if user is author or has permission
        $isAuthor = $publication['auteurId'] === $this->auth->getUser()['id'];
        $canEdit = $isAuthor ?
            $this->auth->hasPermission('edit_own_publication') :
            $this->auth->hasPermission('edit_publication');

        if (!$canEdit) {
            $this->renderForbidden();
            return;
        }

        // Get documents
        $documents = json_decode($publication['documents'] ?? '[]', true);

        // Find document
        $documentIndex = null;
        foreach ($documents as $index => $document) {
            if ($document['filename'] === $filename) {
                $documentIndex = $index;
                break;
            }
        }

        if ($documentIndex === null) {
            $this->setFlash('error', 'Document introuvable.');
            $this->redirect('publications/edit/' . $id);
            return;
        }

        // Delete file
        $fileManager = new FileManager('uploads/publications/');
        $fileManager->delete($filename);

        // Remove from documents
        array_splice($documents, $documentIndex, 1);

        // Update publication
        $updated = $publicationModel->update($id, [
            'documents' => json_encode($documents)
        ]);

        if ($updated) {
            $this->setFlash('success', 'Le document a été supprimé avec succès.');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de la suppression du document.');
        }

        $this->redirect('publications/edit/' . $id);
    }


    private function handleDocumentUploads() {
        $documents = [];

        // Check if there are document uploads
        if (isset($_FILES['documents']) && is_array($_FILES['documents']['name'])) {
            $fileManager = new FileManager('uploads/publications/', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);

            // Upload each document
            $uploadResults = $fileManager->uploadMultiple($_FILES['documents']);

            foreach ($uploadResults as $result) {
                $documents[] = [
                    'filename' => $result['filename'],
                    'originalName' => $_FILES['documents']['name'][array_search($result['filename'], $uploadResults)],
                    'path' => $result['path'],
                    'size' => $result['size'],
                    'mime' => $result['mime']
                ];
            }
        }

        return $documents;
    }


    private function deletePublicationFiles($publication) {
        if (!empty($publication['documents'])) {
            $documents = json_decode($publication['documents'], true);

            if (is_array($documents)) {
                $fileManager = new FileManager('uploads/publications/');

                foreach ($documents as $document) {
                    $fileManager->delete($document['filename']);
                }
            }
        }
    }
    /**
     * Download publication document
     * @param int $id Publication ID
     * @param string $filename Filename to download
     */
    public function downloadDocument($id, $filename) {
        if (!$this->requireAuth()) {
            return;
        }

        $publicationModel = new Publication();
        $publication = $publicationModel->find($id);

        if (!$publication) {
            $this->renderNotFound();
            return;
        }

        // Find the document in the publication's documents
        $documents = json_decode($publication['documents'] ?? '[]', true);
        $documentExists = false;

        foreach ($documents as $document) {
            if ($document['filename'] === $filename) {
                $documentExists = true;
                break;
            }
        }

        if (!$documentExists) {
            $this->setFlash('error', 'Le document demandé n\'existe pas');
            $this->redirect('publications/' . $id);
            return;
        }

        // Get the file path
        $filePath = 'uploads/publications/' . $filename;

        // Check if file exists
        if (!file_exists($filePath)) {
            $this->setFlash('error', 'Le fichier demandé n\'existe pas');
            $this->redirect('publications/' . $id);
            return;
        }

        // Set headers for download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        // Clear output buffer
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Read file
        readfile($filePath);
        exit;
    }

}