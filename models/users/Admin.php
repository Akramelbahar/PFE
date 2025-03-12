<?php
require_once './models/users/Utilisateur.php';

/**
 * Admin Class (inherits from Utilisateur)
 */
class Admin extends Utilisateur {
    protected $table = 'Admin';
    protected $primaryKey = 'utilisateurId';

    /**
     * Get an admin with user details
     * @param int $id
     * @return array|false
     */
    public function findWithDetails($id) {
        $query = "
            SELECT a.*, u.*
            FROM Admin a
            JOIN Utilisateur u ON a.utilisateurId = u.id
            WHERE a.utilisateurId = :id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create admin from existing user
     * @param int $userId
     * @return int|false
     */
    public function createFromUser($userId) {
        return $this->create(['utilisateurId' => $userId]);
    }

    /**
     * Get all admins with user details
     * @return array
     */
    public function getAllWithUserDetails() {
        $query = "
            SELECT a.*, u.*
            FROM Admin a
            JOIN Utilisateur u ON a.utilisateurId = u.id
        ";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}