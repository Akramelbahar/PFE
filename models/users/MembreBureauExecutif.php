<?php
require_once './models/users/Utilisateur.php';

/**
 * MembreBureauExecutif Class (inherits from Utilisateur)
 */
class MembreBureauExecutif extends Utilisateur {
    protected $table = 'MembreBureauExecutif';
    protected $primaryKey = 'utilisateurId';

    /**
     * Get a board member with user details
     * @param int $id
     * @return array|false
     */
    public function findWithDetails($id) {
        $query = "
            SELECT m.*, u.*, c.domaineRecherche, c.bio
            FROM MembreBureauExecutif m
            JOIN Utilisateur u ON m.utilisateurId = u.id
            LEFT JOIN Chercheur c ON m.chercheurId = c.utilisateurId
            WHERE m.utilisateurId = :id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create board member from existing user and possibly researcher
     * @param int $userId
     * @param string $role
     * @param float $mandat
     * @param string $permissions
     * @param int|null $chercheurId
     * @return int|false
     */
    public function createFromUser($userId, $role, $mandat, $permissions, $chercheurId = null) {
        return $this->create([
            'utilisateurId' => $userId,
            'role' => $role,
            'Mandat' => $mandat,
            'permissions' => $permissions,
            'chercheurId' => $chercheurId
        ]);
    }

    /**
     * Get all board members with user details
     * @return array
     */
    public function getAllWithUserDetails() {
        $query = "
            SELECT m.*, u.*, c.domaineRecherche, c.bio
            FROM MembreBureauExecutif m
            JOIN Utilisateur u ON m.utilisateurId = u.id
            LEFT JOIN Chercheur c ON m.chercheurId = c.utilisateurId
        ";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get by role
     * @param string $role
     * @return array
     */
    public function getByRole($role) {
        return $this->where('role', $role);
    }
}