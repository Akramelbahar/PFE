<?php
/**
 * Translation Class
 * Manages multilingual support
 */
class Translation {
    private static $translations = [];
    private static $defaultLanguage = 'fr';
    private static $currentLanguage = 'fr';

    /**
     * Initialize the translation system
     * @param string $defaultLang
     */
    public static function init($defaultLang = 'fr') {
        self::$defaultLanguage = $defaultLang;

        // Get current language from session if set
        if (isset($_SESSION['lang'])) {
            self::$currentLanguage = $_SESSION['lang'];
        } else {
            self::$currentLanguage = $defaultLang;
            $_SESSION['lang'] = $defaultLang;
        }

        // Load language files
        self::loadTranslations(self::$currentLanguage);
    }

    /**
     * Load translations for a specific language
     * @param string $lang
     */
    private static function loadTranslations($lang) {
        $langFile = './lang/' . $lang . '.php';

        if (file_exists($langFile)) {
            self::$translations = require $langFile;
        } else {
            // Fallback to default language
            $langFile = './lang/' . self::$defaultLanguage . '.php';
            if (file_exists($langFile)) {
                self::$translations = require $langFile;
            }
        }
    }

    /**
     * Get the translation for a key
     * @param string $key
     * @param array $params
     * @return string
     */
    public static function get($key, $params = []) {
        if (isset(self::$translations[$key])) {
            $translation = self::$translations[$key];

            // Replace placeholders with parameters
            if (!empty($params)) {
                foreach ($params as $placeholder => $value) {
                    $translation = str_replace(':' . $placeholder, $value, $translation);
                }
            }

            return $translation;
        }

        return $key; // Return the key if no translation found
    }

    /**
     * Get current language
     * @return string
     */
    public static function getCurrentLanguage() {
        return self::$currentLanguage;
    }

    /**
     * Check if the current language is RTL
     * @return bool
     */
    public static function isRtl() {
        return self::$currentLanguage === 'ar';
    }
}