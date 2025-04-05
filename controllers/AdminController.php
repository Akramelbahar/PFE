<?php
require_once './core/Controller.php';
require_once './models/users/Admin.php';
require_once './models/users/Utilisateur.php';
require_once './models/events/Evenement.php';
require_once './models/publications/Publication.php';
require_once './models/projects/ProjetRecherche.php';
require_once './models/Actualite.php';
require_once './models/Contact.php';

/**
 * Admin Controller
 */
class AdminController extends Controller {
    /**
     * Dashboard page
     */
    public function index() {
        // Ensure user is admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Get counts for dashboard
        $counts = $this->getDashboardCounts();

        // Get recent items
        $recentItems = $this->getRecentItems();

        $this->render('admin/dashboard', [
            'pageTitle' => 'Tableau de bord',
            'counts' => $counts,
            'recentItems' => $recentItems
        ]);
    }

    /**
     * Get counts for dashboard
     * @return array
     */
    private function getDashboardCounts() {
        $db = Db::getInstance();
        $counts = [];

        // Users count
        $stmt = $db->query("SELECT COUNT(*) FROM Utilisateur");
        $counts['users'] = $stmt->fetchColumn();

        // Events count
        $stmt = $db->query("SELECT COUNT(*) FROM Evenement");
        $counts['events'] = $stmt->fetchColumn();

        // Publications count
        $stmt = $db->query("SELECT COUNT(*) FROM Publication");
        $counts['publications'] = $stmt->fetchColumn();

        // Projects count
        $stmt = $db->query("SELECT COUNT(*) FROM ProjetRecherche");
        $counts['projects'] = $stmt->fetchColumn();

        // News count
        $stmt = $db->query("SELECT COUNT(*) FROM Actualite");
        $counts['news'] = $stmt->fetchColumn();

        // Contact messages count
        $stmt = $db->query("SELECT COUNT(*) FROM Contact");
        $counts['contacts'] = $stmt->fetchColumn();

        // Researchers count
        $stmt = $db->query("SELECT COUNT(*) FROM Chercheur");
        $counts['researchers'] = $stmt->fetchColumn();

        // Board members count
        $stmt = $db->query("SELECT COUNT(*) FROM MembreBureauExecutif");
        $counts['boardMembers'] = $stmt->fetchColumn();

        return $counts;
    }

