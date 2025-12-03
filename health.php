<?php
// Simple health check endpoint for Railway
header('Content-Type: application/json');

try {
    // Check if we can load environment variables
    // Check environment variables (Railway populates $_SERVER/getenv)
    $host = $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?? 'not set';
    $db   = $_SERVER['DB_NAME'] ?? getenv('DB_NAME') ?? 'not set';
    
    // Robust IPv4 resolution
    function getIPv4($hostname) {
        $hostname = trim($hostname);
        $debug = [];
        
        // Method 1: gethostbynamel
        $ips = gethostbynamel($hostname);
        if ($ips && is_array($ips) && !empty($ips)) {
            return ['ip' => $ips[0], 'method' => 'gethostbynamel'];
        }
        
        // Method 2: dns_get_record
        if (function_exists('dns_get_record')) {
            $records = dns_get_record($hostname, DNS_A);
            if ($records && !empty($records)) {
                foreach ($records as $r) {
                    if (isset($r['ip'])) return ['ip' => $r['ip'], 'method' => 'dns_get_record'];
                }
            }
        }
        
        // Method 3: shell_exec dig
        if (function_exists('shell_exec')) {
            $ip = trim(shell_exec("dig +short A " . escapeshellarg($hostname)));
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return ['ip' => $ip, 'method' => 'dig'];
            }
        }
        
        // Fallback
        return ['ip' => gethostbyname($hostname), 'method' => 'fallback'];
    }

    $resolution = getIPv4($host);
    $hostIPv4 = $resolution['ip'];
    
    $response = [
        'status' => 'ok',
        'message' => 'Application is running',
        'php_version' => phpversion(),
        'db_host' => $host,
        'db_host_resolved' => $hostIPv4,
        'resolution_method' => $resolution['method'],
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
