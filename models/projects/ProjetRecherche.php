<?php
require_once './models/Model.php';

/**
 * ProjetRecherche Class
 */
class ProjetRecherche extends Model {
    protected $table = 'ProjetRecherche';

    /**
     * Get project with chef details
     * @param int $id
     * @return array|false
     */
    public function findWithChef($id) {
        $query = "
            SELECT p.*, u.nom as chefNom, u.prenom as chefPrenom
            FROM ProjetRecherche p
            LEFT JOIN Utilisateur u ON p.chefProjet = u.id
            WHERE p.id = :id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get projects by chef
     * @param int $chefProjet
     * @return array
     */
    public function getByChef($chefProjet) {
        return $this->where('chefProjet', $chefProjet);
    }

    /**
     * Get all participants for a project
     * @param int $projetId
     * @return array
     */
    public function getParticipants($projetId) {
        $query = "
            SELECT p.*, u.nom, u.prenom, u.email
            FROM Participe p
            JOIN Utilisateur u ON p.utilisateurId = u.id
            WHERE p.projetId = :projetId
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['projetId' => $projetId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all partners for a project
     * @param int $projetId
     * @return array
     */
    public function getPartners($projetId) {
        $query = "
            SELECT p.*
            FROM Partner p
            JOIN ProjetPartner pp ON p.id = pp.partnerId
            WHERE pp.projetId = :projetId
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['projetId' => $projetId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add participant to project
     * @param int $projetId
     * @param int $utilisateurId
     * @param string $role
     * @return int|false
     */
    public function addParticipant($projetId, $utilisateurId, $role = 'participant') {
        $participModel = new Participe();
        return $participModel->create([
            'projetId' => $projetId,
            'utilisateurId' => $utilisateurId,
            'role' => $role
        ]);
    }

    /**
     * Add partner to project
     * @param int $projetId
     * @param int $partnerId
     * @return bool
     */
    public function addPartner($projetId, $partnerId) {
        $db = Db::getInstance();
        $stmt = $db->prepare("INSERT INTO ProjetPartner (projetId, partnerId) VALUES (:projetId, :partnerId)");
        return $stmt->execute([
            'projetId' => $projetId,
            'partnerId' => $partnerId
        ]);
    }
}