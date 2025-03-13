<?php
require_once './core/Controller.php';
require_once './auth/Auth.php';
require_once './models/users/Utilisateur.php';

/**
 * Authentication Controller
 */
class AuthController extends Controller {
    /**
     * Login page
     */
    public function login() {
        // If already logged in, redirect to home
        if ($this->auth->isLoggedIn()) {
            $this->redirect('');
            return;
        }

        // Check for error messages
        $error = $this->getInput('error');
        $errorMessage = '';

        if ($error === 'login_required') {
            $errorMessage = 'Veuillez vous connecter pour accéder à cette page.';
        } elseif ($error === 'invalid_credentials') {
            $errorMessage = 'Email ou mot de passe incorrect.';
        }

        $this->render('auth/login', [
            'pageTitle' => 'Connexion',
            'errorMessage' => $errorMessage
        ]);
    }

    /**
     * Process login form
     */
    public function doLogin() {
        // If already logged in, redirect to home
        if ($this->auth->isLoggedIn()) {
            $this->redirect('');
            return;
        }

        // Check if form was submitted
        if (!$this->isPost()) {
            $this->redirect('login');
            return;
        }

        // Get form data
        $email = $this->getInput('email');
        $password = $this->getInput('password');
        $rememberMe = $this->getInput('remember_me') === 'on';

        // Validate input
        $validation = $this->validate(
            ['email' => $email, 'password' => $password],
            ['email' => 'required|email', 'password' => 'required|min:6']
        );

        if ($validation !== true) {
            $this->render('auth/login', [
                'pageTitle' => 'Connexion',
                'errors' => $validation,
                'email' => $email
            ]);
            return;
        }

        // Try to login
        if ($this->auth->login($email, $password)) {
            // Set session lifetime if remember me is checked
            if ($rememberMe) {
                $sessionLifetime = $this->config->get('app.session_lifetime', 7200);
                ini_set('session.gc_maxlifetime', $sessionLifetime);
                session_set_cookie_params($sessionLifetime);
            }

            // Redirect to home or previous page
            $redirect = $this->getInput('redirect', '');
            $this->redirect($redirect);
        } else {
            // Invalid credentials
            $this->redirect('login', ['error' => 'invalid_credentials']);
        }
    }

    /**
     * Logout
     */
    public function logout() {
        $this->auth->logout();
        $this->redirect('login');
    }

    /**
     * Register page
     */
    public function register() {
        // If already logged in, redirect to home
        if ($this->auth->isLoggedIn()) {
            $this->redirect('');
            return;
        }

        $this->render('auth/register', [
            'pageTitle' => 'Inscription'
        ]);
    }

    /**
     * Process registration form
     */
    public function doRegister() {
        // If already logged in, redirect to home
        if ($this->auth->isLoggedIn()) {
            $this->redirect('');
            return;
        }

        // Check if form was submitted
        if (!$this->isPost()) {
            $this->redirect('register');
            return;
        }

        // Get form data
        $nom = $this->getInput('nom');
        $prenom = $this->getInput('prenom');
        $email = $this->getInput('email');
        $password = $this->getInput('password');
        $passwordConfirm = $this->getInput('password_confirm');

        // Validate input
        $validation = $this->validate(
            [
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'password' => $password,
                'password_confirm' => $passwordConfirm
            ],
            [
                'nom' => 'required|max:255',
                'prenom' => 'required|max:255',
                'email' => 'required|email|unique:Utilisateur,email',
                'password' => 'required|min:6',
                'password_confirm' => 'required|same:password'
            ]
        );

        if ($validation !== true) {
            $this->render('auth/register', [
                'pageTitle' => 'Inscription',
                'errors' => $validation,
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email
            ]);
            return;
        }

        // Create user
        $userId = $this->auth->register([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'motDePasse' => $password
        ]);

        if ($userId) {
            // Auto login
            $this->auth->login($email, $password);

            // Set success message
            $this->setFlash('success', 'Votre compte a été créé avec succès. Bienvenue !');

            // Redirect to home
            $this->redirect('');
        } else {
            $this->render('auth/register', [
                'pageTitle' => 'Inscription',
                'errorMessage' => 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.',
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email
            ]);
        }
    }

    /**
     * Forgot password page
     */
    public function forgotPassword() {
        // If already logged in, redirect to home
        if ($this->auth->isLoggedIn()) {
            $this->redirect('');
            return;
        }

        $this->render('auth/forgot_password', [
            'pageTitle' => 'Mot de passe oublié'
        ]);
    }

