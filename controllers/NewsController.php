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
        $actualiteModel = new Actualite();
        $news = $actualiteModel->getAllWithAuthorDetails();

        $this->render('news/index', [
            'pageTitle' => 'Actualités',
            'news' => $news
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

        $this->render('news/view', [
            'pageTitle' => $news['titre'],
            'news' => $news,
            'relatedNews' => $relatedNews
        ]);
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
        $evenementModel = new Evenement();
        $events = $evenementModel->getAllWithTypes();

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
        $imageUrl = null;

        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->fileManager->upload($image);
            if ($uploadResult) {
                $imageUrl = $uploadResult['path'];
            }
        }

        // Create news
        $actualiteModel = new Actualite();
        $newsId = $actualiteModel->create([
            'titre' => $titre,
            'contenu' => $contenu,
            'imageUrl' => $imageUrl,
            'auteurId' => $this->auth->getUser()['id'],
            'datePublication' => date('Y-m-d H:i:s'),
            'evenementId' => $evenementId
        ]);

        if ($newsId) {
            $this->setFlash('success', 'L\'actualité a été créée avec succès.');
            $this->redirect('news/' . $newsId);
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de la création de l\'actualité.');
            $this->redirect('news/create');
        }
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
        $evenementModel = new Evenement();
        $events = $evenementModel->getAllWithTypes();

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

        // Handle image upload if provided
        $image = $this->getFile('image');
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->fileManager->upload($image);
            if ($uploadResult) {
                $data['imageUrl'] = $uploadResult['path'];

                // Delete old image if exists
                if (!empty($news['imageUrl'])) {
                    $this->fileManager->delete(basename($news['imageUrl']));
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
        if (!empty($news['imageUrl'])) {
            $this->fileManager->delete(basename($news['imageUrl']));
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