<?php
require_once './core/Controller.php';
require_once './models/Actualite.php';
require_once './utils/FileManager.php';

/**
 * News Controller
 * Manages news and announcements
 */
class NewsController extends Controller {
    private $fileManager;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->fileManager = new FileManager('uploads/news/');
    }

    /**
     * News index page
     */
    public function index() {
        // Get search parameters
        $search = $this->getInput('search');
        $sort = $this->getInput('sort', 'recent');

        // Get pagination parameters
        $page = max(1, intval($this->getInput('page', 1)));
        $perPage = 12; // Number of items per page

        $actualiteModel = new Actualite();

        // Apply search if provided
        if (!empty($search)) {
            $news = $actualiteModel->search($search);
        } else {
            // Apply sorting
            switch ($sort) {
                case 'oldest':
                    $news = $actualiteModel->getAllWithAuthorDetails('a.datePublication ASC');
                    break;
                case 'title':
                    $news = $actualiteModel->getAllWithAuthorDetails('a.titre ASC');
                    break;
                case 'recent':
                default:
                    $news = $actualiteModel->getAllWithAuthorDetails('a.datePublication DESC');
                    break;
            }
        }

        // Pagination
        $totalItems = count($news);
        $totalPages = ceil($totalItems / $perPage);

        // Ensure valid page number
        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
        }

        // Get items for current page
        $startIndex = ($page - 1) * $perPage;
        $news = array_slice($news, $startIndex, $perPage);

        $pagination = [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
            'totalItems' => $totalItems
        ];

        $this->render('news/index', [
            'pageTitle' => 'Actualités',
            'news' => $news,
            'pagination' => $pagination,
            'search' => $search,
            'sort' => $sort
        ]);
    }

    /**
     * View news details
     * @param int $id News ID
     */
    public function view($id) {
        $actualiteModel = new Actualite();
        $news = $actualiteModel->findWithAuthor($id);

        if (!$news) {
            $this->renderNotFound();
            return;
        }

        // Get related news (exclude current)
        $relatedNews = $this->getRelatedNews($news);

        // Get related event if exists
        $relatedEvent = null;
        if (!empty($news['evenementId']) && class_exists('Evenement')) {
            $evenementModel = new Evenement();
            $relatedEvent = $evenementModel->find($news['evenementId']);

            // Add event type info if available
            if ($relatedEvent) {
                $eventType = $this->getEventType($relatedEvent['id']);
                $relatedEvent['type'] = $eventType;
            }
        }

        $this->render('news/view', [
            'pageTitle' => $news['titre'],
            'news' => $news,
            'relatedNews' => $relatedNews,
            'relatedEvent' => $relatedEvent
        ]);
    }

    /**
     * Determine event type
     * @param int $eventId
     * @return string
     */
    private function getEventType($eventId) {
        $db = Db::getInstance();

        $stmt = $db->prepare("SELECT COUNT(*) FROM Seminaire WHERE evenementId = :id");
        $stmt->execute(['id' => $eventId]);
        if ($stmt->fetchColumn() > 0) {
            return 'Séminaire';
        }

        $stmt = $db->prepare("SELECT COUNT(*) FROM Conference WHERE evenementId = :id");
        $stmt->execute(['id' => $eventId]);
        if ($stmt->fetchColumn() > 0) {
            return 'Conférence';
        }

        $stmt = $db->prepare("SELECT COUNT(*) FROM Workshop WHERE evenementId = :id");
        $stmt->execute(['id' => $eventId]);
        if ($stmt->fetchColumn() > 0) {
            return 'Workshop';
        }

        return 'Événement';
    }

    /**
     * Get related news
     * @param array $news Current news item
     * @return array
     */
    private function getRelatedNews($news) {
        $db = Db::getInstance();

        // Get news with same auteur or related to same event
        $query = "
            SELECT a.*, u.nom as auteurNom, u.prenom as auteurPrenom
            FROM Actualite a
            LEFT JOIN Utilisateur u ON a.auteurId = u.id
            WHERE a.id != :id AND 
                (a.auteurId = :auteurId OR 
                 (a.evenementId IS NOT NULL AND a.evenementId = :evenementId))
            ORDER BY a.datePublication DESC
            LIMIT 4
        ";

        $stmt = $db->prepare($query);
        $stmt->execute([
            'id' => $news['id'],
            'auteurId' => $news['auteurId'] ?? 0,
            'evenementId' => $news['evenementId'] ?? 0
        ]);

        $related = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // If not enough related news, get most recent
        if (count($related) < 4) {
            $remaining = 4 - count($related);
            $excludeIds = array_column($related, 'id');
            $excludeIds[] = $news['id'];

            $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));

            $query = "
                SELECT a.*, u.nom as auteurNom, u.prenom as auteurPrenom
                FROM Actualite a
                LEFT JOIN Utilisateur u ON a.auteurId = u.id
                WHERE a.id NOT IN ({$placeholders})
                ORDER BY a.datePublication DESC
                LIMIT {$remaining}
            ";

            $stmt = $db->prepare($query);
            $stmt->execute($excludeIds);

            $related = array_merge($related, $stmt->fetchAll(PDO::FETCH_ASSOC));
        }

        return $related;
    }

    /**
     * Create news form
     */
    public function create() {
        // Ensure user is authenticated and has permission
        if (!$this->requireAuth() || !$this->requirePermission('create_news')) {
            return;
        }

        // Get events for relation
        $events = [];
        if (class_exists('Evenement')) {
            $evenementModel = new Evenement();
            if (method_exists($evenementModel, 'getAllWithTypes')) {
                $events = $evenementModel->getAllWithTypes();
            }
        }

        $this->render('news/create', [
            'pageTitle' => 'Créer une Actualité',
            'events' => $events
        ]);
    }

    /**
     * Store news
     */
    public function store() {
        // Ensure user is authenticated and has permission
        if (!$this->requireAuth() || !$this->requirePermission('create_news')) {
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('news/create');
            return;
        }

        // Get form data
        $titre = $this->getInput('titre');
        $contenu = $this->getInput('contenu');
        $evenementId = $this->getInput('evenement_id') ?: null;

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
            $this->render('news/create', [
                'pageTitle' => 'Créer une Actualité',
                'errors' => $validation,
                'titre' => $titre,
                'contenu' => $contenu,
                'evenement_id' => $evenementId
            ]);
            return;
        }

        // Handle image upload
        $image = $this->getFile('image');
        $mediaUrl = null;

        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->fileManager->upload($image);
            if ($uploadResult) {
                $mediaUrl = $uploadResult['path'];
            }
        }

        // Create news
        try {
            $db = Db::getInstance();
            $db->beginTransaction();

            // Prepare data
            $newsData = [
                'titre' => $titre,
                'contenu' => $contenu,
                'mediaUrl' => $mediaUrl,
                'auteurId' => $this->auth->getUser()['id'],
                'datePublication' => date('Y-m-d H:i:s'),
                'evenementId' => $evenementId
            ];

            // Create SQL query
            $columns = implode(', ', array_keys($newsData));
            $placeholders = ':' . implode(', :', array_keys($newsData));

            $query = "INSERT INTO Actualite ({$columns}) VALUES ({$placeholders})";
            $stmt = $db->prepare($query);

            foreach ($newsData as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            $stmt->execute();
            $newsId = $db->lastInsertId();

            $db->commit();

            if ($newsId) {
                $this->setFlash('success', 'L\'actualité a été créée avec succès.');
                $this->redirect('news/' . $newsId);
            } else {
                throw new Exception('Failed to get last insert ID');
            }
        } catch (Exception $e) {
            // Log the specific error
            error_log('News creation error: ' . $e->getMessage());

            if (isset($db)) {
                $db->rollBack();
            }

            $this->setFlash('error', 'Une erreur est survenue lors de la création de l\'actualité: ' . $e->getMessage());
            $this->redirect('news/create');
        }
    }
