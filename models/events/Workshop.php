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
        ";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}