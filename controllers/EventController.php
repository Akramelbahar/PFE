<?php
require_once './core/Controller.php';
require_once './models/events/Evenement.php';
require_once './models/events/Conference.php';
require_once './models/events/Seminaire.php';
require_once './models/events/Workshop.php';
require_once './models/users/Utilisateur.php';
require_once './models/projects/ProjetRecherche.php';
require_once './utils/FileManager.php';

/**
 * EventController - Manages events for the research association
 */
class EventController extends Controller {
    protected $evenementModel;
    protected $conferenceModel;
    protected $seminaireModel;
    protected $workshopModel;
    protected $utilisateurModel;
    protected $projetModel;
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
        $this->projetModel = new ProjetRecherche();
        $this->fileManager = new FileManager('uploads/events/');
    }

    /**
     * Display events dashboard
     */
    public function index() {
        // Require authentication
        if (!$this->requireAuth()) {
            return;
        }

        // Get filter parameters
        $type = $this->getInput('type');
        $creator = $this->getInput('creator');
        $startDate = $this->getInput('start_date');
        $endDate = $this->getInput('end_date');

        // Get filtered events
        $events = $this->getFilteredEvents($type, $creator, $startDate, $endDate);

        // Prepare filter options
        $filters = $this->getEventFilters();

        $this->render('evenements/index', [
            'events' => $events,
            'filters' => $filters,
            'currentFilters' => [
                'type' => $type,
                'creator' => $creator,
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'pageTitle' => 'Événements'
        ]);
    }

    /**
     * View specific event details
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
        $specificDetails = $this->getEventSpecificDetails($id, $eventType);

        // Get event documents
        $documents = $this->fileManager->listFiles($id);

        // Get related news
        $relatedNews = $this->getRelatedNews($id);

        // Get related project if exists
        $relatedProject = $event['projetId'] ? $this->projetModel->find($event['projetId']) : null;

        $this->render('evenements/view', [
            'event' => $event,
            'eventType' => $eventType,
            'specificDetails' => $specificDetails,
            'documents' => $documents,
            'relatedNews' => $relatedNews,
            'relatedProject' => $relatedProject,
            'pageTitle' => $event['titre']
        ]);
    }

    /**
     * Create new event form
     */
    public function create() {
        if (!$this->requireAuth()) {
            return;
        }

        // Get all researchers for selection
        $chercheurs = $this->utilisateurModel->getAllWithRoles();

        // Get all projects
        $projets = $this->projetModel->all();

        $this->render('evenements/create', [
            'chercheurs' => $chercheurs,
            'projets' => $projets,
            'pageTitle' => 'Créer un nouvel événement'
        ]);
    }

    /**
     * Store new event
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
            'createurId' => $this->auth->getUser()['id'],
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
            $this->redirect('events/create');
            return;
        }

        // Create specific event type
        try {
            switch ($data['type']) {
                case 'Conference':
                    $this->createConference($eventId);
                    break;
                case 'Seminaire':
                    $this->createSeminaire($eventId);
                    break;
                case 'Workshop':
                    $this->createWorkshop($eventId);
                    break;
            }
        } catch (Exception $e) {
            // Rollback event creation if specific type fails
            $this->evenementModel->delete($eventId);
            $this->setFlash('error', 'Erreur lors de la création des détails de l\'événement');
            $this->redirect('events/create');
            return;
        }

        // Handle file uploads
        $documents = $this->getFile('documents');
        if ($documents && !empty($documents['name'][0])) {
            $this->fileManager->uploadMultiple($documents, $eventId);
        }

        $this->setFlash('success', 'Événement créé avec succès');
        $this->redirect('events/' . $eventId);
    }

    /**
     * Edit event form
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

        // Check permissions
        if (!$this->canEditEvent($event)) {
            $this->renderForbidden();
            return;
        }

        // Determine event type and get specific details
        $eventType = $this->getEventType($id);
        $specificDetails = $this->getEventSpecificDetails($id, $eventType);

        // Get all researchers for selection (for workshop instructor)
        $chercheurs = $this->utilisateurModel->getAllWithRoles();

        // Get all projects
        $projets = $this->projetModel->all();

        // Get event documents
        $documents = $this->fileManager->listFiles($id);

        $this->render('evenements/edit', [
            'event' => $event,
            'eventType' => $eventType,
            'specificDetails' => $specificDetails,
            'chercheurs' => $chercheurs,
            'projets' => $projets,
            'documents' => $documents,
            'pageTitle' => 'Modifier - ' . $event['titre']
        ]);
    }

    /**
     * Update event
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

        // Check permissions
        if (!$this->canEditEvent($event)) {
            $this->renderForbidden();
            return;
        }

        // Base event data to update
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
            $this->redirect('events/edit/' . $id);
            return;
        }

        // Update base event
        $this->evenementModel->update($id, $data);

        // Update specific event type details
        $eventType = $this->getEventType($id);
        $this->updateEventTypeDetails($id, $eventType);

        // Handle file uploads
        $documents = $this->getFile('documents');
        if ($documents && !empty($documents['name'][0])) {
            $this->fileManager->uploadMultiple($documents, $id);
        }

        $this->setFlash('success', 'Événement mis à jour avec succès');
        $this->redirect('events/' . $id);
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

        // Check permissions
        if (!$this->canEditEvent($event)) {
            $this->renderForbidden();
            return;
        }

        // Delete specific event type record
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
        $this->redirect('events');
    }

    /**
     * Private helper methods
     */

    /**
     * Create conference-specific details
     * @param int $eventId Event ID
     * @throws Exception
     */
    private function createConference($eventId) {
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
            throw new Exception('Invalid conference dates');
        }

        $this->conferenceModel->createFromEvent($eventId, $dateDebut, $dateFin);
    }

    /**
     * Create seminar-specific details
     * @param int $eventId Event ID
     * @throws Exception
     */
    private function createSeminaire($eventId) {
        $date = $this->getInput('date');

        $specificRules = [
            'date' => 'required|date'
        ];

        $specificValidation = $this->validate([
            'date' => $date
        ], $specificRules);

        if ($specificValidation !== true) {
            throw new Exception('Invalid seminar date');
        }

        $this->seminaireModel->createFromEvent($eventId, $date);
    }

    /**
     * Create workshop-specific details
     * @param int $eventId Event ID
     * @throws Exception
     */
    private function createWorkshop($eventId) {
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
            throw new Exception('Invalid workshop dates');
        }
        if ($specificValidation !== true) {
            throw new Exception('Invalid workshop dates');
        }

