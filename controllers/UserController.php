<?php
require_once './core/Controller.php';
require_once './models/users/Utilisateur.php';
require_once './models/users/Chercheur.php';
require_once './models/users/MembreBureauExecutif.php';
require_once './models/users/Admin.php';
require_once './utils/FileManager.php';
require_once './utils/PermissionsHelper.php';

/**
 * User Controller
 */
class UserController extends Controller {
    protected $db;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->db = Db::getInstance();
    }

    /**
     * User profile page
     */
    public function profile() {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        // Get current user
        $user = $this->auth->getUser();

        // Get user details based on role
        $userDetails = null;
        $userType = 'standard';

        if ($this->auth->hasRole('chercheur')) {
            $chercheurModel = new Chercheur();
            $userDetails = $chercheurModel->findWithDetails($user['id']);
            $userType = 'chercheur';
        } elseif ($this->auth->hasRole('membreBureauExecutif')) {
            $membreModel = new MembreBureauExecutif();
            $userDetails = $membreModel->findWithDetails($user['id']);
            $userType = 'membreBureauExecutif';
        } elseif ($this->auth->hasRole('admin')) {
            $adminModel = new Admin();
            $userDetails = $adminModel->findWithDetails($user['id']);
            $userType = 'admin';
        }

        $this->render('user/profile', [
            'pageTitle' => 'Mon profil',
            'user' => $user,
            'userDetails' => $userDetails,
            'userType' => $userType
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile() {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        // Check if form was submitted
        if (!$this->isPost()) {
            $this->redirect('profile');
            return;
        }

        // Get current user
        $user = $this->auth->getUser();

        // Get form data
        $nom = $this->getInput('nom');
        $prenom = $this->getInput('prenom');
        $email = $this->getInput('email');
        $currentPassword = $this->getInput('current_password');
        $newPassword = $this->getInput('new_password');
        $newPasswordConfirm = $this->getInput('new_password_confirm');

        // Prepare data to update
        $userData = [
            'nom' => $nom,
            'prenom' => $prenom
        ];

        // Validate basic info
        $validation = $this->validate(
            ['nom' => $nom, 'prenom' => $prenom],
            ['nom' => 'required|max:255', 'prenom' => 'required|max:255']
        );

        if ($validation !== true) {
            $this->render('user/profile', [
                'pageTitle' => 'Mon profil',
                'user' => $user,
                'errors' => $validation
            ]);
            return;
        }

        // Check if email is being changed
        if ($email !== $user['email']) {
            // Validate email
            $emailValidation = $this->validate(
                ['email' => $email],
                ['email' => 'required|email|unique:Utilisateur,email']
            );

            if ($emailValidation !== true) {
                $this->render('user/profile', [
                    'pageTitle' => 'Mon profil',
                    'user' => $user,
                    'errors' => $emailValidation
                ]);
                return;
            }

            $userData['email'] = $email;
        }

        // Check if password is being changed
        if (!empty($currentPassword) || !empty($newPassword) || !empty($newPasswordConfirm)) {
            // Validate password
            $passwordValidation = $this->validate(
                [
                    'current_password' => $currentPassword,
                    'new_password' => $newPassword,
                    'new_password_confirm' => $newPasswordConfirm
                ],
                [
                    'current_password' => 'required',
                    'new_password' => 'required|min:6',
                    'new_password_confirm' => 'required|same:new_password'
                ]
            );

            if ($passwordValidation !== true) {
                $this->render('user/profile', [
                    'pageTitle' => 'Mon profil',
                    'user' => $user,
                    'errors' => $passwordValidation
                ]);
                return;
            }

            // Verify current password
            if (!password_verify($currentPassword, $user['motDePasse'])) {
                $this->render('user/profile', [
                    'pageTitle' => 'Mon profil',
                    'user' => $user,
                    'errorMessage' => 'Le mot de passe actuel est incorrect.'
                ]);
                return;
            }

            // Hash new password
            $userData['motDePasse'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        // Check if there's a profile picture upload
        $profilePicture = $this->getFile('profile_picture');

        if ($profilePicture && $profilePicture['error'] === UPLOAD_ERR_OK) {
            $fileManager = new FileManager('uploads/profile_pictures/');
            $uploadResult = $fileManager->upload($profilePicture);

            if ($uploadResult) {
                $userData['profilePicture'] = $uploadResult['filename'];
            }
        }

        // Update user data
        $utilisateurModel = new Utilisateur();
        $updated = $utilisateurModel->update($user['id'], $userData);

        // Update role-specific data if needed
        if ($updated && $this->auth->hasRole('chercheur')) {
            $domaineRecherche = $this->getInput('domaine_recherche');
            $bio = $this->getInput('bio');

            $chercheurModel = new Chercheur();
            $chercheurModel->update($user['id'], [
                'domaineRecherche' => $domaineRecherche,
                'bio' => $bio
            ]);
        }

        if ($updated) {
            $this->setFlash('success', 'Votre profil a été mis à jour avec succès.');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de la mise à jour de votre profil.');
        }

        $this->redirect('profile');
    }

    /**
     * View another user's profile
     * @param int $id User ID
     */
    public function view($id) {
        // Ensure user is authenticated
        if (!$this->requireAuth()) {
            return;
        }

        // Get user
        $utilisateurModel = new Utilisateur();
        $user = $utilisateurModel->find($id);

        if (!$user) {
            $this->renderNotFound();
            return;
        }

        // Get user details based on role
        $userDetails = null;
        $userType = 'standard';
        $roles = [];

        // Check if user is a researcher
        $chercheurModel = new Chercheur();
        $chercheurDetails = $chercheurModel->findWithDetails($id);

        if ($chercheurDetails) {
            $userDetails = $chercheurDetails;
            $userType = 'chercheur';
            $roles[] = 'chercheur';
        }

        // Check if user is a board member
        $membreModel = new MembreBureauExecutif();
        $membreDetails = $membreModel->findWithDetails($id);

        if ($membreDetails) {
            $userDetails = $membreDetails;
            $userType = 'membreBureauExecutif';
            $roles[] = 'membreBureauExecutif';
            $roles[] = strtolower($membreDetails['role']);
        }

        // Check if user is an admin
        $adminModel = new Admin();
        $adminDetails = $adminModel->findWithDetails($id);

        if ($adminDetails) {
            $userType = 'admin';
            $roles[] = 'admin';
        }

        $this->render('user/view', [
            'pageTitle' => $user['prenom'] . ' ' . $user['nom'],
            'user' => $user,
            'userDetails' => $userDetails,
            'userType' => $userType,
            'roles' => $roles
        ]);
    }

    /**
     * List all users (admin only)
     */
    public function index() {
        // Ensure user is an admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Get filter parameters
        $role = $this->getInput('role');
        $status = $this->getInput('status');
        $search = $this->getInput('search');

        // Get users query
        $query = "
        SELECT u.*, 
            CASE 
                WHEN a.utilisateurId IS NOT NULL THEN 'admin' 
                WHEN c.utilisateurId IS NOT NULL THEN 'chercheur'
                WHEN m.utilisateurId IS NOT NULL THEN 'membreBureauExecutif'
                ELSE 'standard'
            END as role
        FROM Utilisateur u
        LEFT JOIN Admin a ON u.id = a.utilisateurId
        LEFT JOIN Chercheur c ON u.id = c.utilisateurId
        LEFT JOIN MembreBureauExecutif m ON u.id = m.utilisateurId
        WHERE 1=1
        ";

        $params = [];
        $conditions = [];

        // Apply role filter
        if (!empty($role)) {
            switch ($role) {
                case 'admin':
                    $conditions[] = "a.utilisateurId IS NOT NULL";
                    break;
                case 'chercheur':
                    $conditions[] = "c.utilisateurId IS NOT NULL";
                    break;
                case 'membreBureauExecutif':
                    $conditions[] = "m.utilisateurId IS NOT NULL";
                    break;
            }
        }

        // Apply status filter
        if ($status !== null && $status !== '') {
            $conditions[] = "u.status = :status";
            $params['status'] = (int)$status;
        }

        // Apply search filter
        if (!empty($search)) {
            $conditions[] = "(u.nom LIKE :search OR u.prenom LIKE :search OR u.email LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        // Combine conditions
        if (!empty($conditions)) {
            $query .= " AND " . implode(" AND ", $conditions);
        }

        // Order by registration date
        $query .= " ORDER BY u.dateInscription DESC";

        // Prepare and execute query
        $stmt = $this->db->prepare($query);

        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Enrich users with additional roles
        foreach ($users as &$user) {
            // Fetch detailed roles
            $userRoles = [];

            // Check admin
            $stmt = $this->db->prepare("SELECT * FROM Admin WHERE utilisateurId = :id");
            $stmt->execute(['id' => $user['id']]);
            if ($stmt->fetch()) {
                $userRoles[] = 'admin';
            }

            // Check researcher
            $stmt = $this->db->prepare("SELECT * FROM Chercheur WHERE utilisateurId = :id");
            $stmt->execute(['id' => $user['id']]);
            if ($stmt->fetch()) {
                $userRoles[] = 'chercheur';
            }

            // Check executive board member
            $stmt = $this->db->prepare("SELECT * FROM MembreBureauExecutif WHERE utilisateurId = :id");
            $stmt->execute(['id' => $user['id']]);
            $membreBureau = $stmt->fetch();
            if ($membreBureau) {
                $userRoles[] = 'membreBureauExecutif';
                // Add specific board member role if exists
                if (!empty($membreBureau['role'])) {
                    $userRoles[] = strtolower($membreBureau['role']);
                }
            }

            $user['roles'] = $userRoles;
        }

        $this->render('admin/users', [
            'pageTitle' => 'Gestion des utilisateurs',
            'users' => $users,
            'filters' => [
                'role' => $role,
                'status' => $status,
                'search' => $search
            ]
        ]);
    }

    /**
     * Edit user form (admin only)
     * @param int $id User ID
     */
    public function edit($id) {
        // Ensure user is an admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Get user
        $utilisateurModel = new Utilisateur();
        $user = $utilisateurModel->find($id);

        if (!$user) {
            $this->renderNotFound();
            return;
        }

        // Get user details based on role
        $userDetails = [];
        $userRoles = [];

        // Check if user is a researcher
        $chercheurModel = new Chercheur();
        $chercheurDetails = $chercheurModel->findWithDetails($id);

        if ($chercheurDetails) {
            $userDetails['chercheur'] = $chercheurDetails;
            $userRoles[] = 'chercheur';
        }

        // Check if user is a board member
        $membreModel = new MembreBureauExecutif();
        $membreDetails = $membreModel->findWithDetails($id);

        if ($membreDetails) {
            $userDetails['membreBureauExecutif'] = $membreDetails;
            $userRoles[] = 'membreBureauExecutif';
        }

        // Check if user is an admin
        $adminModel = new Admin();
        $adminDetails = $adminModel->findWithDetails($id);

        if ($adminDetails) {
            $userDetails['admin'] = $adminDetails;
            $userRoles[] = 'admin';
        }

        $this->render('user/edit', [
            'pageTitle' => 'Modifier l\'utilisateur',
            'user' => $user,
            'userDetails' => $userDetails,
            'userRoles' => $userRoles
        ]);
    }

    /**
     * Process user edit form (admin only)
     * @param int $id User ID
     */
    public function update($id) {
        // Ensure user is an admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Check if form was submitted
        if (!$this->isPost()) {
            $this->redirect('admin/users/edit/' . $id);
            return;
        }

        // Get user
        $utilisateurModel = new Utilisateur();
        $user = $utilisateurModel->find($id);

        if (!$user) {
            $this->renderNotFound();
            return;
        }

        // Get form data
        $nom = $this->getInput('nom');
        $prenom = $this->getInput('prenom');
        $email = $this->getInput('email');
        $status = $this->getInput('status') === 'on' ? 1 : 0;
        $roles = $this->getInput('roles', []);

        // Prepare data to update
        $userData = [
            'nom' => $nom,
            'prenom' => $prenom,
            'status' => $status
        ];

        // Validate basic info
        $validation = $this->validate(
            ['nom' => $nom, 'prenom' => $prenom],
            ['nom' => 'required|max:255', 'prenom' => 'required|max:255']
        );

        if ($validation !== true) {
            $this->setFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
            $this->redirect('admin/users/edit/' . $id);
            return;
        }

        // Check if email is being changed
        if ($email !== $user['email']) {
            // Validate email
            $emailValidation = $this->validate(
                ['email' => $email],
                ['email' => 'required|email|unique:Utilisateur,email']
            );

            if ($emailValidation !== true) {
                $this->setFlash('error', 'L\'adresse email est invalide ou déjà utilisée.');
                $this->redirect('admin/users/edit/' . $id);
                return;
            }

            $userData['email'] = $email;
        }

        // Check if password is being changed
        $password = $this->getInput('password');
        if (!empty($password)) {
            // Validate password
            $passwordValidation = $this->validate(
                ['password' => $password],
                ['password' => 'required|min:6']
            );

            if ($passwordValidation !== true) {
                $this->setFlash('error', 'Le mot de passe doit contenir au moins 6 caractères.');
                $this->redirect('admin/users/edit/' . $id);
                return;
            }

            // Hash new password
            $userData['motDePasse'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Update user data
        $updated = $utilisateurModel->update($id, $userData);

        if (!$updated) {
            $this->setFlash('error', 'Une erreur est survenue lors de la mise à jour de l\'utilisateur.');
            $this->redirect('admin/users/edit/' . $id);
            return;
        }

        // Handle roles
        $this->handleUserRoles($id, $roles);

        $this->setFlash('success', 'L\'utilisateur a été mis à jour avec succès.');
        $this->redirect('admin/users');
    }

    /**
     * Create new user (admin only)
     */
    public function create() {
        // Ensure user is an admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        $this->render('user/create', [
            'pageTitle' => 'Créer un utilisateur'
        ]);
    }

    /**
     * Process user creation form (admin only)
     */
    public function store() {
        // Ensure user is an admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Check if form was submitted
        if (!$this->isPost()) {
            $this->redirect('admin/users/create');
            return;
        }

        // Get form data
        $nom = $this->getInput('nom');
        $prenom = $this->getInput('prenom');
        $email = $this->getInput('email');
        $password = $this->getInput('password');
        $role = $this->getInput('role');

        // Validate input
        $validation = $this->validate(
            [
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'password' => $password,
                'role' => $role
            ],
            [
                'nom' => 'required|max:255',
                'prenom' => 'required|max:255',
                'email' => 'required|email|unique:Utilisateur,email',
                'password' => 'required|min:6',
                'role' => 'required'
            ]
        );

        if ($validation !== true) {
            $this->render('user/create', [
                'pageTitle' => 'Créer un utilisateur',
                'errors' => $validation,
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'role' => $role
            ]);
            return;
        }

        // Create user
        $utilisateurModel = new Utilisateur();
        $userId = $utilisateurModel->register([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'motDePasse' => $password
        ]);

        if (!$userId) {
            $this->render('user/create', [
                'pageTitle' => 'Créer un utilisateur',
                'errorMessage' => 'Une erreur est survenue lors de la création de l\'utilisateur.',
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'role' => $role
            ]);
            return;
        }

        // Set user role
        switch ($role) {
            case 'admin':
                $adminModel = new Admin();
                $adminModel->createFromUser($userId);
                break;

            case 'chercheur':
                $domaineRecherche = $this->getInput('domaine_recherche');
                $bio = $this->getInput('bio');

                $chercheurModel = new Chercheur();
                $chercheurModel->createFromUser($userId, $domaineRecherche, $bio);
                break;

            case 'membreBureauExecutif':
                $membreRole = $this->getInput('membre_role');
                $mandat = $this->getInput('mandat');
                $permissions = implode(',', $this->getInput('permissions', []));
                $isChercheur = $this->getInput('is_chercheur') === 'on';

                if ($isChercheur) {
                    $domaineRecherche = $this->getInput('chercheur_domaine');
                    $bio = $this->getInput('chercheur_bio');

                    $chercheurModel = new Chercheur();
                    $chercheurId = $chercheurModel->createFromUser($userId, $domaineRecherche, $bio);
                } else {
                    $chercheurId = null;
                }

                $membreModel = new MembreBureauExecutif();
                $membreModel->createFromUser($userId, $membreRole, $mandat, $permissions, $chercheurId);
                break;
        }

        $this->setFlash('success', 'L\'utilisateur a été créé avec succès.');
        $this->redirect('admin/users');
    }

    /**
     * Delete user (admin only)
     * @param int $id User ID
     */
    public function delete($id) {
        // Ensure user is an admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Get user
        $utilisateurModel = new Utilisateur();
        $user = $utilisateurModel->find($id);

        if (!$user) {
            $this->renderNotFound();
            return;
        }

        // Current user cannot delete themselves
        if ($user['id'] === $this->auth->getUser()['id']) {
            $this->setFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            $this->redirect('admin/users');
            return;
        }

        // Delete user
        $deleted = $utilisateurModel->delete($id);

        if ($deleted) {
            $this->setFlash('success', 'L\'utilisateur a été supprimé avec succès.');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de la suppression de l\'utilisateur.');
        }

        $this->redirect('admin/users');
    }

    /**
     * Handle user roles update
     * @param int $id User ID
     * @param array $roles Roles to set
     */
    private function handleUserRoles($id, $roles) {
        // Get database instance
        $db = Db::getInstance();

        // Remove all current roles
        $db->prepare("DELETE FROM Admin WHERE utilisateurId = :id")->execute(['id' => $id]);

        // Don't remove Chercheur role if part of MembreBureauExecutif
        $membreModel = new MembreBureauExecutif();
        $membreDetails = $membreModel->findWithDetails($id);

        if (!$membreDetails || $membreDetails['chercheurId'] === null) {
            $db->prepare("DELETE FROM Chercheur WHERE utilisateurId = :id")->execute(['id' => $id]);
        }

        $db->prepare("DELETE FROM MembreBureauExecutif WHERE utilisateurId = :id")->execute(['id' => $id]);

        // Add selected roles
        if (in_array('admin', $roles)) {
            $adminModel = new Admin();
            $adminModel->createFromUser($id);
        }

        if (in_array('chercheur', $roles)) {
            $domaineRecherche = $this->getInput('domaine_recherche');
            $bio = $this->getInput('bio');

            $chercheurModel = new Chercheur();
            $chercheurModel->createFromUser($id, $domaineRecherche, $bio);
        }

        if (in_array('membreBureauExecutif', $roles)) {
            $membreRole = $this->getInput('membre_role');
            $mandat = $this->getInput('mandat');
            $permissions = implode(',', $this->getInput('permissions', []));
            $isChercheur = in_array('chercheur', $roles);

            $chercheurId = null;
            if ($isChercheur) {
                $chercheurModel = new Chercheur();
                $chercheur = $chercheurModel->findWithDetails($id);
                $chercheurId = $chercheur ? $chercheur['utilisateurId'] : null;
            }

            $membreModel = new MembreBureauExecutif();
            $membreModel->createFromUser($id, $membreRole, $mandat, $permissions, $chercheurId);
        }
    }
}