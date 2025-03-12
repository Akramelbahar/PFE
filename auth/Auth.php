<?php
/**
 * Authentication and Authorization Class
 * Manages user authentication and permission checking for the association app
 */
class Auth {
    private static $instance = null;
    private $db;
    private $user = null;
    private $roles = [];

    /**
     * Constructor - Private for singleton pattern
     */
    private function __construct() {
        $this->db = Db::getInstance();
        $this->checkSession();
    }

    /**
     * Prevent cloning of the singleton
     */
    private function __clone() {}

    /**
     * Get singleton instance
     * @return Auth
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Check if there is an active session and load user data
     */
    private function checkSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            $stmt = $this->db->prepare("SELECT * FROM Utilisateur WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $_SESSION['user_id']]);
            $this->user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($this->user) {
                $this->loadRoles($_SESSION['user_id']);
            }
        }
    }

    /**
     * Load user roles
     * @param int $userId
     */
    private function loadRoles($userId) {
        $this->roles = [];

        // Check if user is admin
        $stmt = $this->db->prepare("SELECT * FROM Admin WHERE utilisateurId = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->roles[] = 'admin';
        }

        // Check if user is researcher
        $stmt = $this->db->prepare("SELECT * FROM Chercheur WHERE utilisateurId = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->roles[] = 'chercheur';
        }

        // Check if user is executive board member
        $stmt = $this->db->prepare("SELECT * FROM MembreBureauExecutif WHERE utilisateurId = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);
        $membreBureau = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($membreBureau) {
            $this->roles[] = 'membreBureauExecutif';
            $this->roles[] = strtolower($membreBureau['role']); // Add specific role
        }
    }

    /**
     * Login user
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM Utilisateur WHERE email = :email AND status = 1 LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['motDePasse'])) {
            // Start session if not started
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
            $_SESSION['user_email'] = $user['email'];

            // Update last login
            $stmt = $this->db->prepare("UPDATE Utilisateur SET derniereConnexion = NOW() WHERE id = :id");
            $stmt->execute(['id' => $user['id']]);

            // Load user data
            $this->user = $user;
            $this->loadRoles($user['id']);

            return true;
        }

        return false;
    }

    /**
     * Logout user
     */
    public function logout() {
        // Start session if not started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Clear session variables
        $_SESSION = [];

        // Destroy session
        session_destroy();

        // Clear user data
        $this->user = null;
        $this->roles = [];
    }

    /**
     * Register a new user
     * @param array $data
     * @return int|bool User ID if successful, false otherwise
     */
    public function register($data) {
        // Validate email is unique
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM Utilisateur WHERE email = :email");
        $stmt->execute(['email' => $data['email']]);
        if ($stmt->fetchColumn() > 0) {
            return false;
        }

        // Hash password
        $data['motDePasse'] = password_hash($data['motDePasse'], PASSWORD_DEFAULT);

        // Set default values
        $data['dateInscription'] = date('Y-m-d H:i:s');
        $data['status'] = 1;

        // Insert user
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $stmt = $this->db->prepare("INSERT INTO Utilisateur ({$columns}) VALUES ({$placeholders})");
        if ($stmt->execute($data)) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Check if user is logged in
     * @return bool
     */
    public function isLoggedIn() {
        return $this->user !== null;
    }

    /**
     * Check if current user has a specific role
     * @param string|array $role
     * @return bool
     */
    public function hasRole($role) {
        if (!$this->isLoggedIn()) {
            return false;
        }

        if (is_array($role)) {
            return count(array_intersect($role, $this->roles)) > 0;
        }

        return in_array($role, $this->roles);
    }

    /**
     * Check if user has permission to access a resource or perform an action
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission) {
        if (!$this->isLoggedIn()) {
            return false;
        }

        // Admin has all permissions
        if (in_array('admin', $this->roles)) {
            return true;
        }

        // Check for executive board member with specific permissions
        if (in_array('membreBureauExecutif', $this->roles)) {
            $stmt = $this->db->prepare("SELECT role, permissions FROM MembreBureauExecutif WHERE utilisateurId = :id LIMIT 1");
            $stmt->execute(['id' => $this->user['id']]);
            $membreBureau = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($membreBureau) {
                // Check if member has all permissions (wildcard)
                if (strpos($membreBureau['permissions'], '*') !== false) {
                    return true;
                }

                // Check specific permissions
                $permissions = explode(',', $membreBureau['permissions']);
                if (in_array($permission, $permissions)) {
                    return true;
                }

                // President and Vice President have all permissions
                if (in_array(strtolower($membreBureau['role']), ['president', 'vicepresident'])) {
                    return true;
                }
            }
        }

        // Define role-based permissions
        $rolePermissions = [
            'chercheur' => [
                'view_publications', 'add_publication', 'edit_own_publication', 'delete_own_publication',
                'view_events', 'register_event', 'propose_idea',
                'view_projects', 'participate_project'
            ]
        ];

        // Check if any of the user's roles have the required permission
        foreach ($this->roles as $userRole) {
            if (isset($rolePermissions[$userRole]) && in_array($permission, $rolePermissions[$userRole])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get current logged in user
     * @return array|null
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Get user ID
     * @return int|null
     */
    public function getUserId() {
        return $this->user ? $this->user['id'] : null;
    }

    /**
     * Get user roles
     * @return array
     */
    public function getRoles() {
        return $this->roles;
    }

    /**
     * Get user by ID
     * @param int $id
     * @return array|false
     */
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM Utilisateur WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check if a user exists by email
     * @param string $email
     * @return bool
     */
    public function userExists($email) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM Utilisateur WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Get bureau role for the current user
     * @return string|null Role name or null if not a bureau member
     */
    public function getBureauRole() {
        if (!$this->isLoggedIn() || !$this->hasRole('membreBureauExecutif')) {
            return null;
        }

        $stmt = $this->db->prepare("SELECT role FROM MembreBureauExecutif WHERE utilisateurId = :id LIMIT 1");
        $stmt->execute(['id' => $this->user['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['role'] : null;
    }

    /**
     * Check if current user has a specific bureau role
     * @param string|array $role Bureau role(s) to check
     * @return bool
     */
    public function hasBureauRole($role) {
        $bureauRole = $this->getBureauRole();
        if (!$bureauRole) {
            return false;
        }

        if (is_array($role)) {
            return in_array($bureauRole, $role);
        }

        return $bureauRole === $role;
    }

    /**
     * Get human-readable role name for display
     * @param string $role Machine role name
     * @return string Human-readable role name
     */
    public function getBureauRoleLabel($role) {
        $roleLabels = [
            'President' => 'Président',
            'VicePresident' => 'Vice-président',
            'GeneralSecretary' => 'Secrétaire Général',
            'Treasurer' => 'Trésorier',
            'ViceTreasurer' => 'Vice-trésorier',
            'Counselor' => 'Conseiller'
        ];

        return $roleLabels[$role] ?? $role;
    }

    /**
     * Get all permissions for current user
     * @return array
     */
    public function getAllPermissions() {
        if (!$this->isLoggedIn()) {
            return [];
        }

        // Admin has all permissions
        if (in_array('admin', $this->roles)) {
            return ['*'];
        }

        $permissions = [];

        // Check for executive board member permissions
        if (in_array('membreBureauExecutif', $this->roles)) {
            $stmt = $this->db->prepare("
                SELECT role, permissions 
                FROM MembreBureauExecutif 
                WHERE utilisateurId = :id LIMIT 1
            ");
            $stmt->execute(['id' => $this->user['id']]);
            $membreBureau = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($membreBureau) {
                // Check if member has wildcard permissions
                if (strpos($membreBureau['permissions'], '*') !== false) {
                    return ['*'];
                }

                // Get specific permissions
                $bureauPermissions = explode(',', $membreBureau['permissions']);
                $permissions = array_merge($permissions, $bureauPermissions);

                // President and Vice President have all permissions
                if (in_array(strtolower($membreBureau['role']), ['president', 'vicepresident'])) {
                    return ['*'];
                }
            }
        }

        // Add role-based permissions
        $rolePermissions = [
            'chercheur' => [
                'view_publications', 'add_publication', 'edit_own_publication', 'delete_own_publication',
                'view_events', 'register_event', 'propose_idea',
                'view_projects', 'participate_project'
            ]
        ];

        foreach ($this->roles as $userRole) {
            if (isset($rolePermissions[$userRole])) {
                $permissions = array_merge($permissions, $rolePermissions[$userRole]);
            }
        }

        return array_unique($permissions);
    }
}