    /**
     * Get recent items for dashboard
     * @return array
     */
    private function getRecentItems() {
        $items = [];

        // Recent users
        $utilisateurModel = new Utilisateur();
        $db = Db::getInstance();
        $stmt = $db->query("SELECT * FROM Utilisateur ORDER BY dateInscription DESC LIMIT 5");
        $items['users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Recent events
        $evenementModel = new Evenement();
        $items['events'] = $this->getRecentEvents(5);

        // Recent publications
        $publicationModel = new Publication();
        $stmt = $db->query("
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
            ORDER BY p.datePublication DESC
            LIMIT 5
        ");
        $items['publications'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Recent news
        $actualiteModel = new Actualite();
        $items['news'] = $actualiteModel->getRecent(5);

        // Recent contact messages
        $contactModel = new Contact();
        $items['contacts'] = $contactModel->getRecent(5);

        return $items;
    }

    /**
     * Get recent events
     * @param int $limit
     * @return array
     */
    private function getRecentEvents($limit = 5) {
        $db = Db::getInstance();

        // Query to get upcoming events of all types
        $query = "
            (SELECT e.*, s.date as eventDate, 'Seminaire' as eventType
            FROM Evenement e
            JOIN Seminaire s ON e.id = s.evenementId
            ORDER BY e.dateCreation DESC
            LIMIT :limit)
            
            UNION
            
            (SELECT e.*, c.dateDebut as eventDate, 'Conference' as eventType
            FROM Evenement e
            JOIN Conference c ON e.id = c.evenementId
            ORDER BY e.dateCreation DESC
            LIMIT :limit)
            
            UNION
            
            (SELECT e.*, w.dateDebut as eventDate, 'Workshop' as eventType
            FROM Evenement e
            JOIN Workshop w ON e.id = w.evenementId
            ORDER BY e.dateCreation DESC
            LIMIT :limit)
            
            ORDER BY dateCreation DESC
            LIMIT :final_limit
        ";

        $stmt = $db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':final_limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Users management page
     */
    public function users() {
        // Ensure user is admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Get all users with roles
        $utilisateurModel = new Utilisateur();
        $users = $utilisateurModel->getAllWithRoles();

        $this->render('admin/users', [
            'pageTitle' => 'Gestion des utilisateurs',
            'users' => $users
        ]);
    }

    /**
     * Events management page
     */
    public function events() {
        // Ensure user is admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Get all events with types
        $evenementModel = new Evenement();
        $events = $evenementModel->getAllWithTypes();

        $this->render('admin/events', [
            'pageTitle' => 'Gestion des événements',
            'events' => $events
        ]);
    }

    /**
     * Publications management page
     */
    public function publications() {
        // Ensure user is admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Get all publications with types
        $publicationModel = new Publication();
        $publications = $publicationModel->getAllWithTypes();

        $this->render('admin/publications', [
            'pageTitle' => 'Gestion des publications',
            'publications' => $publications
        ]);
    }

    /**
     * Projects management page
     */
    public function projects() {
        // Ensure user is admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Get all projects
        $projetModel = new ProjetRecherche();
        $projects = $projetModel->all();

        $this->render('admin/projects', [
            'pageTitle' => 'Gestion des projets',
            'projects' => $projects
        ]);
    }

    /**
     * News management page
     */
    public function news() {
        // Ensure user is admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Get all news
        $actualiteModel = new Actualite();
        $news = $actualiteModel->all();

        $this->render('admin/news', [
            'pageTitle' => 'Gestion des actualités',
            'news' => $news
        ]);
    }

    /**
     * Contact messages management page
     */
    public function contacts() {
        // Ensure user is admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Get all contact messages
        $contactModel = new Contact();
        $contacts = $contactModel->all();

        $this->render('admin/contacts', [
            'pageTitle' => 'Messages de contact',
            'contacts' => $contacts
        ]);
    }

    /**
     * Settings page
     */
    public function settings() {
        // Ensure user is admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Get current settings
        $config = $this->config->getAll();

        $this->render('admin/settings', [
            'pageTitle' => 'Paramètres',
            'config' => $config
        ]);
    }

    /**
     * Update settings
     */
    public function updateSettings() {
        // Ensure user is admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Check if form was submitted
        if (!$this->isPost()) {
            $this->redirect('admin/settings');
            return;
        }

        // Get form data
        $appName = $this->getInput('app_name');
        $appUrl = $this->getInput('app_url');
        $timezone = $this->getInput('timezone');
        $locale = $this->getInput('locale');
        $debug = $this->getInput('debug') === 'on';
        $sessionLifetime = (int)$this->getInput('session_lifetime');

        // Validate input
        $validation = $this->validate(
            [
                'app_name' => $appName,
                'app_url' => $appUrl,
                'timezone' => $timezone,
                'locale' => $locale,
                'session_lifetime' => $sessionLifetime
            ],
            [
                'app_name' => 'required|max:255',
                'app_url' => 'required|max:255',
                'timezone' => 'required',
                'locale' => 'required',
                'session_lifetime' => 'required|numeric'
            ]
        );

        if ($validation !== true) {
            $this->setFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
            $this->redirect('admin/settings');
            return;
        }

        // Update settings
        $this->config->set('app.name', $appName);
        $this->config->set('app.url', $appUrl);
        $this->config->set('app.timezone', $timezone);
        $this->config->set('app.locale', $locale);
        $this->config->set('app.debug', $debug);
        $this->config->set('app.session_lifetime', $sessionLifetime);

        // Save settings to local config file
        $configFile = __DIR__ . '/../../config/config.local.php';
        $configData = $this->config->getAll();

        $configContent = "<?php\nreturn " . var_export($configData, true) . ";\n";

        if (file_put_contents($configFile, $configContent)) {
            $this->setFlash('success', 'Les paramètres ont été mis à jour avec succès.');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de l\'enregistrement des paramètres.');
        }

        $this->redirect('admin/settings');
    }
}