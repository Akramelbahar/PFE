<?php
// Autoload classes or include necessary files
require_once './autoload.php'; // Or your equivalent file loading system

// Load application routes
$router = require_once './config/routes.php';

// Run the router
$router->run();