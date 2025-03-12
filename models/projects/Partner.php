<?php
require_once './models/Model.php';

/**
 * Partner Class
 */
class Partner extends Model {
    protected $table = 'Partner';

    /**
     * Get all projects for this partner
     * @param int $partnerId
     * @return array
     */
    public function getProjects($partnerId) {
        $query = "
            SELECT pr.*
            FROM ProjetRecherche pr
            JOIN ProjetPartner pp ON pr.id = pp.projetId
            WHERE pp.partnerId = :partnerId
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['partnerId' => $partnerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add partner to a project
     * @param int $partnerId
     * @param int $projetId
     * @return bool
     */
    public function addToProject($partnerId, $projetId) {
        $db = Db::getInstance();
        $stmt = $db->prepare("INSERT INTO ProjetPartner (partnerId, projetId) VALUES (:partnerId, :projetId)");
        return $stmt->execute([
            'partnerId' => $partnerId,
            'projetId' => $projetId
        ]);
    }

    /**
     * Remove partner from a project
     * @param int $partnerId
     * @param int $projetId
     * @return bool
     */
    public function removeFromProject($partnerId, $projetId) {
        $db = Db::getInstance();
        $stmt = $db->prepare("DELETE FROM ProjetPartner WHERE partnerId = :partnerId AND projetId = :projetId");
        return $stmt->execute([
            'partnerId' => $partnerId,
            'projetId' => $projetId
        ]);
    }
}