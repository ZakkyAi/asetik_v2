<?php

/**
 * Asetik v2 - Application Entry Point
 * This file bootstraps the application and handles all incoming requests
 */

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path
define('BASE_PATH', __DIR__);

// Autoloader for classes
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/src/';
    
    // Check if the class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Get the relative class name
    $relative_class = substr($class, $len);
    
    // Replace namespace separators with directory separators
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Load helper functions
require_once __DIR__ . '/src/helpers.php';

// Load routes and dispatch
$router = require __DIR__ . '/src/routes.php';
$router->dispatch();
