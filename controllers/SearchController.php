<?php
require_once './core/Controller.php';
require_once './models/projects/ProjetRecherche.php';
require_once './models/publications/Publication.php';
require_once './models/events/Evenement.php';
require_once './models/Actualite.php';
require_once './models/IdeeRecherche.php';
require_once './models/users/Utilisateur.php';

/**
 * Search Controller
 */
class SearchController extends Controller {
    /**
     * Search results page
     */
    public function index() {
        $query = $this->getInput('q', '');

        if (empty($query)) {
            $this->redirect('');
            return;
        }

        // Get search results from all entities
        $results = [
            'projects' => $this->searchProjects($query),
            'publications' => $this->searchPublications($query),
            'events' => $this->searchEvents($query),
            'ideas' => $this->searchIdeas($query),
            'news' => $this->searchNews($query),
            'users' => $this->searchUsers($query)
        ];

        // Combine all results for the "All" tab
        $results['total'] = $this->formatSearchResults($results);

        $this->render('search/results', [
            'pageTitle' => 'Résultats de recherche: ' . $query,
            'query' => $query,
            'results' => $results
        ]);
    }

    /**
     * Format all search results for the "All" tab
     * @param array $results
     * @return array
     */
    private function formatSearchResults($results) {
        $formattedResults = [];

        // Projects
        foreach ($results['projects'] as $project) {
            $formattedResults[] = [
                'id' => $project['id'],
                'title' => $project['titre'],
                'excerpt' => $this->truncate($project['description'], 150),
                'date' => $project['dateDebut'],
                'author' => $project['chefPrenom'] . ' ' . $project['chefNom'],
                'url' => 'projects/' . $project['id'],
                'type' => 'project',
                'type_label' => 'Projet',
                'type_color' => 'primary'
            ];
        }

        // Publications
        foreach ($results['publications'] as $publication) {
            $formattedResults[] = [
                'id' => $publication['id'],
                'title' => $publication['titre'],
                'excerpt' => $this->truncate($publication['contenu'], 150),
                'date' => $publication['datePublication'],
                'author' => $publication['auteurPrenom'] . ' ' . $publication['auteurNom'],
                'url' => 'publications/' . $publication['id'],
                'type' => 'publication',
                'type_label' => $publication['type'] ?? 'Publication',
                'type_color' => 'success'
            ];
        }

        // Events
        foreach ($results['events'] as $event) {
            $formattedResults[] = [
                'id' => $event['id'],
                'title' => $event['titre'],
                'excerpt' => $this->truncate($event['description'], 150),
                'date' => $event['eventDate'] ?? $event['dateCreation'],
                'author' => $event['createurPrenom'] . ' ' . $event['createurNom'],
                'url' => 'events/' . $event['id'],
                'type' => 'event',
                'type_label' => $event['type'] ?? 'Événement',
                'type_color' => 'info'
            ];
        }

        // Ideas
        foreach ($results['ideas'] as $idea) {
            $formattedResults[] = [
                'id' => $idea['id'],
                'title' => $idea['titre'],
                'excerpt' => $this->truncate($idea['description'], 150),
                'date' => $idea['dateProposition'],
                'author' => $idea['proposerPrenom'] . ' ' . $idea['proposerNom'],
                'url' => 'ideas/' . $idea['id'],
                'type' => 'idea',
                'type_label' => 'Idée',
                'type_color' => 'warning'
            ];
        }

        // News
        foreach ($results['news'] as $news) {
            $formattedResults[] = [
                'id' => $news['id'],
                'title' => $news['titre'],
                'excerpt' => $this->truncate($news['contenu'], 150),
                'date' => $news['datePublication'],
                'author' => $news['auteurPrenom'] . ' ' . $news['auteurNom'],
                'url' => 'news/' . $news['id'],
                'type' => 'news',
                'type_label' => 'Actualité',
                'type_color' => 'danger'
            ];
        }

        // Sort by most recent date
        usort($formattedResults, function($a, $b) {
            $dateA = strtotime($a['date'] ?? '');
            $dateB = strtotime($b['date'] ?? '');
            return $dateB - $dateA;
        });

        return $formattedResults;
    }

