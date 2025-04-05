<?php
require_once './models/users/Utilisateur.php';

/**
 * Chercheur Class (inherits from Utilisateur)
 */
class Chercheur extends Utilisateur {
    protected $table = 'Chercheur';
    protected $primaryKey = 'utilisateurId';

    /**
     * Get a researcher with user details
     * @param int $id
     * @return array|false
     */
    public function findWithDetails($id) {
        $query = "
            SELECT c.*, u.*
            FROM Chercheur c
            JOIN Utilisateur u ON c.utilisateurId = u.id
            WHERE c.utilisateurId = :id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create researcher from existing user
     * @param int $userId
     * @param string|null $domaineRecherche
     * @param string|null $bio
     * @return int|false
     */
    public function createFromUser($userId, $domaineRecherche = null, $bio = null) {
        return $this->create([
            'utilisateurId' => $userId,
            'domaineRecherche' => $domaineRecherche,
            'bio' => $bio
        ]);
    }

    /**
     * Get all researchers with user details
     * @return array
     */
    public function getAllWithUserDetails() {
        $query = "
            SELECT c.*, u.*
            FROM Chercheur c
            JOIN Utilisateur u ON c.utilisateurId = u.id
        ";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all projects for this researcher
     * @param int $userId
     * @return array
     */
    public function getProjects($userId) {
        $query = "
            SELECT pr.*
            FROM ProjetRecherche pr
            JOIN Participe p ON pr.id = p.projetId
            WHERE p.utilisateurId = :userId
            UNION
            SELECT pr.*
            FROM ProjetRecherche pr
            WHERE pr.chefProjet = :userId
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}