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
        $upcomingEvents = $this->getUpcomingEvents();

        // Get latest publications
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
     * About page
     */
    public function about() {
        // Get board members from database - with graceful fallback
        $boardMembers = [];
        if (class_exists('MembreBureauExecutif')) {
            $membreModel = new MembreBureauExecutif();
            $boardMembers = $membreModel->getAllWithUserDetails();
        }

        // Get partner organizations - with graceful fallback
        $partners = $this->getPartners();

        // Get research statistics
        $stats = $this->getAssociationStatistics();

        // Get research domains
        $domains = $this->getResearchDomains();

        $this->render('home/about', [
            'boardMembers' => $boardMembers,
            'partners' => $partners,
            'stats' => $stats,
            'domains' => $domains,
            'pageTitle' => 'À propos'
        ]);
    }

    /**
     * Get upcoming events
     * @param int $limit
     * @return array
     */
    private function getUpcomingEvents($limit = 3) {
        try {
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
        } catch (Exception $e) {
            // Return empty array if query fails
            return [];
        }
    }

    /**
     * Get latest publications
     * @param int $limit
     * @return array
     */
    private function getLatestPublications($limit = 3) {
        try {
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
        } catch (Exception $e) {
            // Return empty array if query fails
            return [];
        }
    }

    /**
     * Get partners safely with error handling
     * @return array Partners
     */
    private function getPartners() {
        // Default partners to return if Partner model is not available
        $defaultPartners = [
            [
                'id' => 1,
                'nom' => 'Université Cadi Ayyad',
                'logo' => 'assets/images/partners/uca.png',
                'url' => 'https://www.uca.ma',
                'description' => 'Université publique marocaine'
            ],
            [
                'id' => 2,
                'nom' => 'CNRST',
                'logo' => 'assets/images/partners/cnrst.png',
                'url' => 'https://www.cnrst.ma',
                'description' => 'Centre National pour la Recherche Scientifique et Technique'
            ],
            [
                'id' => 3,
                'nom' => 'Ministère de l\'Éducation',
                'logo' => 'assets/images/partners/mesrsfc.png',
                'url' => 'https://www.enssup.gov.ma',
                'description' => 'Ministère de l\'Enseignement Supérieur'
            ],
            [
                'id' => 4,
                'nom' => 'AMSIC',
                'logo' => 'assets/images/partners/amsic.png',
                'url' => 'https://www.amsic.ma',
                'description' => 'Association Marocaine pour la Science, l\'Innovation et la Collaboration'
            ]
        ];

        // Check if Partner class exists or the file exists
        if (class_exists('Partner') || file_exists('./models/Partner.php')) {
            try {
                // If the class is defined but the file isn't loaded yet
                if (!class_exists('Partner') && file_exists('./models/Partner.php')) {
                    include_once './models/Partner.php';
                }

                // Now try to use the Partner model if it exists
                if (class_exists('Partner')) {
                    $partnerModel = new Partner();
                    $partners = $partnerModel->getAll();

                    // If we got partners from the database, return them
                    if (!empty($partners)) {
                        return $partners;
                    }
                }
            } catch (Exception $e) {
                // If any error occurs, fall back to default partners
            }
        }

        // Return default partners
        return $defaultPartners;
    }

    /**
     * Get research domains from database
     * @return array
     */
    private function getResearchDomains() {
        try {
            $db = Db::getInstance();

            $query = "
                SELECT * FROM DomaineRecherche
                ORDER BY priorite ASC
            ";

            $stmt = $db->query($query);
            $domains = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($domains)) {
                return $domains;
            }
        } catch (Exception $e) {
            // Continue with default domains on exception
        }

        // Return default domains if table doesn't exist or query fails
        return $this->getDefaultDomains();
    }

    /**
     * Get default research domains if database table doesn't exist
     * @return array
     */
    private function getDefaultDomains() {
        return [
            [
                'id' => 1,
                'nom' => 'Intelligence Artificielle et Data Science',
                'description' => 'Analyses de données, apprentissage automatique et systèmes intelligents',
                'icon' => 'laptop-code'
            ],
            [
                'id' => 2,
                'nom' => 'Systèmes Embarqués et IoT',
                'description' => 'Développement de solutions connectées et systèmes intelligents',
                'icon' => 'microchip'
            ],
            [
                'id' => 3,
                'nom' => 'Génie Industriel et Optimisation',
                'description' => 'Amélioration des processus et logistique avancée',
                'icon' => 'cogs'
            ]
        ];
    }

    /**
     * Get association statistics with error handling
     * @return array
     */
    private function getAssociationStatistics() {
        $stats = [
            'projects' => 12,  // Default values
            'publications' => 30,
            'events' => 15,
            'partners' => 8
        ];

        try {
            $db = Db::getInstance();

            // Get project count
            try {
                $stmt = $db->query("SELECT COUNT(*) as count FROM ProjetRecherche");
                $stats['projects'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            } catch (Exception $e) {
                // Keep default value if query fails
            }

            // Get publication count
            try {
                $stmt = $db->query("SELECT COUNT(*) as count FROM Publication");
                $stats['publications'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            } catch (Exception $e) {
                // Keep default value if query fails
            }

            // Get event count
            try {
                $stmt = $db->query("SELECT COUNT(*) as count FROM Evenement");
                $stats['events'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            } catch (Exception $e) {
                // Keep default value if query fails
            }

            // Get partner count
            try {
                $stmt = $db->query("SELECT COUNT(*) as count FROM Partner");
                $stats['partners'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            } catch (Exception $e) {
                // Keep default value if query fails
            }
        } catch (Exception $e) {
            // Return default values if database connection fails
        }

        return $stats;
    }
}