// Add this helper method to your NewsController class
    private function columnExists($table, $column) {
        $db = Db::getInstance();
        $stmt = $db->query("SHOW COLUMNS FROM {$table} LIKE '{$column}'");
        return $stmt->rowCount() > 0;
    }
    /**
     * Edit news form
     * @param int $id News ID
     */
    public function edit($id) {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        $actualiteModel = new Actualite();
        $news = $actualiteModel->findWithAuthor($id);

        if (!$news) {
            $this->renderNotFound();
            return;
        }

        // Check if user is author or has admin permission
        $isAuthor = $news['auteurId'] == $this->auth->getUser()['id'];
        $canEdit = $isAuthor ?
            $this->auth->hasPermission('edit_own_news') :
            $this->auth->hasPermission('edit_news');

        if (!$canEdit) {
            $this->renderForbidden();
            return;
        }

        // Get events for relation
        $events = [];
        if (class_exists('Evenement')) {
            $evenementModel = new Evenement();
            if (method_exists($evenementModel, 'getAllWithTypes')) {
                $events = $evenementModel->getAllWithTypes();
            }
        }

        $this->render('news/edit', [
            'pageTitle' => 'Modifier l\'actualité: ' . $news['titre'],
            'news' => $news,
            'events' => $events
        ]);
    }

    /**
     * Update news
     * @param int $id News ID
     */
    /**
     * Update news
     * @param int $id News ID
     */
    public function update($id) {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        $actualiteModel = new Actualite();
        $news = $actualiteModel->findWithAuthor($id);

        if (!$news) {
            $this->renderNotFound();
            return;
        }

        // Check if user is author or has admin permission
        $isAuthor = $news['auteurId'] == $this->auth->getUser()['id'];
        $canEdit = $isAuthor ?
            $this->auth->hasPermission('edit_own_news') :
            $this->auth->hasPermission('edit_news');

        if (!$canEdit) {
            $this->renderForbidden();
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('news/edit/' . $id);
            return;
        }

        // Get form data
        $titre = $this->getInput('titre');
        $contenu = $this->getInput('contenu');
        $evenementId = $this->getInput('evenement_id') ?: null;
        $removeImage = $this->getInput('remove_image') ? true : false;

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
            $this->redirect('news/edit/' . $id);
            return;
        }

        // Data to update
        $data = [
            'titre' => $titre,
            'contenu' => $contenu,
            'evenementId' => $evenementId
        ];

        // Handle image removal if requested
        if ($removeImage && !empty($news['mediaUrl'])) {
            $this->fileManager->delete(basename($news['mediaUrl']));
            $data['mediaUrl'] = null;
        }
        // Handle image upload if provided
        else {
            $image = $this->getFile('image');
            if ($image && $image['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->fileManager->upload($image);
                if ($uploadResult) {
                    $data['mediaUrl'] = $uploadResult['path'];

                    // Delete old image if exists
                    if (!empty($news['mediaUrl'])) {
                        $this->fileManager->delete(basename($news['mediaUrl']));
                    }
                }
            }
        }

        // Update news
        $updated = $actualiteModel->update($id, $data);

        if ($updated) {
            $this->setFlash('success', 'L\'actualité a été mise à jour avec succès.');
            $this->redirect('news/' . $id);
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de la mise à jour de l\'actualité.');
            $this->redirect('news/edit/' . $id);
        }
    }
    /**
     * Delete news
     * @param int $id News ID
     */
    public function delete($id) {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        $actualiteModel = new Actualite();
        $news = $actualiteModel->find($id);

        if (!$news) {
            $this->renderNotFound();
            return;
        }

        // Check if user is author or has admin permission
        $isAuthor = $news['auteurId'] == $this->auth->getUser()['id'];
        $canDelete = $isAuthor ?
            $this->auth->hasPermission('delete_own_news') :
            $this->auth->hasPermission('delete_news');

        if (!$canDelete) {
            $this->renderForbidden();
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('news/' . $id);
            return;
        }

        // Delete image if exists
        if (!empty($news['mediaUrl'])) {
            $this->fileManager->delete(basename($news['mediaUrl']));
        }

        // Delete news
        $deleted = $actualiteModel->delete($id);

        if ($deleted) {
            $this->setFlash('success', 'L\'actualité a été supprimée avec succès.');
            $this->redirect('news');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de la suppression de l\'actualité.');
            $this->redirect('news/' . $id);
        }
    }
}