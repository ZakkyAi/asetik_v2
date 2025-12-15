<?php

use App\Router\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\GuestMiddleware;

// Initialize router with base path
// Detect base path from REQUEST_URI
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$basePath = $scriptName !== '/' ? $scriptName : '';
$router = new Router($basePath);

// Public routes (no authentication required)
$router->get('/', function() {
    // Start session if not started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if user is logged in
    if (isset($_SESSION['user_id'])) {
        // Redirect to home/dashboard
        redirect('/home');
    } else {
        // Redirect to login
        redirect('/login');
    }
});

// Router test page (for development)
$router->get('/router-test', __DIR__ . '/../router-test.php');

// Debug route (for troubleshooting)
$router->get('/debug', function() {
    echo "<h1>Router Debug Info</h1>";
    echo "<pre>";
    echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
    echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
    echo "Base Path: " . dirname($_SERVER['SCRIPT_NAME']) . "\n";
    echo "Parsed Path: " . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . "\n";
    echo "\nSession Data:\n";
    print_r($_SESSION);
    echo "</pre>";
});

// Comprehensive test page
$router->get('/test-all-routes', __DIR__ . '/../test-all-routes.php');

// Guest routes (only for non-authenticated users)
$router->group([GuestMiddleware::class], function($router) {
    $router->get('/login', __DIR__ . '/../public/modules/login.php');
    $router->post('/login', __DIR__ . '/../public/modules/login.php');
});

// Authenticated routes
$router->group([AuthMiddleware::class], function($router) {
    // Home/Dashboard
    $router->get('/home', __DIR__ . '/../public/index.php');
    $router->get('/dashboard', __DIR__ . '/../public/index.php');
    
    // Logout
    $router->get('/logout', __DIR__ . '/../public/modules/logout.php');
    
    // User routes (for normal users)
    $router->get('/showdata', __DIR__ . '/../public/showdata.php');
    $router->get('/apply-fix', __DIR__ . '/../public/apply_fix.php');
    $router->post('/apply-fix', __DIR__ . '/../public/apply_fix.php');
    $router->get('/take-back', __DIR__ . '/../public/take_back.php');
    $router->post('/take-back', __DIR__ . '/../public/take_back.php');
});

// Admin only routes
$router->group([AuthMiddleware::class, AdminMiddleware::class], function($router) {
    // Users management
    $router->get('/users', __DIR__ . '/../public/modules/users/index.php');
    $router->get('/users/add', __DIR__ . '/../public/modules/users/add_user.php');
    $router->post('/users/add', __DIR__ . '/../public/modules/users/add_user.php');
    $router->get('/users/edit/{id}', __DIR__ . '/../public/modules/users/edit_user.php');
    $router->post('/users/edit/{id}', __DIR__ . '/../public/modules/users/edit_user.php');
    $router->get('/users/delete/{id}', __DIR__ . '/../public/modules/users/delete_user.php');
    
    // Products/Peripherals management
    $router->get('/products', __DIR__ . '/../public/modules/products/index.php');
    $router->get('/peripherals', __DIR__ . '/../public/modules/products/index.php');
    $router->get('/products/add', __DIR__ . '/../public/modules/products/add_product.php');
    $router->post('/products/add', __DIR__ . '/../public/modules/products/add_product.php');
    $router->get('/products/edit/{id}', __DIR__ . '/../public/modules/products/update_product.php');
    $router->post('/products/edit/{id}', __DIR__ . '/../public/modules/products/update_product.php');
    $router->get('/products/delete/{id}', __DIR__ . '/../public/modules/products/delete_product.php');
    
    // Records management
    $router->get('/records', __DIR__ . '/../public/modules/records/index.php');
    $router->get('/records/add', __DIR__ . '/../public/modules/records/create.php');
    $router->post('/records/add', __DIR__ . '/../public/modules/records/create.php');
    $router->get('/records/edit/{id}', __DIR__ . '/../public/modules/records/edit.php');
    $router->post('/records/edit/{id}', __DIR__ . '/../public/modules/records/edit.php');
    $router->get('/records/delete/{id}', __DIR__ . '/../public/modules/records/delete.php');
    
    // Approve repairs
    $router->get('/approve', __DIR__ . '/../public/approve.php');
    $router->post('/approve', __DIR__ . '/../public/approve.php');
});

return $router;
