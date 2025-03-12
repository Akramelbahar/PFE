<?php
/**
 * View Class
 * Handles rendering of templates, layouts, and provides utility methods for views
 */
class View {
    /**
     * Base path for template files
     * @var string
     */
    private $basePath;

    /**
     * Current layout template
     * @var string
     */
    private $layout = 'default';

    /**
     * Collection of global view data
     * @var array
     */
    private $globalData = [];

    /**
     * Constructor
     * Initializes the base path for templates
     */
    public function __construct() {
        $config = Config::getInstance();

        // Get the relative templates path from config
        $templatesPath = $config->get('paths.templates', './views/');

        // Normalize the path
        $this->basePath = $this->normalizePath($templatesPath);
    }

    /**
     * Normalize path for consistent usage
     * @param string $path
     * @return string
     */
    private function normalizePath($path) {
        // Remove leading './' or '/'
        $path = preg_replace('/^(\.\/)?(\/)?/', '', $path);

        // Ensure trailing slash
        return './' . rtrim($path, '/') . '/';
    }

    /**
     * Set a global view data variable
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setGlobal($key, $value) {
        $this->globalData[$key] = $value;
        return $this;
    }

    /**
     * Set the layout template
     * @param string $layout
     * @return self
     */
    public function setLayout($layout) {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Render a view template
     * @param string $template Template name
     * @param array $data Additional data to pass to the template
     * @throws Exception If template file is not found
     */
    public function render($template, $data = []) {
        // Merge global and local data
        $viewData = array_merge($this->globalData, $data);

        // Resolve template file path
        $templatePath = $this->resolveTemplatePath($template);

        // Validate template file
        $this->validateTemplateFile($templatePath);

        // Start output buffering
        ob_start();

        // Extract view data to create local variables
        extract($viewData);

        try {
            // Include the template file
            require $templatePath;

            // Capture template content
            $content = ob_get_clean();

            // Render with layout if enabled
            if ($this->layout !== null) {
                $content = $this->renderWithLayout($content, $viewData);
            }

            // Output final content
            echo $content;
        } catch (Exception $e) {
            // Capture any rendering errors
            ob_end_clean();
            throw $e;
        }
    }

    /**
     * Render a partial view
     * @param string $partial Partial name
     * @param array $data Additional data to pass to the partial
     * @return string Rendered partial content
     * @throws Exception If partial file is not found
     */
    public function partial($partial, $data = []) {
        // Merge global and local data
        $viewData = array_merge($this->globalData, $data);

        // Resolve partial file path
        $partialPath = $this->resolveTemplatePath('partials/' . $partial);

        // Validate partial file
        $this->validateTemplateFile($partialPath);

        // Start output buffering
        ob_start();

        // Extract view data to create local variables
        extract($viewData);

        // Include the partial file
        require $partialPath;

        // Return captured partial content
        return ob_get_clean();
    }

    /**
     * Resolve full path to a template file
     * @param string $template Template name
     * @return string Full path to template file
     */
    private function resolveTemplatePath($template) {
        // Normalize template path
        $template = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $template);
        $template = trim($template, DIRECTORY_SEPARATOR);

        // Construct relative path
        $templateFile = $this->basePath . $template .
            (strpos($template, '.php') === false ? '.php' : '');

        return $templateFile;
    }

    /**
     * Validate that a template file exists
     * @param string $path
     * @throws Exception If file does not exist
     */
    private function validateTemplateFile($path) {
        if (!file_exists($path)) {
            throw new Exception("Template file not found: $path");
        }
    }

    /**
     * Render template with layout
     * @param string $content Template content
     * @param array $data View data
     * @return string Final rendered content
     * @throws Exception If layout file is not found
     */
    private function renderWithLayout($content, $data) {
        // Resolve layout file path
        $layoutPath = $this->resolveTemplatePath('layouts/' . $this->layout);

        // Validate layout file
        $this->validateTemplateFile($layoutPath);

        // Start output buffering for layout
        ob_start();

        // Extract view data to create local variables
        extract($data);

        // Include the layout file
        require $layoutPath;

        // Return final rendered content
        return ob_get_clean();
    }

    /**
     * Escape HTML to prevent XSS
     * @param string $string String to escape
     * @return string Escaped string
     */
    public function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Format date
     * @param string $date Date to format
     * @param string $format Format
     * @return string Formatted date
     */
    public function formatDate($date, $format = 'd/m/Y H:i') {
        if (empty($date)) {
            return '';
        }

        try {
            $dateTime = new DateTime($date);
            return $dateTime->format($format);
        } catch (Exception $e) {
            return $date;
        }
    }

    /**
     * Format currency
     * @param float $amount Amount to format
     * @param string $currency Currency symbol
     * @return string Formatted currency
     */
    public function formatCurrency($amount, $currency = 'MAD') {
        return number_format($amount, 2, ',', ' ') . ' ' . $currency;
    }

    /**
     * Truncate text
     * @param string $text Text to truncate
     * @param int $length Maximum length
     * @param string $suffix Suffix to add
     * @return string Truncated text
     */
    public function truncate($text, $length = 100, $suffix = '...') {
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length) . $suffix;
    }

    /**
     * Generate a URL
     * @param string $path Path
     * @param array $params Query parameters
     * @return string Full URL
     */
    public function url($path, $params = []) {
        $config = Config::getInstance();
        $baseUrl = rtrim($config->get('app.url', ''), '/');
        $path = ltrim($path, '/');

        $url = $baseUrl . '/' . $path;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    /**
     * Generate a link
     * @param string $text Link text
     * @param string $path Link path
     * @param array $params Query parameters
     * @param array $attributes Additional HTML attributes
     * @return string HTML link
     */
    public function link($text, $path, $params = [], $attributes = []) {
        $url = $this->url($path, $params);

        $attr = '';
        foreach ($attributes as $key => $value) {
            $attr .= ' ' . $key . '="' . $this->escape($value) . '"';
        }

        return '<a href="' . $this->escape($url) . '"' . $attr . '>' . $this->escape($text) . '</a>';
    }

    /**
     * Check if current path matches given path
     * @param string $path Path to check
     * @return bool
     */
    public function isCurrentPath($path) {
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);

        if ($scriptName !== '/') {
            $currentPath = substr($currentPath, strlen($scriptName));
        }

        return $currentPath === '/' . ltrim($path, '/');
    }

    /**
     * Get active CSS class if path matches current path
     * @param string $path Path to check
     * @param string $class CSS class to return
     * @return string
     */
    public function activeClass($path, $class = 'active') {
        return $this->isCurrentPath($path) ? $class : '';
    }
}