<?php
/**
 * Router class to handle routing
 */
class Router {
    private $routes = [];
    private $notFoundCallback;
    private $baseUrl;

    /**
     * Constructor
     */
    public function __construct() {
        // Determine base URL from server variables
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        $this->baseUrl = $scriptName === '/' ? '' : $scriptName;
    }

    /**
     * Add a GET route
     * @param string $pattern Route pattern
     * @param callable $callback Callback function
     * @return Router
     */
    public function get($pattern, $callback) {
        $this->addRoute('GET', $pattern, $callback);
        return $this;
    }

    /**
     * Add a POST route
     * @param string $pattern Route pattern
     * @param callable $callback Callback function
     * @return Router
     */
    public function post($pattern, $callback) {
        $this->addRoute('POST', $pattern, $callback);
        return $this;
    }

    /**
     * Add a PUT route
     * @param string $pattern Route pattern
     * @param callable $callback Callback function
     * @return Router
     */
    public function put($pattern, $callback) {
        $this->addRoute('PUT', $pattern, $callback);
        return $this;
    }

    /**
     * Add a DELETE route
     * @param string $pattern Route pattern
     * @param callable $callback Callback function
     * @return Router
     */
    public function delete($pattern, $callback) {
        $this->addRoute('DELETE', $pattern, $callback);
        return $this;
    }

    /**
     * Add a route for any HTTP method
     * @param string $pattern Route pattern
     * @param callable $callback Callback function
     * @return Router
     */
    public function any($pattern, $callback) {
        $this->addRoute('GET|POST|PUT|DELETE', $pattern, $callback);
        return $this;
    }

    /**
     * Add a route for multiple HTTP methods
     * @param array $methods HTTP methods
     * @param string $pattern Route pattern
     * @param callable $callback Callback function
     * @return Router
     */
    public function map($methods, $pattern, $callback) {
        $methods = is_array($methods) ? implode('|', $methods) : $methods;
        $this->addRoute($methods, $pattern, $callback);
        return $this;
    }

    /**
     * Set callback for not found routes
     * @param callable $callback Callback function
     * @return Router
     */
    public function notFound($callback) {
        $this->notFoundCallback = $callback;
        return $this;
    }

    /**
     * Add a route
     * @param string $method HTTP method(s)
     * @param string $pattern Route pattern
     * @param callable $callback Callback function
     */
    private function addRoute($method, $pattern, $callback) {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback
        ];
    }

    /**
     * Match the route
     * @param string $method HTTP method
     * @param string $uri Request URI
     * @return array|false
     */
    private function match($method, $uri) {
        // Sort routes: specific routes first, followed by dynamic routes
        usort($this->routes, function($a, $b) {
            // Count parameter segments
            $aParams = substr_count($a['pattern'], ':');
            $bParams = substr_count($b['pattern'], ':');

            // If one has parameters and the other doesn't, put the one without params first
            if ($aParams === 0 && $bParams > 0) return -1;
            if ($aParams > 0 && $bParams === 0) return 1;

            // If both have params, prefer the one with fewer params
            if ($aParams !== $bParams) return $aParams - $bParams;

            // If both have the same number of params, prefer the longer pattern
            return strlen($b['pattern']) - strlen($a['pattern']);
        });

        foreach ($this->routes as $route) {
            // Check if method matches
            if (strpos($route['method'], $method) === false) {
                continue;
            }

            // Convert route pattern to regex
            $pattern = $this->patternToRegex($route['pattern']);

            // Check if URI matches the pattern
            if (preg_match($pattern, $uri, $matches)) {
                // Remove the first match (the full string)
                array_shift($matches);

                return [
                    'callback' => $route['callback'],
                    'params' => $matches
                ];
            }
        }

        return false;
    }

    /**
     * Convert route pattern to regex
     * @param string $pattern Route pattern
     * @return string
     */
    private function patternToRegex($pattern) {
        // Replace named parameters :param with regex
        $pattern = preg_replace('/:[a-zA-Z0-9]+/', '([^/]+)', $pattern);

        // Add regex delimiters and make it match the full string
        return '#^' . $pattern . '$#';
    }

    /**
     * Run the router
     */
    public function run() {
        // Get the HTTP method and URI
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove base path from URI
        if ($this->baseUrl !== '' && strpos($uri, $this->baseUrl) === 0) {
            $uri = substr($uri, strlen($this->baseUrl));
        }

        // Ensure leading slash is removed
        $uri = ltrim($uri, '/');

        // If URI is empty, treat it as the root route
        if ($uri === '') {
            $uri = '';
        }

        // Match the route
        $route = $this->match($method, $uri);

        if ($route) {
            // Call the callback with parameters
            call_user_func_array($route['callback'], $route['params']);
        } else {
            // Not found
            if ($this->notFoundCallback) {
                call_user_func($this->notFoundCallback);
            } else {
                // Default 404 response
                header('HTTP/1.1 404 Not Found');
                echo '404 Not Found';
            }
        }
    }
}