<?php
require_once './models/events/Evenement.php';

/**
 * Conference Class (inherits from Evenement)
 */
class Conference extends Evenement {
    protected $table = 'Conference';
    protected $primaryKey = 'evenementId';

    /**
     * Create conference from existing event
     * @param int $evenementId
     * @param string $dateDebut
     * @param string $dateFin
     * @return int|false
     */
    public function createFromEvent($evenementId, $dateDebut, $dateFin) {
        return $this->create([
            'evenementId' => $evenementId,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin
        ]);
    }

    /**
     * Get all conferences with event details
     * @return array
     */
    public function getAllWithDetails() {
        $query = "
            SELECT c.*, e.*, 
                   u.nom as createurNom, 
                   u.prenom as createurPrenom
            FROM Conference c
            JOIN Evenement e ON c.evenementId = e.id
            LEFT JOIN Utilisateur u ON e.createurId = u.id
            ORDER BY c.dateDebut DESC
        ";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find conference by ID with additional details
     * @param int $id
     * @return array|false
     */
    public function findWithDetails($id) {
        $query = "
            SELECT c.*, e.*, 
                   u.nom as createurNom, 
                   u.prenom as createurPrenom
            FROM Conference c
            JOIN Evenement e ON c.evenementId = e.id
            LEFT JOIN Utilisateur u ON e.createurId = u.id
            WHERE c.evenementId = :id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}