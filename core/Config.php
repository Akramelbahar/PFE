<?php
/**
 * Configuration Class
 * Manages application configuration settings with support for different environments.
 */
class Config {
    private static $instance = null;
    private $config = [];

    /**
     * Constructor - loads configuration
     */
    private function __construct() {
        $this->loadConfig();
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Get singleton instance
     * @return Config
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Load configuration from files
     */
    private function loadConfig() {
        // Determine environment based on hostname
        $hostname = $_SERVER['HTTP_HOST'] ?? '';
        $isProduction = true;
        
        // Default configuration
        $this->config = [
            'app' => [
                'name' => 'Association Recherche et Innovation',
                'url' => $isProduction ? 'https://est.center' : 'http://localhost',
                'timezone' => 'Africa/Casablanca',
                'locale' => 'fr_FR.UTF-8',
                'debug' => !$isProduction, // Debug off in production
                'session_lifetime' => 7200,
            ],
            'database' => [
                'host' => 'localhost', // Always use localhost for database connections
                'username' => $isProduction ? 'estcibjm_ests' : 'root',
                'password' => $isProduction ? 'YOUR_DB_PASSWORD' : '',
                'database' => $isProduction ? 'estcibjm_ests' : 'ests',
                'charset' => 'utf8mb4',
                'port' => 3306,
                'options' => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ],
            ],
            'paths' => [
                'uploads' => './public/uploads/',
                'templates' => './views/',
                'logs' => './logs/',
            ],
            'security' => [
                'password_hash_algo' => PASSWORD_DEFAULT,
                'token_lifetime' => 3600, // 1 hour
            ],
            'mail' => [
                'from_name' => 'Association Recherche et Innovation',
                'from_email' => 'noreply@est.center',
                'smtp_host' => 'localhost',
                'smtp_port' => 587,
                'smtp_username' => '',
                'smtp_password' => '',
                'smtp_secure' => 'tls',
            ],
        ];

        // Load environment-specific configuration
        $env = $isProduction ? 'production' : 'development';
        $envConfigFile = "./config/environments/{$env}.php";

        if (file_exists($envConfigFile)) {
            $envConfig = require $envConfigFile;
            $this->mergeConfig($envConfig);
        }

        // Load local configuration override if present
        $localConfigFile = './config/config.local.php';
        if (file_exists($localConfigFile)) {
            $localConfig = require $localConfigFile;
            $this->mergeConfig($localConfig);
        }
    }

    /**
     * Merge configuration arrays recursively
     * @param array $config Configuration to merge
     */
    private function mergeConfig($config) {
        $this->config = $this->arrayMergeRecursive($this->config, $config);
    }

    /**
     * Recursive array merge
     * @param array $array1 Base array
     * @param array $array2 Array to merge
     * @return array Merged array
     */
    private function arrayMergeRecursive($array1, $array2) {
        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($array1[$key]) && is_array($array1[$key])) {
                $array1[$key] = $this->arrayMergeRecursive($array1[$key], $value);
            } else {
                $array1[$key] = $value;
            }
        }
        return $array1;
    }

    /**
     * Get a configuration value
     * @param string $key Configuration key in dot notation (e.g., 'app.name')
     * @param mixed $default Default value if key doesn't exist
     * @return mixed Configuration value
     */
    public function get($key, $default = null) {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $segment) {
            if (!isset($value[$segment])) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Set a configuration value
     * @param string $key Configuration key in dot notation (e.g., 'app.name')
     * @param mixed $value Value to set
     */
    public function set($key, $value) {
        $keys = explode('.', $key);
        $configRef = &$this->config;

        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                $configRef[$segment] = $value;
            } else {
                if (!isset($configRef[$segment]) || !is_array($configRef[$segment])) {
                    $configRef[$segment] = [];
                }
                $configRef = &$configRef[$segment];
            }
        }
    }

    /**
     * Get all configuration
     * @return array Complete configuration array
     */
    public function getAll() {
        return $this->config;
    }

    /**
     * Save configuration to file
     * @param string $file File path
     * @return bool True if saved successfully
     */
    public function saveToFile($file = null) {
        if ($file === null) {
            $file = './config/config.local.php';
        }

        $directory = dirname($file);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $content = "<?php\nreturn " . var_export($this->config, true) . ";\n";
        return file_put_contents($file, $content) !== false;
    }
}