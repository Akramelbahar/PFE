<?php
require_once './models/events/Evenement.php';

/**
 * Seminaire Class (inherits from Evenement)
 */
class Seminaire extends Evenement {
    protected $table = 'Seminaire';
    protected $primaryKey = 'evenementId';

    /**
     * Create seminar from existing event
     * @param int $evenementId
     * @param string $date
     * @return int|false
     */
    public function createFromEvent($evenementId, $date) {
        return $this->create([
            'evenementId' => $evenementId,
            'date' => $date
        ]);
    }

    /**
     * Get all seminars with event details
     * @return array
     */
    public function getAllWithDetails() {
        $query = "
            SELECT s.*, e.*, u.nom as createurNom, u.prenom as createurPrenom
            FROM Seminaire s
            JOIN Evenement e ON s.evenementId = e.id
            LEFT JOIN Utilisateur u ON e.createurId = u.id
        ";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}