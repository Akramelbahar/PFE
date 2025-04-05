<?php
require_once './core/Controller.php';
require_once './models/projects/ProjetRecherche.php';
require_once './models/projects/Participe.php';
require_once './models/projects/Partner.php';
require_once './utils/FileManager.php';

/**
 * Project Controller
 * Manages research projects
 */
class ProjectController extends Controller {
    private $fileManager;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->fileManager = new FileManager('uploads/projects/');
    }

    /**
     * Projects index page
     */
    public function index() {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        // Get filter parameters
        $status = $this->getInput('status');
        // For database queries, we need to use the correct column name
        $statusColumnName = $this->getStatusColumnName();
        $chercheur = $this->getInput('chercheur');
        $year = $this->getInput('year');
        $search = $this->getInput('search');

        // Get projects with filters
        $projetModel = new ProjetRecherche();
        $projects = $this->getProjects($status, $chercheur, $year, $search);

        // Get available filters
        $filters = $this->getProjectFilters();

        $this->render('projects/index', [
            'pageTitle' => 'Projets de Recherche',
            'projects' => $projects,
            'filters' => $filters,
            'currentFilters' => [
                'status' => $status,
                'chercheur' => $chercheur,
                'year' => $year,
                'search' => $search
            ]
        ]);
    }

    /**
     * Get filtered projects
     * @param string|null $status
     * @param int|null $chercheur
     * @param string|null $year
     * @param string|null $search
     * @return array
     */
    private function getProjects($status = null, $chercheur = null, $year = null, $search = null) {
        $db = Db::getInstance();
        $statusColumnName = $this->getStatusColumnName(); // Get the proper column name

        $params = [];
        $conditions = [];

        $query = "
        SELECT p.*, u.nom as chefNom, u.prenom as chefPrenom
        FROM ProjetRecherche p
        LEFT JOIN Utilisateur u ON p.chefProjet = u.id
    ";

        // Add filters
        if ($status) {
            $conditions[] = "p.$statusColumnName = :status";
            $params['status'] = $status;
        }

        if ($chercheur) {
            $query .= " LEFT JOIN Participe part ON p.id = part.projetId";
            $conditions[] = "(p.chefProjet = :chercheur OR part.utilisateurId = :chercheur)";
            $params['chercheur'] = $chercheur;
        }

        if ($year) {
            $conditions[] = "YEAR(p.dateDebut) = :year";
            $params['year'] = $year;
        }

        if ($search) {
            $conditions[] = "(p.titre LIKE :search OR p.description LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        // Add WHERE clause if there are conditions
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        // Add GROUP BY to eliminate duplicates from the Participe join
        if ($chercheur) {
            $query .= " GROUP BY p.id";
        }

        // Add ORDER BY
        $query .= " ORDER BY p.dateDebut DESC";

        // For debugging
        error_log("Projects query: $query");
        error_log("Parameters: " . json_encode($params));

        $stmt = $db->prepare($query);

        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Get project filters
     * @return array
     */
    private function getProjectFilters() {
        $db = Db::getInstance();
        $filters = [];

        // Status options
        $filters['statuses'] = ['En préparation', 'En cours', 'Terminé', 'Suspendu'];

        // Researchers
        $stmt = $db->query("
            SELECT DISTINCT u.id, u.nom, u.prenom
            FROM Utilisateur u
            JOIN Chercheur c ON u.id = c.utilisateurId
            ORDER BY u.nom, u.prenom
        ");
        $filters['chercheurs'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Years
        $stmt = $db->query("
            SELECT DISTINCT YEAR(dateDebut) as year
            FROM ProjetRecherche
            ORDER BY year DESC
        ");
        $filters['years'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $filters;
    }

    /**
     * Determine the correct status column name in the database
     * @return string The correct column name ('status' or 'statut')
     */
    private function getStatusColumnName() {
        $db = Db::getInstance();

        try {
            // Check if 'statut' column exists
            $stmt = $db->query("SHOW COLUMNS FROM ProjetRecherche LIKE 'statut'");
            if ($stmt->rowCount() > 0) {
                return 'statut';
            }

            // Check if 'etat' column exists
            $stmt = $db->query("SHOW COLUMNS FROM ProjetRecherche LIKE 'etat'");
            if ($stmt->rowCount() > 0) {
                return 'etat';
            }

            // Default to 'status'
            return 'status';
        } catch (Exception $e) {
            error_log('Error checking status column name: ' . $e->getMessage());
            // Default to most common naming convention
            return 'statut';
        }
    }

    /**
     * View project details
     * @param int $id Project ID
     */
    /**
     * View project details
     * @param int $id Project ID
     */
    public function view($id) {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        // Get project with chef details
        $projetModel = new ProjetRecherche();
        $project = $projetModel->findWithChef($id);

        if (!$project) {
            $this->renderNotFound();
            return;
        }

        // Debug the project data
        error_log('Project data from database: ' . print_r($project, true));

        // Get project participants
        $participants = $projetModel->getParticipants($id);

        // Get project partners
        $partners = $projetModel->getPartners($id);

        // Get related publications
        $publications = [];
        if (class_exists('Publication')) {
            $publicationModel = new Publication();
            if (method_exists($publicationModel, 'getByProject')) {
                $publications = $publicationModel->getByProject($id);
            }
        }

        // Get related events
        $events = [];
        if (class_exists('Evenement')) {
            $evenementModel = new Evenement();
            if (method_exists($evenementModel, 'getByProject')) {
                $events = $evenementModel->getByProject($id);
            }
        }

        // Get project documents
        $documents = $this->fileManager->listFiles($id);

        // Make sure dateDebut is properly formatted
        if (isset($project['dateDebut'])) {
            // Ensure it's a valid date format
            $dateDebut = DateTime::createFromFormat('Y-m-d', $project['dateDebut']);
            if (!$dateDebut) {
                // Try other common formats
                $dateDebut = DateTime::createFromFormat('Y-m-d H:i:s', $project['dateDebut']);
            }

            if ($dateDebut) {
                $project['dateDebut'] = $dateDebut->format('Y-m-d');
            }
        } else {
            error_log('Project dateDebut is missing in database result');
            // Provide a fallback date to avoid errors in the view
            $project['dateDebut'] = date('Y-m-d');
        }

        // Format dateFin if it exists
        if (isset($project['dateFin']) && !empty($project['dateFin'])) {
            $dateFin = DateTime::createFromFormat('Y-m-d', $project['dateFin']);
            if (!$dateFin) {
                $dateFin = DateTime::createFromFormat('Y-m-d H:i:s', $project['dateFin']);
            }

            if ($dateFin) {
                $project['dateFin'] = $dateFin->format('Y-m-d');
            }
        }

        $this->render('projects/view', [
            'pageTitle' => $project['titre'],
            'project' => $project,
            'participants' => $participants,
            'partners' => $partners,
            'publications' => $publications,
            'events' => $events,
            'documents' => $documents
        ]);
    }
    /**
     * Create project form
     */
    public function create() {
        // Ensure user is authenticated and has permission
        if (!$this->requireAuth() || !$this->requirePermission('create_project')) {
            return;
        }

        // Get all researchers for selection
        $chercheurs = [];
        if (class_exists('Chercheur')) {
            $chercheurModel = new Chercheur();
            if (method_exists($chercheurModel, 'getAllWithUserDetails')) {
                $chercheurs = $chercheurModel->getAllWithUserDetails();
            }
        }

        // Get all partners for selection
        $partners = [];
        $partnerModel = new Partner();
        if (method_exists($partnerModel, 'all')) {
            $partners = $partnerModel->all();
        }

        // Provide a default empty project array
        $project = [
            'titre' => '',
            'description' => '',
            'dateDebut' => date('Y-m-d'), // Default to today
            'dateFin' => '',
            'status' => 'En cours',
            'chefProjet' => $this->auth->getUser()['id'], // Default to current user
            'budget' => ''
        ];

        $this->render('projects/create', [
            'pageTitle' => 'Créer un Projet',
            'chercheurs' => $chercheurs,
            'partners' => $partners,
            'project' => $project
        ]);
    }

    /**
     * Store new project
     */
    public function store() {
        // Ensure user is authenticated and has permission
        if (!$this->requireAuth() || !$this->requirePermission('create_project')) {
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('projects/create');
            return;
        }

        // Get form data
        $titre = trim($this->getInput('titre'));
        $description = trim($this->getInput('description'));
        $dateDebut = $this->getInput('dateDebut');
        $dateFin = $this->getInput('dateFin');
        $budget = $this->getInput('budget');
        $status = $this->getInput('status', 'En cours');
        $chefProjet = (int)$this->getInput('chefProjet');

        // Validate input
        $validation = $this->validate(
            [
                'titre' => $titre,
                'description' => $description,
                'dateDebut' => $dateDebut,
                'chefProjet' => $chefProjet
            ],
            [
                'titre' => 'required|max:255',
                'description' => 'required',
                'dateDebut' => 'required|date',
                'chefProjet' => 'required|numeric'
            ]
        );

        if ($validation !== true) {
            // For debugging
            error_log('Project validation failed: ' . print_r($validation, true));

            $this->render('projects/create', [
                'pageTitle' => 'Créer un Projet',
                'errors' => $validation,
                'project' => [
                    'titre' => $titre,
                    'description' => $description,
                    'dateDebut' => $dateDebut,
                    'dateFin' => $dateFin,
                    'budget' => $budget,
                    'status' => $status,
                    'chefProjet' => $chefProjet
                ],
                'chercheurs' => (new Chercheur())->getAllWithUserDetails(),
                'partners' => (new Partner())->all()
            ]);
            return;
        }

        // Get database instance
        $db = Db::getInstance();

        try {
            // Begin transaction
            $db->beginTransaction();

            // Prepare project data
            // Check if we should use 'status' or 'statut' for the status column name
            $statusColumnName = $this->getStatusColumnName();

            $projectData = [
                'titre' => $titre,
                'description' => $description,
                'dateDebut' => $dateDebut,
                $statusColumnName => $status,
                'chefProjet' => $chefProjet,
                'dateCreation' => date('Y-m-d H:i:s')
            ];

            // Add optional fields if they're not empty
            if (!empty($dateFin)) {
                $projectData['dateFin'] = $dateFin;
            }

            if (!empty($budget)) {
                $projectData['budget'] = $budget;
            }

            // For debugging
            error_log('Project data: ' . print_r($projectData, true));

            // Create project directly using the database connection
            $columns = implode(', ', array_keys($projectData));
            $placeholders = ':' . implode(', :', array_keys($projectData));

            $query = "INSERT INTO ProjetRecherche ({$columns}) VALUES ({$placeholders})";

            // For debugging
            error_log("SQL Query: $query");

            $stmt = $db->prepare($query);

            foreach ($projectData as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            $stmt->execute();

            // Get the project ID
            $projectId = $db->lastInsertId();

            if (!$projectId) {
                throw new Exception('Failed to get last insert ID');
            }

            // Add participants
            $participants = $this->getInput('participants', []);
            if (!empty($participants)) {
                foreach ($participants as $participantId) {
                    $participantId = (int)$participantId;
                    if ($participantId > 0) {
                        $participantStmt = $db->prepare("INSERT INTO Participe (projetId, utilisateurId, role) VALUES (:projetId, :utilisateurId, :role)");
                        $participantStmt->execute([
                            'projetId' => $projectId,
                            'utilisateurId' => $participantId,
                            'role' => 'participant'
                        ]);
                    }
                }
            }

            // Add partners
            $partners = $this->getInput('partners', []);
            if (!empty($partners)) {
                foreach ($partners as $partnerId) {
                    $partnerId = (int)$partnerId;
                    if ($partnerId > 0) {
                        $partnerStmt = $db->prepare("INSERT INTO ProjetPartner (projetId, partnerId) VALUES (:projetId, :partnerId)");
                        $partnerStmt->execute([
                            'projetId' => $projectId,
                            'partnerId' => $partnerId
                        ]);
                    }
                }
            }

            // Commit transaction
            $db->commit();

            // Handle document uploads
            $documents = $this->getFile('documents');
            if ($documents && !empty($documents['name'][0])) {
                $this->fileManager->uploadMultiple($documents, $projectId);
            }

            $this->setFlash('success', 'Le projet a été créé avec succès.');
            $this->redirect('projects/' . $projectId);

        } catch (Exception $e) {
            // Rollback transaction on error
            $db->rollBack();

            // Log error
            error_log('Project creation failed: ' . $e->getMessage());
            error_log($e->getTraceAsString());

            $this->setFlash('error', 'Une erreur est survenue lors de la création du projet: ' . $e->getMessage());

            $this->render('projects/create', [
                'pageTitle' => 'Créer un Projet',
                'errors' => ['database' => [$e->getMessage()]],
                'project' => [
                    'titre' => $titre,
                    'description' => $description,
                    'dateDebut' => $dateDebut,
                    'dateFin' => $dateFin,
                    'budget' => $budget,
                    'status' => $status,
                    'chefProjet' => $chefProjet
                ],
                'chercheurs' => (new Chercheur())->getAllWithUserDetails(),
                'partners' => (new Partner())->all()
            ]);
        }
    }

    /**
     * Edit project form
     * @param int $id Project ID
     */
    /**
     * Edit project form
     * @param int $id Project ID
     */
    public function edit($id) {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        // Get project
        $projetModel = new ProjetRecherche();
        $project = $projetModel->findWithChef($id);

        if (!$project) {
            $this->renderNotFound();
            return;
        }

        // Check if user is chef or has admin permission
        $isChef = $project['chefProjet'] == $this->auth->getUser()['id'];
        $canEdit = $isChef || $this->auth->hasPermission('edit_project');

        if (!$canEdit) {
            $this->renderForbidden();
            return;
        }

        // Get all researchers for selection
        $chercheurs = [];

        // Direct database query instead of using the Chercheur model
        $db = Db::getInstance();
        $stmt = $db->query("
        SELECT c.*, u.id as utilisateurId, u.nom, u.prenom, u.email
        FROM Chercheur c
        JOIN Utilisateur u ON c.utilisateurId = u.id
        ORDER BY u.nom, u.prenom
    ");
        $chercheurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get all partners for selection
        $partners = [];

        // Direct database query for partners
        $stmt = $db->query("
        SELECT * FROM Partner ORDER BY nom
    ");
        $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get current participants
        $participants = $projetModel->getParticipants($id);

        // Get current partners
        $projectPartners = $projetModel->getPartners($id);

        // Get project documents
        $documents = $this->fileManager->listFiles($id);

        $this->render('projects/edit', [
            'pageTitle' => 'Modifier le Projet: ' . $project['titre'],
            'project' => $project,
            'chercheurs' => $chercheurs,
            'partners' => $partners,
            'participants' => $participants,
            'projectPartners' => $projectPartners,
            'documents' => $documents
        ]);
    }
    /**
     * Update project
     * @param int $id Project ID
     */
    /**
     * Update project
     * @param int $id Project ID
     */
    public function update($id) {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        // Get project
        $projetModel = new ProjetRecherche();
        $project = $projetModel->find($id);

        if (!$project) {
            $this->renderNotFound();
            return;
        }

        // Check if user is chef or has admin permission
        $isChef = $project['chefProjet'] == $this->auth->getUser()['id'];
        $canEdit = $isChef || $this->auth->hasPermission('edit_project');

        if (!$canEdit) {
            $this->renderForbidden();
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('projects/edit/' . $id);
            return;
        }

        // Get form data
        $titre = trim($this->getInput('titre'));
        $description = trim($this->getInput('description'));
        $dateDebut = $this->getInput('dateDebut');
        $dateFin = $this->getInput('dateFin');
        $budget = $this->getInput('budget');
        $status = $this->getInput('status');
        $chefProjet = (int)$this->getInput('chefProjet');

        // Debug the date values
        error_log("dateDebut from form: " . $dateDebut);
        error_log("dateFin from form: " . $dateFin);

        // Validate input
        $validation = $this->validate(
            [
                'titre' => $titre,
                'description' => $description,
                'dateDebut' => $dateDebut,
                'chefProjet' => $chefProjet
            ],
            [
                'titre' => 'required|max:255',
                'description' => 'required',
                'dateDebut' => 'required|date',
                'chefProjet' => 'required|numeric'
            ]
        );

        if ($validation !== true) {
            // For debugging
            error_log('Project update validation failed: ' . print_r($validation, true));

            $this->setFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
            $this->redirect('projects/edit/' . $id);
            return;
        }

        // Get database instance
        $db = Db::getInstance();

        try {
            // Begin transaction
            $db->beginTransaction();

            // Prepare project data
            // Use the same status column name for consistency
            $statusColumnName = $this->getStatusColumnName();

            $projectData = [
                'titre' => $titre,
                'description' => $description,
                'dateDebut' => $dateDebut,
                $statusColumnName => $status,
                'chefProjet' => $chefProjet,
            ];

            // Add optional fields if they're not empty
            if (!empty($dateFin)) {
                $projectData['dateFin'] = $dateFin;
            } else {
                $projectData['dateFin'] = null;
            }

            if (!empty($budget)) {
                $projectData['budget'] = $budget;
            } else {
                $projectData['budget'] = null;
            }

            // For debugging
            error_log('Project update data: ' . print_r($projectData, true));

            // Update project directly using the database connection
            $updates = [];
            foreach ($projectData as $key => $value) {
                $updates[] = "{$key} = :{$key}";
            }

            $query = "UPDATE ProjetRecherche SET " . implode(', ', $updates) . " WHERE id = :id";

            // For debugging
            error_log("SQL Update Query: $query");

            $stmt = $db->prepare($query);

            foreach ($projectData as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->bindValue(':id', $id);

            $updated = $stmt->execute();

            if (!$updated) {
                throw new Exception('Failed to update project');
            }

            // Update participants
            $participantsToAdd = $this->getInput('participants', []);
            $currentParticipants = array_column($projetModel->getParticipants($id), 'utilisateurId');

            // Remove participants not in the new list
            foreach ($currentParticipants as $participantId) {
                if (!in_array($participantId, $participantsToAdd)) {
                    $stmt = $db->prepare("DELETE FROM Participe WHERE projetId = :projetId AND utilisateurId = :utilisateurId");
                    $stmt->execute([
                        'projetId' => $id,
                        'utilisateurId' => $participantId
                    ]);
                }
            }

            // Add new participants
            foreach ($participantsToAdd as $participantId) {
                $participantId = (int)$participantId;
                if ($participantId > 0 && !in_array($participantId, $currentParticipants)) {
                    $participantStmt = $db->prepare("INSERT INTO Participe (projetId, utilisateurId, role) VALUES (:projetId, :utilisateurId, :role)");
                    $participantStmt->execute([
                        'projetId' => $id,
                        'utilisateurId' => $participantId,
                        'role' => 'participant'
                    ]);
                }
            }

            // Update partners
            $partnersToAdd = $this->getInput('partners', []);
            $currentPartners = array_column($projetModel->getPartners($id), 'id');

            // Remove partners not in the new list
            foreach ($currentPartners as $partnerId) {
                if (!in_array($partnerId, $partnersToAdd)) {
                    $stmt = $db->prepare("DELETE FROM ProjetPartner WHERE projetId = :projetId AND partnerId = :partnerId");
                    $stmt->execute([
                        'projetId' => $id,
                        'partnerId' => $partnerId
                    ]);
                }
            }

            // Add new partners
            foreach ($partnersToAdd as $partnerId) {
                $partnerId = (int)$partnerId;
                if ($partnerId > 0 && !in_array($partnerId, $currentPartners)) {
                    $partnerStmt = $db->prepare("INSERT INTO ProjetPartner (projetId, partnerId) VALUES (:projetId, :partnerId)");
                    $partnerStmt->execute([
                        'projetId' => $id,
                        'partnerId' => $partnerId
                    ]);
                }
            }

            // Commit transaction
            $db->commit();

            // Handle document uploads
            $documents = $this->getFile('documents');
            if ($documents && !empty($documents['name'][0])) {
                $uploadedFiles = $this->processDocumentUploads($documents, $id);

                if (!empty($uploadedFiles)) {
                    error_log('Successfully uploaded ' . count($uploadedFiles) . ' documents');
                } else {
                    error_log('No documents were uploaded');
                }
            }

            $this->setFlash('success', 'Le projet a été mis à jour avec succès.');
            $this->redirect('projects/' . $id);

        } catch (Exception $e) {
            // Rollback transaction on error
            $db->rollBack();

            // Log error
            error_log('Project update failed: ' . $e->getMessage());
            error_log($e->getTraceAsString());

            $this->setFlash('error', 'Une erreur est survenue lors de la mise à jour du projet: ' . $e->getMessage());
            $this->redirect('projects/edit/' . $id);
        }
    }
    /**
     * Delete project
     * @param int $id Project ID
     */
    public function delete($id) {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        // Get project
        $projetModel = new ProjetRecherche();
        $project = $projetModel->find($id);

        if (!$project) {
            $this->renderNotFound();
            return;
        }

        // Check if user has permission to delete
        $isChef = $project['chefProjet'] == $this->auth->getUser()['id'];
        $canDelete = $isChef ?
            $this->auth->hasPermission('delete_own_project') :
            $this->auth->hasPermission('delete_project');

        if (!$canDelete) {
            $this->renderForbidden();
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('projects/' . $id);
            return;
        }

        // Get database instance
        $db = Db::getInstance();

        try {
            // Begin transaction
            $db->beginTransaction();

            // Delete participants
            $stmt = $db->prepare("DELETE FROM Participe WHERE projetId = :id");
            $stmt->execute(['id' => $id]);

            // Delete partners
            $stmt = $db->prepare("DELETE FROM ProjetPartner WHERE projetId = :id");
            $stmt->execute(['id' => $id]);

            // Delete the project
            $stmt = $db->prepare("DELETE FROM ProjetRecherche WHERE id = :id");
            $deleted = $stmt->execute(['id' => $id]);

            if (!$deleted) {
                throw new Exception('Failed to delete project');
            }

            // Commit transaction
            $db->commit();

            // Delete documents
            $documents = $this->fileManager->listFiles($id);
            foreach ($documents as $document) {
                $this->fileManager->delete($document['filename'], $id);
            }

            $this->setFlash('success', 'Le projet a été supprimé avec succès.');
            $this->redirect('projects');

        } catch (Exception $e) {
            // Rollback transaction on error
            $db->rollBack();

            // Log error
            error_log('Project deletion failed: ' . $e->getMessage());

            $this->setFlash('error', 'Une erreur est survenue lors de la suppression du projet: ' . $e->getMessage());
            $this->redirect('projects/' . $id);
        }
    }

    /**
     * Delete document
     * @param int $projectId Project ID
     * @param string $filename Filename to delete
     */

    /**
     * Download document
     * @param int $projectId Project ID
     * @param string $filename Filename to download
     */

    /**
     * Document handling methods for ProjectController
     */

    /**
     * Download document
     * @param int $projectId Project ID
     * @param string $filename Filename to download
     */
    public function downloadDocument($projectId, $filename)
    {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        // Validate project exists
        $projetModel = new ProjetRecherche();
        $project = $projetModel->find($projectId);

        if (!$project) {
            $this->renderNotFound();
            return;
        }

        // Validate filename - basic security check
        if (!preg_match('/^[a-zA-Z0-9_\-.]+\.[a-zA-Z0-9]+$/', $filename)) {
            $this->renderForbidden();
            return;
        }

        try {
            // Use the FileManager to handle the download
            if (!$this->fileManager->download($filename, $projectId)) {
                throw new Exception('Document not found or cannot be downloaded.');
            }
            // The download method will exit the script if successful
        } catch (Exception $e) {
            error_log('Document download failed: ' . $e->getMessage());
            $this->setFlash('error', 'Une erreur est survenue lors du téléchargement du document.');
            $this->redirect('projects/' . $projectId);
        }
    }

    /**
     * Delete document
     * @param int $projectId Project ID
     * @param string $filename Filename to delete
     */
    public function deleteDocument($projectId, $filename)
    {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        // Get project
        $projetModel = new ProjetRecherche();
        $project = $projetModel->find($projectId);

        if (!$project) {
            $this->renderNotFound();
            return;
        }

        // Check if user has permission to edit (needed for deleting files)
        $isChef = $project['chefProjet'] == $this->auth->getUser()['id'];
        $canEdit = $isChef || $this->auth->hasPermission('edit_project');

        if (!$canEdit) {
            $this->renderForbidden();
            return;
        }

        // Validate filename - basic security check
        if (!preg_match('/^[a-zA-Z0-9_\-.]+\.[a-zA-Z0-9]+$/', $filename)) {
            $this->setFlash('error', 'Nom de fichier invalide.');
            $this->redirect('projects/edit/' . $projectId);
            return;
        }

        try {
            // Use the FileManager to handle the deletion
            if (!$this->fileManager->delete($filename, $projectId)) {
                throw new Exception('Document not found or cannot be deleted.');
            }

            $this->setFlash('success', 'Le document a été supprimé avec succès.');
        } catch (Exception $e) {
            error_log('Document deletion failed: ' . $e->getMessage());
            $this->setFlash('error', 'Une erreur est survenue lors de la suppression du document.');
        }

        // Redirect back to the edit page
        $this->redirect('projects/edit/' . $projectId);
    }

    /**
     * Process document uploads for a project
     * @param array $files File uploads array
     * @param int $projectId Project ID
     * @return array Uploaded files information
     */
    private function processDocumentUploads($files, $projectId)
    {
        $uploadedFiles = [];

        if (!empty($files) && isset($files['name'][0]) && !empty($files['name'][0])) {
            try {
                $uploadedFiles = $this->fileManager->uploadMultiple($files, $projectId);

                if (empty($uploadedFiles)) {
                    error_log("No files were uploaded for project {$projectId}");
                } else {
                    error_log("Successfully uploaded " . count($uploadedFiles) . " files for project {$projectId}");
                }
            } catch (Exception $e) {
                error_log("Document upload error: " . $e->getMessage());
                $this->setFlash('warning', 'Certains documents n\'ont pas pu être téléchargés. Veuillez vérifier les formats et tailles des fichiers.');
            }
        }

        return $uploadedFiles;
    }
}