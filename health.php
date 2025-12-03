<?php
// Simple health check endpoint for Railway
header('Content-Type: application/json');

try {
    // Check if we can load environment variables
    require_once __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    // Check database connection
    $host = $_ENV['DB_HOST'] ?? 'not set';
    $db   = $_ENV['DB_NAME'] ?? 'not set';
    
    echo json_encode([
        'status' => 'ok',
        'message' => 'Application is running',
        'php_version' => phpversion(),
        'db_host' => $host,
        'db_name' => $db,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
