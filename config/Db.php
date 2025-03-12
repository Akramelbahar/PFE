<?php
/**
 * Database Connection Class (Singleton Pattern)
 */
class Db {
    private static $instance = NULL;

    // Prevent direct instantiation
    private function __construct() {}

    // Prevent cloning
    private function __clone() {}

    // Get instance method
    public static function getInstance() {
        if (!isset(self::$instance)) {
            $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
            self::$instance = new PDO('mysql:host=localhost;dbname=ests', 'root', '', $pdo_options);
            self::$instance->exec("SET NAMES utf8");
        }
        return self::$instance;
    }
}