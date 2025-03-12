<?php
require_once './core/Controller.php';
require_once './models/Actualite.php';
require_once './models/events/Evenement.php';
require_once './models/publications/Publication.php';

/**
 * Home Controller
 */
class HomeController extends Controller {
    /**
     * Home page
     */
    public function index() {
        // Get latest news
        $actualiteModel = new Actualite();
        $latestNews = $actualiteModel->getRecent(3);

        // Get upcoming events
        $evenementModel = new Evenement();
        $upcomingEvents = $this->getUpcomingEvents();

        // Get latest publications
        $publicationModel = new Publication();
        $latestPublications = $this->getLatestPublications();

        // Render the home page
        $this->render('home/index', [
            'latestNews' => $latestNews,
            'upcomingEvents' => $upcomingEvents,
            'latestPublications' => $latestPublications,
            'pageTitle' => 'Accueil'
        ]);
    }

    /**
     * Get upcoming events
     * @param int $limit
     * @return array
     */
    private function getUpcomingEvents($limit = 3) {
        $db = Db::getInstance();

        // Query to get upcoming events of all types
        $query = "
            (SELECT e.*, s.date as eventDate, 'Seminaire' as eventType
            FROM Evenement e
            JOIN Seminaire s ON e.id = s.evenementId
            WHERE s.date >= NOW()
            ORDER BY s.date ASC
            LIMIT :limit)
            
            UNION
            
            (SELECT e.*, c.dateDebut as eventDate, 'Conference' as eventType
            FROM Evenement e
            JOIN Conference c ON e.id = c.evenementId
            WHERE c.dateDebut >= NOW()
            ORDER BY c.dateDebut ASC
            LIMIT :limit)
            
            UNION
            
            (SELECT e.*, w.dateDebut as eventDate, 'Workshop' as eventType
            FROM Evenement e
            JOIN Workshop w ON e.id = w.evenementId
            WHERE w.dateDebut >= NOW()
            ORDER BY w.dateDebut ASC
            LIMIT :limit)
            
            ORDER BY eventDate ASC
            LIMIT :final_limit
        ";

        $stmt = $db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':final_limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get latest publications
     * @param int $limit
     * @return array
     */
    private function getLatestPublications($limit = 3) {
        $db = Db::getInstance();

        // Query to get latest publications with their types
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
            ORDER BY p.datePublication DESC
            LIMIT :limit
        ";

        $stmt = $db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * About page
     */
    public function about() {
        $this->render('home/about', [
            'pageTitle' => 'À propos'
        ]);
    }
}