<?php
// Simple health check endpoint for Railway
header('Content-Type: application/json');

try {
    // Check if we can load environment variables
    // Check environment variables (Railway populates $_SERVER/getenv)
    $host = $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?? 'not set';
    $db   = $_SERVER['DB_NAME'] ?? getenv('DB_NAME') ?? 'not set';
    
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
