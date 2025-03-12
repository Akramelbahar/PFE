<?php
require_once './controllers/Controller.php';
require_once './models/events/Evenement.php';
require_once './models/events/Conference.php';
require_once './models/events/Seminaire.php';
require_once './models/events/Workshop.php';
require_once './models/Utilisateur.php';
require_once './utils/FileManager.php';

/**
 * EvenementController - Manages all event types for the research association app
 */
class EvenementController extends Controller {
    protected $evenementModel;
    protected $conferenceModel;
    protected $seminaireModel;
    protected $workshopModel;
    protected $utilisateurModel;
    protected $fileManager;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->evenementModel = new Evenement();
        $this->conferenceModel = new Conference();
        $this->seminaireModel = new Seminaire();
        $this->workshopModel = new Workshop();
        $this->utilisateurModel = new Utilisateur();
        $this->fileManager = new FileManager('uploads/evenements/');
    }

    /**
     * Display events dashboard
     */
    public function index() {
        // Require authentication for accessing events
        if (!$this->requireAuth()) {
            return;
        }

        // Get all events with their types
        $events = $this->evenementModel->getAllWithTypes();

        $this->render('evenements/index', [
            'events' => $events,
            'pageTitle' => 'Tous les événements'
        ]);
    }

    /**
     * Display event details
     * @param int $id Event ID
     */
    public function view($id) {
        if (!$this->requireAuth()) {
            return;
        }

        $event = $this->evenementModel->findWithCreator($id);

        if (!$event) {
            $this->renderNotFound();
            return;
        }

        // Determine event type and get specific details
        $eventType = $this->getEventType($id);
        $specificDetails = [];

        switch ($eventType) {
            case 'Conference':
                $conference = $this->conferenceModel->find($id);
                $specificDetails = $conference;
                break;

            case 'Seminaire':
                $seminaire = $this->seminaireModel->find($id);
                $specificDetails = $seminaire;
                break;

            case 'Workshop':
                $workshop = $this->workshopModel->find($id);
                if (isset($workshop['instructorId'])) {
                    $instructor = $this->utilisateurModel->find($workshop['instructorId']);
                    $workshop['instructorName'] = $instructor ? $instructor['prenom'] . ' ' . $instructor['nom'] : 'Non assigné';
                }
                $specificDetails = $workshop;
                break;
        }

        // Get event documents
        $documents = $this->fileManager->listFiles($id);

        $this->render('evenements/view', [
            'event' => $event,
            'eventType' => $eventType,
            'specificDetails' => $specificDetails,
            'documents' => $documents,
            'pageTitle' => $event['titre']
        ]);
    }

    /**
     * Display event creation form
     */
    public function create() {
        if (!$this->requireAuth()) {
            return;
        }

        // Get all researchers for selection
        $chercheurs = $this->utilisateurModel->getAllWithRoles();

        $this->render('evenements/create', [
            'chercheurs' => $chercheurs,
            'pageTitle' => 'Créer un nouvel événement'
        ]);
    }

    /**
     * Process event creation
     */
    public function store() {
        if (!$this->requireAuth() || !$this->isPost()) {
            return;
        }

        // Get form data
        $data = [
            'titre' => $this->getInput('titre'),
            'description' => $this->getInput('description'),
            'projetId' => $this->getInput('projetId'),
            'createurId' => $this->auth->getUserId(),
            'lieu' => $this->getInput('lieu'),
            'type' => $this->getInput('type')
        ];

        // Validate base event data
        $rules = [
            'titre' => 'required|min:3|max:255',
            'description' => 'required',
            'type' => 'required'
        ];

        $validation = $this->validate($data, $rules);

        if ($validation !== true) {
            $this->render('evenements/create', [
                'errors' => $validation,
                'data' => $data,
                'pageTitle' => 'Créer un nouvel événement'
            ]);
            return;
        }

        // Create base event
        $eventId = $this->evenementModel->create($data);

        if (!$eventId) {
            $this->setFlash('error', 'Erreur lors de la création de l\'événement');
            $this->redirect('evenements/create');
            return;
        }

        // Create specific event type
        switch ($data['type']) {
            case 'Conference':
                $dateDebut = $this->getInput('dateDebut');
                $dateFin = $this->getInput('dateFin');

                $specificRules = [
                    'dateDebut' => 'required|date',
                    'dateFin' => 'required|date'
                ];

                $specificValidation = $this->validate([
                    'dateDebut' => $dateDebut,
                    'dateFin' => $dateFin
                ], $specificRules);

                if ($specificValidation !== true) {
                    $this->evenementModel->delete($eventId);
                    $this->render('evenements/create', [
                        'errors' => $specificValidation,
                        'data' => $data,
                        'pageTitle' => 'Créer un nouvel événement'
                    ]);
                    return;
                }

                $this->conferenceModel->createFromEvent($eventId, $dateDebut, $dateFin);
                break;

            case 'Seminaire':
                $date = $this->getInput('date');

                $specificRules = [
                    'date' => 'required|date'
                ];

                $specificValidation = $this->validate([
                    'date' => $date
                ], $specificRules);

                if ($specificValidation !== true) {
                    $this->evenementModel->delete($eventId);
                    $this->render('evenements/create', [
                        'errors' => $specificValidation,
                        'data' => $data,
                        'pageTitle' => 'Créer un nouvel événement'
                    ]);
                    return;
                }

                $this->seminaireModel->createFromEvent($eventId, $date);
                break;

            case 'Workshop':
                $instructorId = $this->getInput('instructorId');
                $dateDebut = $this->getInput('dateDebut');
                $dateFin = $this->getInput('dateFin');

                $specificRules = [
                    'dateDebut' => 'required|date',
                    'dateFin' => 'required|date'
                ];

                $specificValidation = $this->validate([
                    'dateDebut' => $dateDebut,
                    'dateFin' => $dateFin
                ], $specificRules);

                if ($specificValidation !== true) {
                    $this->evenementModel->delete($eventId);
                    $this->render('evenements/create', [
                        'errors' => $specificValidation,
                        'data' => $data,
                        'pageTitle' => 'Créer un nouvel événement'
                    ]);
                    return;
                }

                $this->workshopModel->createFromEvent($eventId, $instructorId, $dateDebut, $dateFin);
                break;
        }

        // Handle file uploads
        $documents = $this->getFile('documents');
        if ($documents && !empty($documents['name'][0])) {
            $this->fileManager->uploadMultiple($documents, $eventId);
        }

        $this->setFlash('success', 'Événement créé avec succès');
        $this->redirect('evenements/view/' . $eventId);
    }

    /**
     * Display event edit form
     * @param int $id Event ID
     */
    public function edit($id) {
        if (!$this->requireAuth()) {
            return;
        }

        $event = $this->evenementModel->findWithCreator($id);

        if (!$event) {
            $this->renderNotFound();
            return;
        }

        // Check if user is creator or admin
        if ($event['createurId'] != $this->auth->getUserId() && !$this->auth->hasRole('Admin')) {
            $this->renderForbidden();
            return;
        }

        // Determine event type and get specific details
        $eventType = $this->getEventType($id);
        $specificDetails = [];

        switch ($eventType) {
            case 'Conference':
                $specificDetails = $this->conferenceModel->find($id);
                break;

            case 'Seminaire':
                $specificDetails = $this->seminaireModel->find($id);
                break;

            case 'Workshop':
                $specificDetails = $this->workshopModel->find($id);
                break;
        }

        // Get all researchers for selection (for workshop instructor)
        $chercheurs = $this->utilisateurModel->getAllWithRoles();

        // Get event documents
        $documents = $this->fileManager->listFiles($id);

        $this->render('evenements/edit', [
            'event' => $event,
            'eventType' => $eventType,
            'specificDetails' => $specificDetails,
            'chercheurs' => $chercheurs,
            'documents' => $documents,
            'pageTitle' => 'Modifier - ' . $event['titre']
        ]);
    }

    /**
     * Process event update
     * @param int $id Event ID
     */
    public function update($id) {
        if (!$this->requireAuth() || !$this->isPost()) {
            return;
        }

        $event = $this->evenementModel->find($id);

        if (!$event) {
            $this->renderNotFound();
            return;
        }

        // Check if user is creator or admin
        if ($event['createurId'] != $this->auth->getUserId() && !$this->auth->hasRole('Admin')) {
            $this->renderForbidden();
            return;
        }

        // Get form data
        $data = [
            'titre' => $this->getInput('titre'),
            'description' => $this->getInput('description'),
            'projetId' => $this->getInput('projetId'),
            'lieu' => $this->getInput('lieu')
        ];

        // Validate base event data
        $rules = [
            'titre' => 'required|min:3|max:255',
            'description' => 'required'
        ];

        $validation = $this->validate($data, $rules);

        if ($validation !== true) {
            $this->setFlash('error', 'Données invalides');
            $this->redirect('evenements/edit/' . $id);
            return;
        }

        // Update base event
        $this->evenementModel->update($id, $data);

        // Determine event type and update specific details
        $eventType = $this->getEventType($id);

        switch ($eventType) {
            case 'Conference':
                $dateDebut = $this->getInput('dateDebut');
                $dateFin = $this->getInput('dateFin');

                $specificRules = [
                    'dateDebut' => 'required|date',
                    'dateFin' => 'required|date'
                ];

                $specificValidation = $this->validate([
                    'dateDebut' => $dateDebut,
                    'dateFin' => $dateFin
                ], $specificRules);

                if ($specificValidation === true) {
                    $this->conferenceModel->update($id, [
                        'dateDebut' => $dateDebut,
                        'dateFin' => $dateFin
                    ]);
                }
                break;

            case 'Seminaire':
                $date = $this->getInput('date');

                $specificRules = [
                    'date' => 'required|date'
                ];

                $specificValidation = $this->validate([
                    'date' => $date
                ], $specificRules);

                if ($specificValidation === true) {
                    $this->seminaireModel->update($id, [
                        'date' => $date
                    ]);
                }
                break;

            case 'Workshop':
                $instructorId = $this->getInput('instructorId');
                $dateDebut = $this->getInput('dateDebut');
                $dateFin = $this->getInput('dateFin');

                $specificRules = [
                    'dateDebut' => 'required|date',
                    'dateFin' => 'required|date'
                ];

                $specificValidation = $this->validate([
                    'dateDebut' => $dateDebut,
                    'dateFin' => $dateFin
                ], $specificRules);

                if ($specificValidation === true) {
                    $this->workshopModel->update($id, [
                        'instructorId' => $instructorId,
                        'dateDebut' => $dateDebut,
                        'dateFin' => $dateFin
                    ]);
                }
                break;
        }

        // Handle file uploads
        $documents = $this->getFile('documents');
        if ($documents && !empty($documents['name'][0])) {
            $this->fileManager->uploadMultiple($documents, $id);
        }

        $this->setFlash('success', 'Événement mis à jour avec succès');
        $this->redirect('evenements/view/' . $id);
    }

    /**
     * Delete event
     * @param int $id Event ID
     */
    public function delete($id) {
        if (!$this->requireAuth()) {
            return;
        }

        $event = $this->evenementModel->find($id);

        if (!$event) {
            $this->renderNotFound();
            return;
        }

        // Check if user is creator or admin
        if ($event['createurId'] != $this->auth->getUserId() && !$this->auth->hasRole('Admin')) {
            $this->renderForbidden();
            return;
        }

        // Determine event type and delete specific record
        $eventType = $this->getEventType($id);

        switch ($eventType) {
            case 'Conference':
                $this->conferenceModel->delete($id);
                break;

            case 'Seminaire':
                $this->seminaireModel->delete($id);
                break;

            case 'Workshop':
                $this->workshopModel->delete($id);
                break;
        }

        // Delete base event
        $this->evenementModel->delete($id);

        // Delete associated files
        $documents = $this->fileManager->listFiles($id);
        foreach ($documents as $document) {
            $this->fileManager->delete($document['filename'], $id);
        }

        $this->setFlash('success', 'Événement supprimé avec succès');
        $this->redirect('evenements');
    }

    /**
     * Handle document deletion
     * @param int $eventId Event ID
     * @param string $filename Filename to delete
     */
    public function deleteDocument($eventId, $filename) {
        if (!$this->requireAuth()) {
            return;
        }

        $event = $this->evenementModel->find($eventId);

        if (!$event) {
            $this->renderNotFound();
            return;
        }

        // Check if user is creator or admin
        if ($event['createurId'] != $this->auth->getUserId() && !$this->auth->hasRole('Admin')) {
            $this->renderForbidden();
            return;
        }

        if ($this->fileManager->delete($filename, $eventId)) {
            $this->setFlash('success', 'Document supprimé avec succès');
        } else {
            $this->setFlash('error', 'Erreur lors de la suppression du document');
        }

        $this->redirect('evenements/edit/' . $eventId);
    }

    /**
     * Display seminars list
     */
    public function seminaires() {
        if (!$this->requireAuth()) {
            return;
        }

        $seminaires = $this->seminaireModel->getAllWithDetails();

        $this->render('evenements/seminaires', [
            'seminaires' => $seminaires,
            'pageTitle' => 'Séminaires'
        ]);
    }

    /**
     * Display conferences list
     */
    public function conferences() {
        if (!$this->requireAuth()) {
            return;
        }

        $conferences = $this->conferenceModel->getAllWithDetails();

        $this->render('evenements/conferences', [
            'conferences' => $conferences,
            'pageTitle' => 'Conférences'
        ]);
    }

    /**
     * Display workshops list
     */
    public function workshops() {
        if (!$this->requireAuth()) {
            return;
        }

        $workshops = $this->workshopModel->getAllWithDetails();

        $this->render('evenements/workshops', [
            'workshops' => $workshops,
            'pageTitle' => 'Ateliers'
        ]);
    }

    /**
     * Search events
     */
    public function search() {
        if (!$this->requireAuth()) {
            return;
        }

        $query = $this->getInput('q', '');

        if (empty($query)) {
            $this->redirect('evenements');
            return;
        }

        $results = $this->searchEvents($query);

        $this->render('evenements/search', [
            'results' => $results,
            'query' => $query,
            'pageTitle' => 'Résultats de recherche: ' . $query
        ]);
    }

    /**
     * Return events as JSON (for AJAX requests)
     */
    public function getEventsJson() {
        if (!$this->requireAuth()) {
            return;
        }

        $events = $this->evenementModel->getAllWithTypes();

        // Format dates for calendar display
        $formattedEvents = [];

        foreach ($events as $event) {
            $eventType = $this->getEventType($event['id']);
            $specificDetails = null;

            // Get event dates based on type
            switch ($eventType) {
                case 'Conference':
                    $specificDetails = $this->conferenceModel->find($event['id']);
                    $start = $specificDetails['dateDebut'] ?? null;
                    $end = $specificDetails['dateFin'] ?? null;
                    break;

                case 'Seminaire':
                    $specificDetails = $this->seminaireModel->find($event['id']);
                    $start = $specificDetails['date'] ?? null;
                    $end = $specificDetails['date'] ?? null;
                    break;

                case 'Workshop':
                    $specificDetails = $this->workshopModel->find($event['id']);
                    $start = $specificDetails['dateDebut'] ?? null;
                    $end = $specificDetails['dateFin'] ?? null;
                    break;

                default:
                    $start = null;
                    $end = null;
            }

            if ($start) {
                $formattedEvents[] = [
                    'id' => $event['id'],
                    'title' => $event['titre'],
                    'start' => $start,
                    'end' => $end,
                    'type' => $eventType,
                    'url' => $this->config->get('app.url') . '/evenements/view/' . $event['id']
                ];
            }
        }

        $this->json($formattedEvents);
    }

    /**
     * Determine event type from ID
     * @param int $id Event ID
     * @return string Event type
     */
    private function getEventType($id) {
        // Check if it's a conference
        if ($this->conferenceModel->find($id)) {
            return 'Conference';
        }

        // Check if it's a seminar
        if ($this->seminaireModel->find($id)) {
            return 'Seminaire';
        }

        // Check if it's a workshop
        if ($this->workshopModel->find($id)) {
            return 'Workshop';
        }

        // Default to standard event
        return 'Standard';
    }

    /**
     * Search events by title, description, or location
     * @param string $query Search query
     * @return array Search results
     */
    private function searchEvents($query) {
        $db = Db::getInstance();

        $searchQuery = "
            SELECT e.*, 
                CASE 
                    WHEN s.evenementId IS NOT NULL THEN 'Seminaire' 
                    WHEN c.evenementId IS NOT NULL THEN 'Conference'
                    WHEN w.evenementId IS NOT NULL THEN 'Workshop'
                    ELSE 'Standard'
                END as type
            FROM Evenement e
            LEFT JOIN Seminaire s ON e.id = s.evenementId
            LEFT JOIN Conference c ON e.id = c.evenementId
            LEFT JOIN Workshop w ON e.id = w.evenementId
            WHERE e.titre LIKE :query 
               OR e.description LIKE :query 
               OR e.lieu LIKE :query
            ORDER BY e.id DESC
        ";

        $stmt = $db->prepare($searchQuery);
        $stmt->execute(['query' => '%' . $query . '%']);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}