    /**
     * Search projects
     * @param string $query
     * @return array
     */
    private function searchProjects($query) {
        $db = Db::getInstance();

        $sql = "
            SELECT p.*, u.nom as chefNom, u.prenom as chefPrenom
            FROM ProjetRecherche p
            LEFT JOIN Utilisateur u ON p.chefProjet = u.id
            WHERE p.titre LIKE :query OR p.description LIKE :query
            ORDER BY p.dateDebut DESC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute(['query' => '%' . $query . '%']);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Search publications
     * @param string $query
     * @return array
     */
    private function searchPublications($query) {
        $db = Db::getInstance();

        $sql = "
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
            WHERE p.titre LIKE :query OR p.contenu LIKE :query
            ORDER BY p.datePublication DESC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute(['query' => '%' . $query . '%']);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Search events
     * @param string $query
     * @return array
     */
    private function searchEvents($query) {
        $db = Db::getInstance();

        $sql = "
            SELECT e.*, u.nom as createurNom, u.prenom as createurPrenom,
                CASE 
                    WHEN s.evenementId IS NOT NULL THEN 'Seminaire' 
                    WHEN c.evenementId IS NOT NULL THEN 'Conference'
                    WHEN w.evenementId IS NOT NULL THEN 'Workshop'
                    ELSE 'Standard'
                END as type,
                COALESCE(s.date, c.dateDebut, w.dateDebut) as eventDate
            FROM Evenement e
            LEFT JOIN Seminaire s ON e.id = s.evenementId
            LEFT JOIN Conference c ON e.id = c.evenementId
            LEFT JOIN Workshop w ON e.id = w.evenementId
            LEFT JOIN Utilisateur u ON e.createurId = u.id
            WHERE e.titre LIKE :query OR e.description LIKE :query OR e.lieu LIKE :query
            ORDER BY eventDate DESC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute(['query' => '%' . $query . '%']);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Search research ideas
     * @param string $query
     * @return array
     */
    private function searchIdeas($query) {
        $db = Db::getInstance();

        $sql = "
            SELECT i.*, u.nom as proposerNom, u.prenom as proposerPrenom
            FROM IdeeRecherche i
            LEFT JOIN Utilisateur u ON i.proposePar = u.id
            WHERE i.titre LIKE :query OR i.description LIKE :query
            ORDER BY i.dateProposition DESC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute(['query' => '%' . $query . '%']);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Search news
     * @param string $query
     * @return array
     */
    private function searchNews($query) {
        $db = Db::getInstance();

        $sql = "
            SELECT a.*, u.nom as auteurNom, u.prenom as auteurPrenom
            FROM Actualite a
            LEFT JOIN Utilisateur u ON a.auteurId = u.id
            WHERE a.titre LIKE :query OR a.contenu LIKE :query
            ORDER BY a.datePublication DESC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute(['query' => '%' . $query . '%']);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Search users
     * @param string $query
     * @return array
     */
    private function searchUsers($query) {
        $db = Db::getInstance();

        $sql = "
            SELECT u.*, 
                CASE 
                    WHEN a.utilisateurId IS NOT NULL THEN 'Admin' 
                    WHEN c.utilisateurId IS NOT NULL THEN 'Chercheur'
                    WHEN m.utilisateurId IS NOT NULL THEN 'MembreBureauExecutif'
                    ELSE 'Standard'
                END as role
            FROM Utilisateur u
            LEFT JOIN Admin a ON u.id = a.utilisateurId
            LEFT JOIN Chercheur c ON u.id = c.utilisateurId
            LEFT JOIN MembreBureauExecutif m ON u.id = m.utilisateurId
            WHERE u.nom LIKE :query OR u.prenom LIKE :query OR u.email LIKE :query
            ORDER BY u.nom, u.prenom
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute(['query' => '%' . $query . '%']);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Truncate text to a specific length
     * @param string $text
     * @param int $length
     * @param string $suffix
     * @return string
     */
    private function truncate($text, $length = 100, $suffix = '...') {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . $suffix;
    }
}