// Ajoutez:
// Vérifier que date de fin est après date de début
        if (strtotime($dateDebut) > strtotime($dateFin)) {
            throw new Exception('La date de fin doit être postérieure à la date de début');
        }
        $this->workshopModel->createFromEvent($eventId, $instructorId, $dateDebut, $dateFin);
    }

    /**
     * Update event type specific details
     * @param int $id Event ID
     * @param string $eventType Event type
     */
    private function updateEventTypeDetails($id, $eventType) {
        try {

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
                        'dateFin' =>     'required|date'
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
        } catch (Exception $e) {
            // Log error if needed
            $this->setFlash('error', 'Erreur lors de la mise à jour des détails de l\'événement');
        }
    }

    /**
     * Delete event document
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

        // Check permissions
        if (!$this->canEditEvent($event)) {
            $this->renderForbidden();
            return;
        }

        // Just use FileManager to delete the file, without DB operations
        if ($this->fileManager->delete($filename, $eventId)) {
            $this->setFlash('success', 'Document supprimé avec succès');
        } else {
            $this->setFlash('error', 'Erreur lors de la suppression du document');
        }

        $this->redirect('events/edit/' . $eventId);
    }

    public function downloadDocument($eventId, $filename) {
        if (!$this->requireAuth()) {
            return;
        }

        $event = $this->evenementModel->find($eventId);

        if (!$event) {
            $this->renderNotFound();
            return;
        }

        // Get the file path - use the correct path structure
        $filePath = 'uploads/events/' . $eventId . '/' . $filename;

        // Debug - print path to verify it exists
        // echo $filePath; exit;  // Uncomment this to debug path issues

        // Check if file exists
        if (!file_exists($filePath)) {
            $this->setFlash('error', 'Le fichier demandé n\'existe pas');
            $this->redirect('events/' . $eventId);
            return;
        }

        // Set headers for download
        header('Content-Description: File Transfer');
        header('Content-Type: ' . mime_content_type($filePath));
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
                    'end' => $end ?? $start,
                    'type' => $eventType,
                    'url' => $this->config->get('app.url') . '/events/' . $event['id']
                ];
            }
        }

        $this->json($formattedEvents);
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
            $this->redirect('events');
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
     * Get event-specific details
     * @param int $id Event ID
     * @param string $eventType Event type
     * @return array
     */
    private function getEventSpecificDetails($id, $eventType) {
        switch ($eventType) {
            case 'Conference':
                $details = $this->conferenceModel->find($id);
                break;
            case 'Seminaire':
                $details = $this->seminaireModel->find($id);
                break;
            case 'Workshop':
                $details = $this->workshopModel->find($id);
                if (isset($details['instructorId'])) {
                    $instructor = $this->utilisateurModel->find($details['instructorId']);
                    $details['instructorName'] = $instructor
                        ? $instructor['prenom'] . ' ' . $instructor['nom']
                        : 'Non assigné';
                }
                break;
            default:
                $details = [];
        }
        return $details;
    }

    /**
     * Get related news for an event
     * @param int $id Event ID
     * @return array
     */
    private function getRelatedNews($id) {
        $db = Db::getInstance();
        $query = "
            SELECT a.*, u.nom as auteurNom, u.prenom as auteurPrenom
            FROM Actualite a
            LEFT JOIN Utilisateur u ON a.auteurId = u.id
            WHERE a.evenementId = :eventId
            ORDER BY a.datePublication DESC
            LIMIT 4
        ";

        $stmt = $db->prepare($query);
        $stmt->execute(['eventId' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get filtered events
     * @param string|null $type Event type
     * @param int|null $creator Creator ID
     * @param string|null $startDate Start date
     * @param string|null $endDate End date
     * @return array
     */
    private function getFilteredEvents($type = null, $creator = null, $startDate = null, $endDate = null) {
        $db = Db::getInstance();

        $query = "
            SELECT e.*, 
                CASE 
                    WHEN s.evenementId IS NOT NULL THEN 'Seminaire' 
                    WHEN c.evenementId IS NOT NULL THEN 'Conference'
                    WHEN w.evenementId IS NOT NULL THEN 'Workshop'
                    ELSE 'Standard'
                END as type,
                u.nom as createurNom, 
                u.prenom as createurPrenom,
                COALESCE(s.date, c.dateDebut, w.dateDebut) as eventDate
            FROM Evenement e
            LEFT JOIN Seminaire s ON e.id = s.evenementId
            LEFT JOIN Conference c ON e.id = c.evenementId
            LEFT JOIN Workshop w ON e.id = w.evenementId
            LEFT JOIN Utilisateur u ON e.createurId = u.id
            WHERE 1=1
        ";

        $params = [];

        // Add type filter
        if ($type) {
            $query .= " AND (
                ('".$type."' = 'Seminaire' AND s.evenementId IS NOT NULL) OR 
                ('".$type."' = 'Conference' AND c.evenementId IS NOT NULL) OR 
                ('".$type."' = 'Workshop' AND w.evenementId IS NOT NULL)
            )";
        }

        // Add creator filter
        if ($creator) {
            $query .= " AND e.createurId = :creator";
            $params['creator'] = $creator;
        }

        // Add date range filter
        if ($startDate) {
            $query .= " AND COALESCE(s.date, c.dateDebut, w.dateDebut) >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $query .= " AND COALESCE(s.date, c.dateFin, w.dateFin) <= :end_date";
            $params['end_date'] = $endDate;
        }

        $query .= " ORDER BY eventDate DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get event filters
     * @return array
     */
    private function getEventFilters() {
        $db = Db::getInstance();
        $filters = [];

        // Event types
        $filters['types'] = ['Seminaire', 'Conference', 'Workshop'];

        // Creators
        $stmt = $db->query("
            SELECT DISTINCT u.id, u.nom, u.prenom
            FROM Utilisateur u
            JOIN Evenement e ON u.id = e.createurId
            ORDER BY u.nom, u.prenom
        ");
        $filters['creators'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Date range
        $stmt = $db->query("
            SELECT 
                MIN(COALESCE(s.date, c.dateDebut, w.dateDebut)) as earliest_date,
                MAX(COALESCE(s.date, c.dateFin, w.dateFin)) as latest_date
            FROM Evenement e
            LEFT JOIN Seminaire s ON e.id = s.evenementId
            LEFT JOIN Conference c ON e.id = c.evenementId
            LEFT JOIN Workshop w ON e.id = w.evenementId
        ");
        $dateRange = $stmt->fetch(PDO::FETCH_ASSOC);
        $filters['date_range'] = $dateRange;

        // Search events implementation
        $searchQuery = $this->searchEvents('');

        return $filters;
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

    /**
     * Check if current user can edit the event
     * @param array $event Event data
     * @return bool
     */
    private function canEditEvent($event) {
        return $event['createurId'] == $this->auth->getUser()['id'] ||
            $this->auth->hasRole('Admin');
    }

}