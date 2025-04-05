<?php
// Autoload classes or include necessary files
require_once './autoload.php'; // Or your equivalent file loading system
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load application routes
$router = require_once './config/routes.php';

// Run the router
$router->run();
// Dans index.php
if (!class_exists('Router')) {
    die('Erreur : Classe Router non chargée');
}