    /**
     * Process forgot password form
     */
    public function doForgotPassword() {
        // If already logged in, redirect to home
        if ($this->auth->isLoggedIn()) {
            $this->redirect('');
            return;
        }

        // Check if form was submitted
        if (!$this->isPost()) {
            $this->redirect('forgot-password');
            return;
        }

        // Get form data
        $email = $this->getInput('email');

        // Validate input
        $validation = $this->validate(
            ['email' => $email],
            ['email' => 'required|email']
        );

        if ($validation !== true) {
            $this->render('auth/forgot_password', [
                'pageTitle' => 'Mot de passe oublié',
                'errors' => $validation,
                'email' => $email
            ]);
            return;
        }

        // Check if user exists
        $utilisateurModel = new Utilisateur();
        $user = $utilisateurModel->findByEmail($email);

        if (!$user) {
            $this->setFlash('info', 'Si cette adresse email existe dans notre système, vous recevrez un email avec les instructions pour réinitialiser votre mot de passe.');
            $this->redirect('login');
            return;
        }

        // Generate token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Store token in the database (assuming a PasswordReset table exists)
        $db = Db::getInstance();

        // First, check if there's already a token for this user and delete it
        $stmt = $db->prepare("DELETE FROM PasswordReset WHERE email = :email");
        $stmt->execute(['email' => $email]);

        // Then create a new token
        $stmt = $db->prepare("
            INSERT INTO PasswordReset (email, token, expires)
            VALUES (:email, :token, :expires)
        ");

        $tokenCreated = $stmt->execute([
            'email' => $email,
            'token' => $token,
            'expires' => $expires
        ]);

        if ($tokenCreated) {
            // In a real application, send an email with the reset link
            // For demo purposes, just show a success message

            $this->setFlash('info', 'Si cette adresse email existe dans notre système, vous recevrez un email avec les instructions pour réinitialiser votre mot de passe.');
            $this->redirect('login');
        } else {
            $this->render('auth/forgot_password', [
                'pageTitle' => 'Mot de passe oublié',
                'errorMessage' => 'Une erreur est survenue. Veuillez réessayer.',
                'email' => $email
            ]);
        }
    }

    /**
     * Reset password page
     */
    public function resetPassword() {
        // If already logged in, redirect to home
        if ($this->auth->isLoggedIn()) {
            $this->redirect('');
            return;
        }

        // Get token from URL
        $token = $this->getInput('token');

        if (!$token) {
            $this->redirect('login');
            return;
        }

        // Check if token is valid
        $db = Db::getInstance();
        $stmt = $db->prepare("
            SELECT * FROM PasswordReset
            WHERE token = :token AND expires > NOW()
            LIMIT 1
        ");

        $stmt->execute(['token' => $token]);
        $resetData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$resetData) {
            $this->setFlash('error', 'Le lien de réinitialisation est invalide ou a expiré.');
            $this->redirect('login');
            return;
        }

        $this->render('auth/reset_password', [
            'pageTitle' => 'Réinitialiser le mot de passe',
            'token' => $token
        ]);
    }

    /**
     * Process reset password form
     */
    public function doResetPassword() {
        // If already logged in, redirect to home
        if ($this->auth->isLoggedIn()) {
            $this->redirect('');
            return;
        }

        // Check if form was submitted
        if (!$this->isPost()) {
            $this->redirect('login');
            return;
        }

        // Get form data
        $token = $this->getInput('token');
        $password = $this->getInput('password');
        $passwordConfirm = $this->getInput('password_confirm');

        // Validate input
        $validation = $this->validate(
            ['password' => $password, 'password_confirm' => $passwordConfirm],
            ['password' => 'required|min:6', 'password_confirm' => 'required|same:password']
        );

        if ($validation !== true) {
            $this->render('auth/reset_password', [
                'pageTitle' => 'Réinitialiser le mot de passe',
                'token' => $token,
                'errors' => $validation
            ]);
            return;
        }

        // Get email from token
        $db = Db::getInstance();
        $stmt = $db->prepare("
            SELECT * FROM PasswordReset
            WHERE token = :token AND expires > NOW()
            LIMIT 1
        ");

        $stmt->execute(['token' => $token]);
        $resetData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$resetData) {
            $this->setFlash('error', 'Le lien de réinitialisation est invalide ou a expiré.');
            $this->redirect('login');
            return;
        }

        // Update password
        $email = $resetData['email'];

        $utilisateurModel = new Utilisateur();
        $user = $utilisateurModel->findByEmail($email);

        if (!$user) {
            $this->setFlash('error', 'Utilisateur non trouvé.');
            $this->redirect('login');
            return;
        }

        // Hash new password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Update user
        $updated = $utilisateurModel->update($user['id'], [
            'motDePasse' => $hashedPassword
        ]);

        if ($updated) {
            // Delete token
            $stmt = $db->prepare("DELETE FROM PasswordReset WHERE token = :token");
            $stmt->execute(['token' => $token]);

            $this->setFlash('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
            $this->redirect('login');
        } else {
            $this->render('auth/reset_password', [
                'pageTitle' => 'Réinitialiser le mot de passe',
                'token' => $token,
                'errorMessage' => 'Une erreur est survenue lors de la réinitialisation de votre mot de passe. Veuillez réessayer.'
            ]);
        }
    }
}