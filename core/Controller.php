<?php
/**
 * Base Controller Class
 */
abstract class Controller {
    protected $auth;
    protected $config;
    protected $view;

    /**
     * Constructor
     */
    public function __construct() {
        $this->auth = Auth::getInstance();
        $this->config = Config::getInstance();
        $this->view = new View();

        // Check if there's an active session
        $this->checkSession();
    }

    /**
     * Check session and authentication
     */
    protected function checkSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Ensure user is authenticated
     * @param string|array $role Optional role(s) to check
     * @return bool
     */
    protected function requireAuth($role = null) {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('login', ['error' => 'login_required']);
            return false;
        }

        if ($role !== null && !$this->auth->hasRole($role)) {
            $this->renderForbidden();
            return false;
        }

        return true;
    }

    /**
     * Ensure user has permission
     * @param string $permission Permission to check
     * @return bool
     */
    protected function requirePermission($permission) {
        if (!$this->auth->isLoggedIn() || !$this->auth->hasPermission($permission)) {
            $this->renderForbidden();
            return false;
        }

        return true;
    }

    /**
     * Render a view
     * @param string $template Template name
     * @param array $data Data to pass to the view
     * @return void
     */
    protected function render($template, $data = []) {
        // Add global data
        $data['auth'] = $this->auth;
        $data['config'] = $this->config;
        $data['flash'] = $this->getFlash();

        $this->view->render($template, $data);
    }

    /**
     * Render forbidden page
     */
    protected function renderForbidden() {
        header('HTTP/1.1 403 Forbidden');
        $this->render('errors/forbidden');
        exit;
    }

    /**
     * Render not found page
     */
    protected function renderNotFound() {
        header('HTTP/1.1 404 Not Found');
        $this->render('errors/not_found');
        exit;
    }

    /**
     * Redirect to another page
     * @param string $path Path to redirect to
     * @param array $params Optional query parameters
     */
    protected function redirect($path, $params = []) {
        $url = $this->config->get('app.url') . '/' . ltrim($path, '/');

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        header('Location: ' . $url);
        exit;
    }

    /**
     * Get request method
     * @return string
     */
    protected function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Check if request is POST
     * @return bool
     */
    protected function isPost() {
        return $this->getMethod() === 'POST';
    }

    /**
     * Get input data
     * @param string $key Input key
     * @param mixed $default Default value if not found
     * @return mixed
     */
    protected function getInput($key = null, $default = null) {
        $input = array_merge($_GET, $_POST);

        if ($key === null) {
            return $input;
        }

        return $input[$key] ?? $default;
    }

    /**
     * Get file from upload
     * @param string $key File key
     * @return array|null
     */
    protected function getFile($key) {
        return $_FILES[$key] ?? null;
    }

    /**
     * Set flash message
     * @param string $type Message type (success, error, info, warning)
     * @param string $message Message text
     */
    protected function setFlash($type, $message) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Get flash message and clear it
     * @return array|null
     */
    protected function getFlash() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $flash;
    }

    /**
     * Return JSON response
     * @param array $data Data to return
     * @param int $code HTTP status code
     */
    protected function json($data, $code = 200) {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    /**
     * Validate input data
     * @param array $data Data to validate
     * @param array $rules Validation rules
     * @return array|bool Errors or true if valid
     */
    protected function validate($data, $rules) {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $fieldRules = explode('|', $fieldRules);

            foreach ($fieldRules as $rule) {
                $params = [];
                if (strpos($rule, ':') !== false) {
                    list($rule, $param) = explode(':', $rule, 2);
                    $params = explode(',', $param);
                }

                switch ($rule) {
                    case 'required':
                        if (!isset($data[$field]) || trim($data[$field]) === '') {
                            $errors[$field][] = 'Le champ est requis';
                        }
                        break;

                    case 'email':
                        if (isset($data[$field]) && !empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = 'Email invalide';
                        }
                        break;

                    case 'min':
                        if (isset($data[$field]) && strlen($data[$field]) < (int)$params[0]) {
                            $errors[$field][] = 'Minimum ' . $params[0] . ' caractères';
                        }
                        break;

                    case 'max':
                        if (isset($data[$field]) && strlen($data[$field]) > (int)$params[0]) {
                            $errors[$field][] = 'Maximum ' . $params[0] . ' caractères';
                        }
                        break;

                    case 'numeric':
                        if (isset($data[$field]) && !is_numeric($data[$field])) {
                            $errors[$field][] = 'Doit être un nombre';
                        }
                        break;

                    case 'date':
                        if (isset($data[$field]) && !empty($data[$field])) {
                            $d = DateTime::createFromFormat('Y-m-d', $data[$field]);
                            if (!($d && $d->format('Y-m-d') === $data[$field])) {
                                $errors[$field][] = 'Date invalide (format YYYY-MM-DD)';
                            }
                        }
                        break;

                    case 'same':
                        if (isset($data[$field]) && isset($data[$params[0]]) && $data[$field] !== $data[$params[0]]) {
                            $errors[$field][] = 'Les champs ne correspondent pas';
                        }
                        break;

                    case 'unique':
                        if (isset($data[$field]) && !empty($data[$field])) {
                            list($table, $column) = $params;
                            $db = Db::getInstance();
                            $stmt = $db->prepare("SELECT COUNT(*) FROM {$table} WHERE {$column} = :value");
                            $stmt->execute(['value' => $data[$field]]);

                            if ($stmt->fetchColumn() > 0) {
                                $errors[$field][] = 'Cette valeur existe déjà';
                            }
                        }
                        break;
                }
            }
        }

        return empty($errors) ? true : $errors;
    }
}