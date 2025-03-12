<?php
require_once './core/Controller.php';
require_once './models/IdeeRecherche.php';
require_once './models/projects/ProjetRecherche.php';
require_once './utils/FileManager.php';

/**
 * IdeeRecherche Controller
 * Manages research ideas
 */
class IdeeRechercheController extends Controller {
    private $fileManager;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->fileManager = new FileManager('uploads/ideas/');
    }

    /**
     * Ideas index page
     */
    public function index() {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        $ideeModel = new IdeeRecherche();

        // Admin and executive board members see all ideas
        if ($this->auth->hasRole('admin') || $this->auth->hasRole('membreBureauExecutif')) {
            $ideas = $ideeModel->getAllWithProposerDetails();
        } else {
            // Regular users see only their own ideas
            $ideas = $ideeModel->getByProposer($this->auth->getUser()['id']);
        }

        // Get filter parameters
        $status = $this->getInput('status');
        $domaine = $this->getInput('domaine');
        $search = $this->getInput('search');

        // Apply filters if needed
        if ($status || $domaine || $search) {
            $ideas = $this->filterIdeas($ideas, $status, $domaine, $search);
        }

        // Get all domains for filter
        $domains = $this->getAllDomains();

        $this->render('ideas/index', [
            'pageTitle' => 'Idées de Recherche',
            'ideas' => $ideas,
            'domains' => $domains,
            'filters' => [
                'status' => $status,
                'domaine' => $domaine,
                'search' => $search
            ]
        ]);
    }

    /**
     * Filter ideas based on criteria
     * @param array $ideas
     * @param string|null $status
     * @param string|null $domaine
     * @param string|null $search
     * @return array
     */
    private function filterIdeas($ideas, $status = null, $domaine = null, $search = null) {
        $filtered = [];

        foreach ($ideas as $idea) {
            // Status filter
            if ($status && $idea['status'] !== $status) {
                continue;
            }

            // Domain filter
            if ($domaine && $idea['domaine'] !== $domaine) {
                continue;
            }

            // Search filter
            if ($search) {
                $searchInFields = [
                    $idea['titre'],
                    $idea['description'],
                    $idea['objectifs'],
                    $idea['benefices'],
                    $idea['proposerNom'] . ' ' . $idea['proposerPrenom']
                ];

                $found = false;
                foreach ($searchInFields as $field) {
                    if (stripos($field, $search) !== false) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    continue;
                }
            }

            $filtered[] = $idea;
        }

        return $filtered;
    }

    /**
     * Get all domains from existing ideas
     * @return array
     */
    private function getAllDomains() {
        $db = Db::getInstance();
        $stmt = $db->query("SELECT DISTINCT domaine FROM IdeeRecherche WHERE domaine IS NOT NULL ORDER BY domaine");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * View idea details
     * @param int $id Idea ID
     */
    public function view($id) {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        $ideeModel = new IdeeRecherche();
        $idea = $ideeModel->findWithProposer($id);

        if (!$idea) {
            $this->renderNotFound();
            return;
        }

        // Check if user can view this idea
        $isOwner = $idea['proposePar'] == $this->auth->getUser()['id'];
        $canView = $isOwner || $this->auth->hasRole(['admin', 'membreBureauExecutif']);

        if (!$canView) {
            $this->renderForbidden();
            return;
        }

        // Get documents associated with this idea
        $documents = $this->fileManager->listFiles($id);

        // Get projects created from this idea
        $projetModel = new ProjetRecherche();
        $projects = [];

        if (!empty($idea['projetId'])) {
            $projects[] = $projetModel->findWithChef($idea['projetId']);
        }

        $this->render('ideas/view', [
            'pageTitle' => $idea['titre'],
            'idea' => $idea,
            'documents' => $documents,
            'projects' => $projects
        ]);
    }

    /**
     * Create idea form
     */
    public function create() {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        // Get domains for dropdown
        $domains = $this->getAllDomains();

        $this->render('ideas/create', [
            'pageTitle' => 'Proposer une Idée de Recherche',
            'domains' => $domains
        ]);
    }

    /**
     * Store idea
     */
    public function store() {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('ideas/create');
            return;
        }

        // Get form data
        $titre = $this->getInput('titre');
        $description = $this->getInput('description');
        $domaine = $this->getInput('domaine');
        $objectifs = $this->getInput('objectifs');
        $benefices = $this->getInput('benefices');
        $ressources = $this->getInput('ressources');

        // Validate input
        $validation = $this->validate(
            [
                'titre' => $titre,
                'description' => $description,
                'domaine' => $domaine
            ],
            [
                'titre' => 'required|max:255',
                'description' => 'required',
                'domaine' => 'required'
            ]
        );

        if ($validation !== true) {
            $domains = $this->getAllDomains();

            $this->render('ideas/create', [
                'pageTitle' => 'Proposer une Idée de Recherche',
                'errors' => $validation,
                'idea' => [
                    'titre' => $titre,
                    'description' => $description,
                    'domaine' => $domaine,
                    'objectifs' => $objectifs,
                    'benefices' => $benefices,
                    'ressourcesNecessaires' => $ressources
                ],
                'domains' => $domains
            ]);
            return;
        }

        // Create idea
        $ideeModel = new IdeeRecherche();
        $ideaId = $ideeModel->create([
            'titre' => $titre,
            'description' => $description,
            'domaine' => $domaine,
            'objectifs' => $objectifs,
            'benefices' => $benefices,
            'ressourcesNecessaires' => $ressources,
            'proposePar' => $this->auth->getUser()['id'],
            'dateProposition' => date('Y-m-d H:i:s'),
            'status' => 'Soumise'
        ]);

        if (!$ideaId) {
            $this->setFlash('error', 'Une erreur est survenue lors de la soumission de votre idée.');
            $this->redirect('ideas/create');
            return;
        }

        // Handle document uploads
        $documents = $this->getFile('documents');
        if ($documents && !empty($documents['name'][0])) {
            $this->fileManager->uploadMultiple($documents, $ideaId);
        }

        $this->setFlash('success', 'Votre idée de recherche a été soumise avec succès.');
        $this->redirect('ideas/' . $ideaId);
    }

    /**
     * Edit idea form
     * @param int $id Idea ID
     */
    public function edit($id) {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        $ideeModel = new IdeeRecherche();
        $idea = $ideeModel->find($id);

        if (!$idea) {
            $this->renderNotFound();
            return;
        }

        // Check if user can edit this idea
        $isOwner = $idea['proposePar'] == $this->auth->getUser()['id'];
        $canEdit = $isOwner || $this->auth->hasRole(['admin', 'membreBureauExecutif']);

        if (!$canEdit) {
            $this->renderForbidden();
            return;
        }

        // Check if idea is not already approved/rejected
        if (in_array($idea['status'], ['Approuvée', 'Rejetée']) && !$this->auth->hasRole(['admin', 'membreBureauExecutif'])) {
            $this->setFlash('error', 'Vous ne pouvez pas modifier une idée qui a déjà été ' . strtolower($idea['status']) . '.');
            $this->redirect('ideas/' . $id);
            return;
        }

        // Get domains for dropdown
        $domains = $this->getAllDomains();

        // Get documents
        $documents = $this->fileManager->listFiles($id);

        $this->render('ideas/edit', [
            'pageTitle' => 'Modifier l\'idée: ' . $idea['titre'],
            'idea' => $idea,
            'domains' => $domains,
            'documents' => $documents
        ]);
    }

    /**
     * Update idea
     * @param int $id Idea ID
     */
    public function update($id) {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        $ideeModel = new IdeeRecherche();
        $idea = $ideeModel->find($id);

        if (!$idea) {
            $this->renderNotFound();
            return;
        }

        // Check if user can edit this idea
        $isOwner = $idea['proposePar'] == $this->auth->getUser()['id'];
        $canEdit = $isOwner || $this->auth->hasRole(['admin', 'membreBureauExecutif']);

        if (!$canEdit) {
            $this->renderForbidden();
            return;
        }

        // Check if idea is not already approved/rejected (for non-admins)
        if (in_array($idea['status'], ['Approuvée', 'Rejetée']) && !$this->auth->hasRole(['admin', 'membreBureauExecutif'])) {
            $this->setFlash('error', 'Vous ne pouvez pas modifier une idée qui a déjà été ' . strtolower($idea['status']) . '.');
            $this->redirect('ideas/' . $id);
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('ideas/edit/' . $id);
            return;
        }

        // Get form data
        $titre = $this->getInput('titre');
        $description = $this->getInput('description');
        $domaine = $this->getInput('domaine');
        $objectifs = $this->getInput('objectifs');
        $benefices = $this->getInput('benefices');
        $ressources = $this->getInput('ressources');

        // Validate input
        $validation = $this->validate(
            [
                'titre' => $titre,
                'description' => $description,
                'domaine' => $domaine
            ],
            [
                'titre' => 'required|max:255',
                'description' => 'required',
                'domaine' => 'required'
            ]
        );

        if ($validation !== true) {
            $this->setFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
            $this->redirect('ideas/edit/' . $id);
            return;
        }

        // Update idea
        $updated = $ideeModel->update($id, [
            'titre' => $titre,
            'description' => $description,
            'domaine' => $domaine,
            'objectifs' => $objectifs,
            'benefices' => $benefices,
            'ressourcesNecessaires' => $ressources
        ]);

        if (!$updated) {
            $this->setFlash('error', 'Une erreur est survenue lors de la mise à jour de l\'idée.');
            $this->redirect('ideas/edit/' . $id);
            return;
        }

        // Handle document uploads
        $documents = $this->getFile('documents');
        if ($documents && !empty($documents['name'][0])) {
            $this->fileManager->uploadMultiple($documents, $id);
        }

        $this->setFlash('success', 'L\'idée a été mise à jour avec succès.');
        $this->redirect('ideas/' . $id);
    }

    /**
     * Update idea status (admin or board members only)
     * @param int $id Idea ID
     */
    public function updateStatus($id) {
        // Ensure user has right permissions
        if (!$this->requirePermission('approve_idea')) {
            return;
        }

        $ideeModel = new IdeeRecherche();
        $idea = $ideeModel->find($id);

        if (!$idea) {
            $this->renderNotFound();
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('ideas/' . $id);
            return;
        }

        // Get new status
        $status = $this->getInput('status');
        $commentaire = $this->getInput('commentaire');

        // Validate status
        $validStatuses = ['Soumise', 'En évaluation', 'Approuvée', 'Rejetée'];
        if (!in_array($status, $validStatuses)) {
            $this->setFlash('error', 'Statut invalide.');
            $this->redirect('ideas/' . $id);
            return;
        }

        // Update idea status
        $updated = $ideeModel->update($id, [
            'status' => $status,
            'commentaire' => $commentaire,
            'evaluateurId' => $this->auth->getUser()['id'],
            'dateEvaluation' => date('Y-m-d H:i:s')
        ]);

        if ($updated) {
            // Send notification to the idea proposer
            if ($status === 'Approuvée' || $status === 'Rejetée') {
                $this->notifyProposer($idea, $status, $commentaire);
            }

            $this->setFlash('success', 'Le statut de l\'idée a été mis à jour avec succès.');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de la mise à jour du statut.');
        }

        $this->redirect('ideas/' . $id);
    }

    /**
     * Delete idea
     * @param int $id Idea ID
     */
    public function delete($id) {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        $ideeModel = new IdeeRecherche();
        $idea = $ideeModel->find($id);

        if (!$idea) {
            $this->renderNotFound();
            return;
        }

        // Check if user can delete this idea
        $isOwner = $idea['proposePar'] == $this->auth->getUser()['id'];
        $canDelete = $isOwner ?
            $this->auth->hasPermission('delete_own_idea') :
            $this->auth->hasPermission('delete_idea');

        if (!$canDelete) {
            $this->renderForbidden();
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('ideas/' . $id);
            return;
        }

        // Delete documents
        $documents = $this->fileManager->listFiles($id);
        foreach ($documents as $document) {
            $this->fileManager->delete($document['filename'], $id);
        }

        // Delete idea
        $deleted = $ideeModel->delete($id);

        if ($deleted) {
            $this->setFlash('success', 'L\'idée a été supprimée avec succès.');
            $this->redirect('ideas');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de la suppression de l\'idée.');
            $this->redirect('ideas/' . $id);
        }
    }

    /**
     * Delete document
     * @param int $ideaId Idea ID
     * @param string $filename Filename to delete
     */
    public function deleteDocument($ideaId, $filename) {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        $ideeModel = new IdeeRecherche();
        $idea = $ideeModel->find($ideaId);

        if (!$idea) {
            $this->renderNotFound();
            return;
        }

        // Check if user can delete document
        $isOwner = $idea['proposePar'] == $this->auth->getUser()['id'];
        $canEdit = $isOwner || $this->auth->hasRole(['admin', 'membreBureauExecutif']);

        if (!$canEdit) {
            $this->renderForbidden();
            return;
        }

        if ($this->fileManager->delete($filename, $ideaId)) {
            $this->setFlash('success', 'Le document a été supprimé avec succès.');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de la suppression du document.');
        }

        $this->redirect('ideas/edit/' . $ideaId);
    }

    /**
     * Create project from idea (admin or board members only)
     * @param int $id Idea ID
     */
    public function createProject($id) {
        // Ensure user has right permissions
        if (!$this->requireAuth(['admin', 'membreBureauExecutif'])) {
            return;
        }

        $ideeModel = new IdeeRecherche();
        $idea = $ideeModel->findWithProposer($id);

        if (!$idea) {
            $this->renderNotFound();
            return;
        }

        // Check if idea is approved
        if ($idea['status'] !== 'Approuvée') {
            $this->setFlash('error', 'Vous ne pouvez créer un projet qu\'à partir d\'une idée approuvée.');
            $this->redirect('ideas/' . $id);
            return;
        }

        // Check if project already exists
        if (!empty($idea['projetId'])) {
            $this->setFlash('info', 'Un projet existe déjà pour cette idée.');
            $this->redirect('projects/' . $idea['projetId']);
            return;
        }

        // Get all researchers for selection
        $chercheurModel = new Chercheur();
        $chercheurs = $chercheurModel->getAllWithUserDetails();

        // Pre-fill form with idea details
        $projetData = [
            'titre' => $idea['titre'],
            'description' => $idea['description'],
            'chefProjet' => $idea['proposePar'], // Default to idea proposer
            'ressourcesNecessaires' => $idea['ressourcesNecessaires'],
            'objectifs' => $idea['objectifs']
        ];

        $this->render('ideas/create_project', [
            'pageTitle' => 'Créer un Projet à partir de l\'idée: ' . $idea['titre'],
            'idea' => $idea,
            'projetData' => $projetData,
            'chercheurs' => $chercheurs
        ]);
    }

    /**
     * Store project from idea
     * @param int $id Idea ID
     */
    public function storeProject($id) {
        // Ensure user has right permissions
        if (!$this->requireAuth(['admin', 'membreBureauExecutif'])) {
            return;
        }

        $ideeModel = new IdeeRecherche();
        $idea = $ideeModel->find($id);

        if (!$idea) {
            $this->renderNotFound();
            return;
        }

        // Check if idea is approved
        if ($idea['status'] !== 'Approuvée') {
            $this->setFlash('error', 'Vous ne pouvez créer un projet qu\'à partir d\'une idée approuvée.');
            $this->redirect('ideas/' . $id);
            return;
        }

        // Check if project already exists
        if (!empty($idea['projetId'])) {
            $this->setFlash('info', 'Un projet existe déjà pour cette idée.');
            $this->redirect('projects/' . $idea['projetId']);
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('ideas/create-project/' . $id);
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
            $this->redirect('ideas/create-project/' . $id);
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
            $this->redirect('ideas/create-project/' . $id);
            return;
        }

        // Add participants
        $participants = $this->getInput('participants', []);
        if (!empty($participants)) {
            foreach ($participants as $participantId) {
                $projetModel->addParticipant($projectId, $participantId);
            }
        }

        // Add idea proposer as participant if not already the chef
        if ($idea['proposePar'] != $chefProjet && !in_array($idea['proposePar'], $participants)) {
            $projetModel->addParticipant($projectId, $idea['proposePar']);
        }

        // Link project to idea
        $ideeModel->update($id, ['projetId' => $projectId]);

        // Copy documents from idea to project
        $documents = $this->fileManager->listFiles($id);
        $projectFileManager = new FileManager('uploads/projects/');

        foreach ($documents as $document) {
            $sourcePath = 'uploads/ideas/' . $id . '/' . $document['filename'];
            $targetDir = 'uploads/projects/' . $projectId;

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $targetPath = $targetDir . '/' . $document['filename'];

            if (file_exists($sourcePath)) {
                copy($sourcePath, $targetPath);
            }
        }

        $this->setFlash('success', 'Le projet a été créé avec succès à partir de l\'idée.');
        $this->redirect('projects/' . $projectId);
    }


    /**
     * Send notification to idea proposer
     * @param array $idea Idea data
     * @param string $status New status
     * @param string $commentaire Evaluation comment
     */
    private function notifyProposer($idea, $status, $commentaire) {
        // Get proposer details
        $userModel = new Utilisateur();
        $proposer = $userModel->find($idea['proposePar']);

        if (!$proposer) {
            return;
        }

        // In a real application, send email notification
        // This is just a placeholder
        $subject = 'Mise à jour de votre idée de recherche';
        $message = "Bonjour " . $proposer['prenom'] . ",\n\n";
        $message .= "Votre idée de recherche \"" . $idea['titre'] . "\" a été " . strtolower($status) . ".\n\n";

        if (!empty($commentaire)) {
            $message .= "Commentaire de l'évaluateur : " . $commentaire . "\n\n";
        }

        $message .= "Vous pouvez consulter les détails sur la plateforme.\n\n";
        $message .= "Cordialement,\nAssociation Recherche et Innovation";

        // mail($proposer['email'], $subject, $message);
        }
}