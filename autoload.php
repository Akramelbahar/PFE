<?php
// autoload.php

/**
 * Simple autoloader function
 * @param string $class Class name to load
 */
function autoload($class) {
    // Define mappings for class locations (modify paths as needed)
    $classMappings = [
        // Core classes
        'Router' => './core/Router.php',
        'Controller' => './core/Controller.php',
        'View' => './core/View.php',
        'Db' => './config/Db.php',
        'Config' => './core/Config.php',
        'Auth' => './auth/Auth.php',
        'CSRF' => './utils/CSRF.php',
        'FileManager' => './utils/FileManager.php',

        // Controllers
        'HomeController' => './controllers/HomeController.php',
        'AuthController' => './controllers/AuthController.php',
        'UserController' => './controllers/UserController.php',
        'AdminController' => './controllers/AdminController.php',
        'PublicationController' => './controllers/PublicationController.php',
        'EventController' => './controllers/EventController.php',
        'ProjectController' => './controllers/ProjectController.php',
        'NewsController' => './controllers/NewsController.php',
        'ContactController' => './controllers/ContactController.php',
        'IdeeRechercheController' => './controllers/IdeeRechercheController.php',
        'SearchController'=>'./controllers/SearchController.php',
        'ActivityDashboardController'=>'./controllers/ActivityDashboardController.php',
        'PartnerController'=>'./controllers/PartnerController.php',
        // Models
        'Model' => './models/Model.php',
        'Utilisateur' => './models/users/Utilisateur.php',
        'Admin' => './models/users/Admin.php',
        'Chercheur' => './models/users/Chercheur.php',
        'MembreBureauExecutif' => './models/users/MembreBureauExecutif.php',
        'Publication' => './models/publications/Publication.php',
        'Article' => './models/publications/Article.php',
        'Livre' => './models/publications/Livre.php',
        'Chapitre' => './models/publications/Chapitre.php',
        'Evenement' => './models/events/Evenement.php',
        'Conference' => './models/events/Conference.php',
        'Seminaire' => './models/events/Seminaire.php',
        'Workshop' => './models/events/Workshop.php',
        'ProjetRecherche' => './models/projects/ProjetRecherche.php',
        'Participe' => './models/Participe.php',
        'Partner' => './models/projects/Partner.php',
        'IdeeRecherche' => './models/IdeeRecherche.php',
        'Actualite' => './models/Actualite.php',
        'Contact' => './models/Contact.php',

    ];

    // Check if class exists in our mappings
    if (isset($classMappings[$class])) {
        require_once $classMappings[$class];
    }
}

// Register the autoloader
spl_autoload_register('autoload');