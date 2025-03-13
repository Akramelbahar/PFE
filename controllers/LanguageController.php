<?php
require_once './core/Controller.php';

/**
 * Language Controller
 * Manages language switching functionality
 */
class LanguageController extends Controller {
    /**
     * Set the language
     * @param string $lang
     */
    public function set($lang) {
        // Validate the language code
        $validLanguages = ['fr', 'en', 'ar'];

        if (!in_array($lang, $validLanguages)) {
            $lang = 'fr'; // Default to French
        }

        // Set session language
        $_SESSION['lang'] = $lang;

        // Redirect back
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';
        $this->redirect($redirect ?: '');
    }
}