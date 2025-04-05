<?php
/**
 * Utility Class with static helper methods
 */
class Utils {
    /**
     * Format date to readable format
     * @param string $date
     * @param string $format
     * @return string
     */
    public static function formatDate($date, $format = 'd/m/Y H:i') {
        if (empty($date)) {
            return '';
        }
        $dateTime = new DateTime($date);
        return $dateTime->format($format);
    }

    /**
     * Format currency
     * @param float $amount
     * @param string $currency
     * @return string
     */
    public static function formatCurrency($amount, $currency = 'MAD') {
        return number_format($amount, 2, ',', ' ') . ' ' . $currency;
    }

    /**
     * Generate a random token
     * @param int $length
     * @return string
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Sanitize input data
     * @param string $data
     * @return string
     */
    public static function sanitize($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }

    /**
     * Validate email
     * @param string $email
     * @return bool
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Get file extension
     * @param string $filename
     * @return string
     */
    public static function getFileExtension($filename) {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * Check if file is allowed
     * @param string $filename
     * @param array $allowedExtensions
     * @return bool
     */
    public static function isFileAllowed($filename, $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']) {
        $extension = self::getFileExtension($filename);
        return in_array($extension, $allowedExtensions);
    }

    /**
     * Generate a slug from a string
     * @param string $text
     * @return string
     */
    public static function slugify($text) {
        // Replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // Transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // Trim
        $text = trim($text, '-');

        // Remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // Lowercase
        $text = strtolower($text);

        // If empty, return 'n-a'
        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * Truncate text
     * @param string $text
     * @param int $length
     * @param string $suffix
     * @return string
     */
    public static function truncate($text, $length = 100, $suffix = '...') {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . $suffix;
    }

    /**
     * Sanitize filename
     * @param string $filename
     * @return string
     */
    public static function sanitizeFilename($filename) {
        // Replace spaces with underscores
        $filename = str_replace(' ', '_', $filename);

        // Remove any non-alphanumeric characters except for dots, underscores and hyphens
        $filename = preg_replace('/[^A-Za-z0-9._-]/', '', $filename);

        return $filename;
    }

    /**
     * Generate a unique filename
     * @param string $filename
     * @return string
     */
    public static function uniqueFilename($filename) {
        $extension = self::getFileExtension($filename);
        $basename = basename($filename, '.' . $extension);
        $basename = self::sanitizeFilename($basename);

        return $basename . '_' . time() . '.' . $extension;
    }

    /**
     * Check if a string is JSON
     * @param string $string
     * @return bool
     */
    public static function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}