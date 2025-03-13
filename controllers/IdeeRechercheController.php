<?php
require_once './core/Controller.php';
require_once './models/IdeeRecherche.php';
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

        // Get domains for filtering (you might want to replace this with actual domains from your database)
        $domains = ['Informatique', 'Génie Civil', 'Génie Électrique', 'Management', 'Autre'];

        $this->render('ideas/index', [
            'pageTitle' => 'Idées de Recherche',
            'ideas' => $ideas,
            'domains' => $domains
        ]);
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

        $this->render('ideas/view', [
            'pageTitle' => $idea['titre'],
            'idea' => $idea
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

        // Get domains for dropdown (replace with actual domains if needed)
        $domains = ['Informatique', 'Génie Civil', 'Génie Électrique', 'Management', 'Autre'];

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

        // Validate input
        $validation = $this->validate(
            [
                'titre' => $titre,
                'description' => $description
            ],
            [
                'titre' => 'required|max:255',
                'description' => 'required'
            ]
        );

        if ($validation !== true) {
            $domains = ['Informatique', 'Génie Civil', 'Génie Électrique', 'Management', 'Autre'];

            $this->render('ideas/create', [
                'pageTitle' => 'Proposer une Idée de Recherche',
                'errors' => $validation,
                'idea' => [
                    'titre' => $titre,
                    'description' => $description
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
            'proposePar' => $this->auth->getUser()['id'],
            'dateProposition' => date('Y-m-d H:i:s'),
            'status' => 'en attente'
        ]);

        if (!$ideaId) {
            $this->setFlash('error', 'Une erreur est survenue lors de la soumission de votre idée.');
            $this->redirect('ideas/create');
            return;
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

        // Get domains for dropdown
        $domains = ['Informatique', 'Génie Civil', 'Génie Électrique', 'Management', 'Autre'];

        $this->render('ideas/edit', [
            'pageTitle' => 'Modifier l\'idée: ' . $idea['titre'],
            'idea' => $idea,
            'domains' => $domains
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

        if (!$this->isPost()) {
            $this->redirect('ideas/edit/' . $id);
            return;
        }

        // Get form data
        $titre = $this->getInput('titre');
        $description = $this->getInput('description');

        // Validate input
        $validation = $this->validate(
            [
                'titre' => $titre,
                'description' => $description
            ],
            [
                'titre' => 'required|max:255',
                'description' => 'required'
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
            'description' => $description
        ]);

        if (!$updated) {
            $this->setFlash('error', 'Une erreur est survenue lors de la mise à jour de l\'idée.');
            $this->redirect('ideas/edit/' . $id);
            return;
        }

        $this->setFlash('success', 'L\'idée a été mise à jour avec succès.');
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
        $canDelete = $isOwner || $this->auth->hasRole(['admin', 'membreBureauExecutif']);

        if (!$canDelete) {
            $this->renderForbidden();
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('ideas/' . $id);
            return;
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
     * Update idea status (admin or board members only)
     * @param int $id Idea ID
     */
    public function updateStatus($id) {
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

        if (!$this->isPost()) {
            $this->redirect('ideas/' . $id);
            return;
        }

        // Get new status
        $status = $this->getInput('status');

        // Validate status
        $validStatuses = ['en attente', 'approuvée', 'refusé'];
        if (!in_array($status, $validStatuses)) {
            $this->setFlash('error', 'Statut invalide.');
            $this->redirect('ideas/' . $id);
            return;
        }

        // Update idea status
        $updated = $ideeModel->update($id, [
            'status' => $status
        ]);

        if ($updated) {
            $this->setFlash('success', 'Le statut de l\'idée a été mis à jour avec succès.');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de la mise à jour du statut.');
        }

        $this->redirect('ideas/' . $id);
    }
}