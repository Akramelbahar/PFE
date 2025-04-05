<?php
require_once './models/events/Evenement.php';

/**
 * Workshop Class (inherits from Evenement)
 */
class Workshop extends Evenement {
    protected $table = 'Workshop';
    protected $primaryKey = 'evenementId';

    /**
     * Create workshop from existing event
     * @param int $evenementId
     * @param int|null $instructorId
     * @param string $dateDebut
     * @param string $dateFin
     * @return int|false
     */
    public function createFromEvent($evenementId, $instructorId, $dateDebut, $dateFin) {
        return $this->create([
            'evenementId' => $evenementId,
            'instructorId' => $instructorId,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin
        ]);
    }

    /**
     * Get all workshops with event details
     * @return array
     */
    public function getAllWithDetails() {
        $query = "
            SELECT w.*, e.*,
                   u1.nom as createurNom, u1.prenom as createurPrenom,
                   u2.nom as instructorNom, u2.prenom as instructorPrenom
            FROM Workshop w
            JOIN Evenement e ON w.evenementId = e.id
            LEFT JOIN Utilisateur u1 ON e.createurId = u1.id
            LEFT JOIN Utilisateur u2 ON w.instructorId = u2.id
            ORDER BY w.dateDebut DESC
        ";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find workshop by ID with additional details
     * @param int $id
     * @return array|false
     */
    public function findWithDetails($id) {
        $query = "
            SELECT w.*, e.*,
                   u1.nom as createurNom, u1.prenom as createurPrenom,
                   u2.nom as instructorNom, u2.prenom as instructorPrenom
            FROM Workshop w
            JOIN Evenement e ON w.evenementId = e.id
            LEFT JOIN Utilisateur u1 ON e.createurId = u1.id
            LEFT JOIN Utilisateur u2 ON w.instructorId = u2.id
            WHERE w.evenementId = :id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}