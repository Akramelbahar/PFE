<?php
require_once './core/Controller.php';
require_once './models/projects/ProjetRecherche.php';
require_once './models/Participe.php';
require_once './models/Partner.php';
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

        $params = [];
        $conditions = [];

        $query = "
            SELECT p.*, u.nom as chefNom, u.prenom as chefPrenom
            FROM ProjetRecherche p
            LEFT JOIN Utilisateur u ON p.chefProjet = u.id
        ";

        // Add filters
        if ($status) {
            $conditions[] = "p.status = :status";
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

        // Get project participants
        $participants = $projetModel->getParticipants($id);

        // Get project partners
        $partners = $projetModel->getPartners($id);

        // Get related publications
        $publicationModel = new Publication();
        $publications = $publicationModel->getByProject($id);

        // Get related events
        $evenementModel = new Evenement();
        $events = $evenementModel->getByProject($id);

        // Get project documents
        $documents = $this->fileManager->listFiles($id);

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
        $chercheurModel = new Chercheur();
        $chercheurs = $chercheurModel->getAllWithUserDetails();

        // Get all partners for selection
        $partnerModel = new Partner();
        $partners = $partnerModel->all();

        $this->render('projects/create', [
            'pageTitle' => 'Créer un Projet',
            'chercheurs' => $chercheurs,
            'partners' => $partners
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
        $titre = $this->getInput('titre');
        $description = $this->getInput('description');
        $dateDebut = $this->getInput('dateDebut');
        $dateFin = $this->getInput('dateFin');
        $budget = $this->getInput('budget');
        $status = $this->getInput('status');
        $chefProjet = $this->getInput('chefProjet');

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
                ]
            ]);
            return;
        }

        // Create project
        $projetModel = new ProjetRecherche();
        $projectId = $projetModel->create([
            'titre' => $titre,
            'description' => $description,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin ?: null,
            'budget' => $budget ?: null,
            'status' => $status ?: 'En cours',
            'chefProjet' => $chefProjet,
            'dateCreation' => date('Y-m-d H:i:s')
        ]);

        if (!$projectId) {
            $this->setFlash('error', 'Une erreur est survenue lors de la création du projet.');
            $this->redirect('projects/create');
            return;
        }

        // Add participants
        $participants = $this->getInput('participants', []);
        if (!empty($participants)) {
            foreach ($participants as $participantId) {
                $projetModel->addParticipant($projectId, $participantId);
            }
        }

        // Add partners
        $partners = $this->getInput('partners', []);
        if (!empty($partners)) {
            foreach ($partners as $partnerId) {
                $projetModel->addPartner($projectId, $partnerId);
            }
        }

        // Handle document uploads
        $documents = $this->getFile('documents');
        if ($documents && !empty($documents['name'][0])) {
            $this->fileManager->uploadMultiple($documents, $projectId);
        }

        $this->setFlash('success', 'Le projet a été créé avec succès.');
        $this->redirect('projects/' . $projectId);
    }

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
        $chercheurModel = new Chercheur();
        $chercheurs = $chercheurModel->getAllWithUserDetails();

        // Get all partners for selection
        $partnerModel = new Partner();
        $partners = $partnerModel->all();

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
        $titre = $this->getInput('titre');
        $description = $this->getInput('description');
        $dateDebut = $this->getInput('dateDebut');
        $dateFin = $this->getInput('dateFin');
        $budget = $this->getInput('budget');
        $status = $this->getInput('status');
        $chefProjet = $this->getInput('chefProjet');

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
            $this->setFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
            $this->redirect('projects/edit/' . $id);
            return;
        }

        // Update project
        $updated = $projetModel->update($id, [
            'titre' => $titre,
            'description' => $description,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin ?: null,
            'budget' => $budget ?: null,
            'status' => $status,
            'chefProjet' => $chefProjet
        ]);

        if (!$updated) {
            $this->setFlash('error', 'Une erreur est survenue lors de la mise à jour du projet.');
            $this->redirect('projects/edit/' . $id);
            return;
        }

        // Update participants
        $participantsToAdd = $this->getInput('participants', []);
        $currentParticipants = array_column($projetModel->getParticipants($id), 'utilisateurId');

        // Remove participants not in the new list
        $participeModel = new Participe();
        foreach ($currentParticipants as $participantId) {
            if (!in_array($participantId, $participantsToAdd)) {
                $participeModel->removeParticipant($id, $participantId);
            }
        }

        // Add new participants
        foreach ($participantsToAdd as $participantId) {
            if (!in_array($participantId, $currentParticipants)) {
                $projetModel->addParticipant($id, $participantId);
            }
        }

        // Update partners
        $partnersToAdd = $this->getInput('partners', []);
        $currentPartners = array_column($projetModel->getPartners($id), 'id');

        // Remove partners not in the new list
        $partnerModel = new Partner();
        foreach ($currentPartners as $partnerId) {
            if (!in_array($partnerId, $partnersToAdd)) {
                $partnerModel->removeFromProject($partnerId, $id);
            }
        }

        // Add new partners
        foreach ($partnersToAdd as $partnerId) {
            if (!in_array($partnerId, $currentPartners)) {
                $projetModel->addPartner($id, $partnerId);
            }
        }

        // Handle document uploads
        $documents = $this->getFile('documents');
        if ($documents && !empty($documents['name'][0])) {
            $this->fileManager->uploadMultiple($documents, $id);
        }

        $this->setFlash('success', 'Le projet a été mis à jour avec succès.');
        $this->redirect('projects/' . $id);
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

        // First delete associated records

        // Delete participants
        $db = Db::getInstance();
        $stmt = $db->prepare("DELETE FROM Participe WHERE projetId = :id");
        $stmt->execute(['id' => $id]);

        // Delete partners
        $stmt = $db->prepare("DELETE FROM ProjetPartner WHERE projetId = :id");
        $stmt->execute(['id' => $id]);

        // Delete documents
        $documents = $this->fileManager->listFiles($id);
        foreach ($documents as $document) {
            $this->fileManager->delete($document['filename'], $id);
        }

        // Finally delete the project
        $deleted = $projetModel->delete($id);

        if ($deleted) {
            $this->setFlash('success', 'Le projet a été supprimé avec succès.');
            $this->redirect('projects');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de la suppression du projet.');
            $this->redirect('projects/' . $id);
        }
    }

    /**
     * Delete document
     * @param int $projectId Project ID
     * @param string $filename Filename to delete
     */
    public function deleteDocument($projectId, $filename) {
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

        // Check if user has permission to edit
        $isChef = $project['chefProjet'] == $this->auth->getUser()['id'];
        $canEdit = $isChef || $this->auth->hasPermission('edit_project');

        if (!$canEdit) {
            $this->renderForbidden();
            return;
        }

        if ($this->fileManager->delete($filename, $projectId)) {
            $this->setFlash('success', 'Le document a été supprimé avec succès.');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de la suppression du document.');
        }

        $this->redirect('projects/edit/' . $projectId);
    }
}