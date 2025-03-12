<?php
require_once './models/Model.php';

/**
 * Utilisateur Base Class
 */
class Utilisateur extends Model {
    protected $table = 'Utilisateur';

    /**
     * Get user's full name
     * @param int $id
     * @return string
     */
    public function getFullName($id = null) {
        $userId = $id ?? $this->id;
        $user = $this->find($userId);
        return $user ? $user['prenom'] . ' ' . $user['nom'] : '';
    }

    /**
     * Update user's last login time
     * @param int $id
     * @return bool
     */
    public function updateLastLogin($id) {
        return $this->update($id, ['derniereConnexion' => date('Y-m-d H:i:s')]);
    }

    /**
     * Find user by email
     * @param string $email
     * @return array|false
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all users with their specific roles
     * @return array
     */
    public function getAllWithRoles() {
        $query = "
            SELECT u.*, 
                CASE 
                    WHEN a.utilisateurId IS NOT NULL THEN 'Admin' 
                    WHEN c.utilisateurId IS NOT NULL THEN 'Chercheur'
                    WHEN m.utilisateurId IS NOT NULL THEN 'MembreBureauExecutif'
                    ELSE 'Standard'
                END as role
            FROM Utilisateur u
            LEFT JOIN Admin a ON u.id = a.utilisateurId
            LEFT JOIN Chercheur c ON u.id = c.utilisateurId
            LEFT JOIN MembreBureauExecutif m ON u.id = m.utilisateurId
        ";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verify user password
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function verifyPassword($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['motDePasse'])) {
            return $user;
        }
        return false;
    }

    /**
     * Register a new user
     * @param array $data
     * @return int|false
     */
    public function register($data) {
        // Hash the password
        $data['motDePasse'] = password_hash($data['motDePasse'], PASSWORD_DEFAULT);

        // Set default values if not provided
        $data['dateInscription'] = $data['dateInscription'] ?? date('Y-m-d H:i:s');
        $data['status'] = $data['status'] ?? true;

        return $this->create($data);
    }
}