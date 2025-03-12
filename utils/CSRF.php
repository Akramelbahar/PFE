<?php
/**
 * CSRF Protection Utility
 * Provides methods for generating and verifying CSRF tokens to prevent cross-site request forgery attacks.
 */
class CSRF {
    /**
     * Generate a CSRF token and store it in the session
     * @return string The generated token
     */
    public static function generateToken() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Verify that the provided token matches the one in the session
     * @param string $token The token to verify
     * @return bool True if the token is valid, false otherwise
     */
    public static function verifyToken($token) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            return false;
        }

        $valid = hash_equals($_SESSION['csrf_token'], $token);

        // Regenerate token after verification for enhanced security
        if ($valid) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $valid;
    }

    /**
     * Generate an HTML input field containing the CSRF token
     * @return string HTML input field with the CSRF token
     */
    public static function tokenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Add CSRF token to a URL as a query parameter
     * @param string $url The URL to add the token to
     * @return string URL with CSRF token appended
     */
    public static function addTokenToUrl($url) {
        $token = self::generateToken();
        $separator = (parse_url($url, PHP_URL_QUERY) === null) ? '?' : '&';
        return $url . $separator . 'csrf_token=' . urlencode($token);
    }
}