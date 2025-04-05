<?php
require_once './models/Model.php';

/**
 * IdeeRecherche Class
 */
class IdeeRecherche extends Model {
    protected $table = 'IdeeRecherche';

    /**
     * Get idea with proposer details
     * @param int $id
     * @return array|false
     */
    public function findWithProposer($id) {
        $query = "
            SELECT i.*, u.nom as proposerNom, u.prenom as proposerPrenom
            FROM IdeeRecherche i
            LEFT JOIN Utilisateur u ON i.proposePar = u.id
            WHERE i.id = :id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get ideas by proposer
     * @param int $proposePar
     * @return array
     */
    public function getByProposer($proposePar) {
        return $this->where('proposePar', $proposePar);
    }

    /**
     * Get ideas by status
     * @param string $status
     * @return array
     */
    public function getByStatus($status) {
        return $this->where('status', $status);
    }

    /**
     * Update idea status
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus($id, $status) {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Get all ideas with proposer details
     * @return array
     */
    public function getAllWithProposerDetails() {
        $query = "
            SELECT i.*, u.nom as proposerNom, u.prenom as proposerPrenom
            FROM IdeeRecherche i
            LEFT JOIN Utilisateur u ON i.proposePar = u.id
        ";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}