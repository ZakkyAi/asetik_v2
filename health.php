<?php
// Simple health check endpoint for Railway
header('Content-Type: application/json');

try {
    // Check if we can load environment variables
    // Check environment variables (Railway populates $_SERVER/getenv)
    $host = $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?? 'not set';
    $db   = $_SERVER['DB_NAME'] ?? getenv('DB_NAME') ?? 'not set';
    
    // Force IPv4 resolution
    $hostIPv4 = gethostbyname($host);
    
    // Try to connect to DB to verify health
    $dsn = "pgsql:host=$hostIPv4;port=5432;dbname=$db;sslmode=require";
    $pass = $_SERVER['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? '';
    $user = $_SERVER['DB_USER'] ?? getenv('DB_USER') ?? 'postgres';
    
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_TIMEOUT => 3]);
    
    echo json_encode([
        'status' => 'ok',
        'message' => 'Application is running',
        'php_version' => phpversion(),
        'db_host' => $host,
        'db_host_resolved' => $hostIPv4,
        'db_name' => $db,
        'db_connection' => 'success',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
