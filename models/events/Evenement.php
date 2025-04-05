<?php
require_once './models/Model.php';

/**
 * Evenement Base Class
 */
class Evenement extends Model {
    protected $table = 'Evenement';

    /**
     * Get event with creator details
     * @param int $id
     * @return array|false
     */
    public function findWithCreator($id) {
        $query = "
            SELECT e.*, u.nom as createurNom, u.prenom as createurPrenom
            FROM Evenement e
            LEFT JOIN Utilisateur u ON e.createurId = u.id
            WHERE e.id = :id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get events by project
     * @param int $projetId
     * @return array
     */
    public function getByProject($projetId) {
        return $this->where('projetId', $projetId);
    }

    /**
     * Get events by creator
     * @param int $createurId
     * @return array
     */
    public function getByCreator($createurId) {
        return $this->where('createurId', $createurId);
    }

    /**
     * Get all events with their specific types
     * @return array
     */
    public function getAllWithTypes() {
        $query = "
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
            ORDER BY e.dateCreation DESC
        ";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get upcoming events
     * @param int $limit Number of events to retrieve
     * @return array
     */
    public function getUpcomingEvents($limit = 5) {
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

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':final_limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}