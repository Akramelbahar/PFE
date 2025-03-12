<?php
require_once './models/Model.php';

/**
 * Participe Class
 */
class Participe extends Model {
    protected $table = 'Participe';

    /**
     * Get all participants for a project with user details
     * @param int $projetId
     * @return array
     */
    public function getAllByProject($projetId) {
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
     * Get all projects for a user
     * @param int $utilisateurId
     * @return array
     */
    public function getAllByUser($utilisateurId) {
        $query = "
            SELECT p.*, pr.titre, pr.description, pr.dateDebut, pr.dateFin
            FROM Participe p
            JOIN ProjetRecherche pr ON p.projetId = pr.id
            WHERE p.utilisateurId = :utilisateurId
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['utilisateurId' => $utilisateurId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if a user is a participant in a project
     * @param int $projetId
     * @param int $utilisateurId
     * @return bool
     */
    public function isParticipant($projetId, $utilisateurId) {
        $query = "
            SELECT COUNT(*) 
            FROM Participe 
            WHERE projetId = :projetId AND utilisateurId = :utilisateurId
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'projetId' => $projetId,
            'utilisateurId' => $utilisateurId
        ]);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Remove a participant from a project
     * @param int $projetId
     * @param int $utilisateurId
     * @return bool
     */
    public function removeParticipant($projetId, $utilisateurId) {
        $query = "
            DELETE FROM Participe 
            WHERE projetId = :projetId AND utilisateurId = :utilisateurId
        ";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'projetId' => $projetId,
            'utilisateurId' => $utilisateurId
        ]);
    }
}