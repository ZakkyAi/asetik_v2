<?php
// Simple health check endpoint for Railway
header('Content-Type: application/json');

try {
    // Check if we can load environment variables
    // Check environment variables (Railway populates $_SERVER/getenv)
    $host = $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?? 'not set';
    $db   = $_SERVER['DB_NAME'] ?? getenv('DB_NAME') ?? 'not set';
    
    // Force IPv4 resolution
    $hostIPv4 = $host;
    $dns_records = [];
    if (function_exists('dns_get_record')) {
        $dns_records = dns_get_record($host, DNS_A);
        if (!empty($dns_records) && isset($dns_records[0]['ip'])) {
            $hostIPv4 = $dns_records[0]['ip'];
        }
    }
    
    if ($hostIPv4 === $host) {
        $ip = gethostbyname($host);
        if ($ip !== $host) {
            $hostIPv4 = $ip;
        }
    }
    
    $response = [
        'status' => 'ok',
        'message' => 'Application is running',
        'php_version' => phpversion(),
        'db_host' => $host,
        'db_host_resolved' => $hostIPv4,
        'dns_debug' => $dns_records,
        'db_name' => $db,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    // Optional: Only check DB if ?check_db=1 is passed
    if (isset($_GET['check_db'])) {
        try {
            $dsn = "pgsql:host=$hostIPv4;port=5432;dbname=$db;sslmode=require";
            $pass = $_SERVER['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? '';
            $user = $_SERVER['DB_USER'] ?? getenv('DB_USER') ?? 'postgres';
            $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_TIMEOUT => 3]);
            $response['db_connection'] = 'success';
        } catch (Exception $e) {
            $response['db_connection'] = 'failed: ' . $e->getMessage();
        }
    }
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
