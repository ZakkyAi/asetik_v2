<?php
/**
 * Railway-optimized database connection
 * Works with Railway environment variables WITHOUT dotenv
 */

// Get environment variables from Railway (or system)
$host = $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost';
$db   = $_SERVER['DB_NAME'] ?? getenv('DB_NAME') ?? 'postgres';
$user = $_SERVER['DB_USER'] ?? getenv('DB_USER') ?? 'postgres';  
$pass = $_SERVER['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? '';
$port = $_SERVER['DB_PORT'] ?? getenv('DB_PORT') ?? '5432';

// For local development with .env file
if (empty($pass) && file_exists(__DIR__ . '/.env')) {
    $envFile = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envFile as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        if ($key === 'DB_HOST') $host = $value;
        if ($key === 'DB_NAME') $db = $value;
        if ($key === 'DB_USER') $user = $value;
        if ($key === 'DB_PASSWORD') $pass = $value;
        if ($key === 'DB_PORT') $port = $value;
    }
}

// Robust IPv4 resolution function
function getIPv4($hostname) {
    $hostname = trim($hostname);
    
    // Method 1: gethostbynamel (returns list of IPv4)
    $ips = gethostbynamel($hostname);
    if ($ips && is_array($ips) && !empty($ips)) {
        return $ips[0];
    }
    
    // Method 2: dns_get_record (DNS_A for IPv4)
    if (function_exists('dns_get_record')) {
        $records = dns_get_record($hostname, DNS_A);
        if ($records && !empty($records)) {
            foreach ($records as $r) {
                if (isset($r['ip'])) return $r['ip'];
            }
        }
    }
    
    // Method 3: shell_exec with dig (if available)
    if (function_exists('shell_exec')) {
        $ip = trim(shell_exec("dig +short A " . escapeshellarg($hostname)));
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $ip;
        }
        
        // Method 4: getent
        $output = shell_exec("getent hosts " . escapeshellarg($hostname));
        if ($output) {
            $parts = preg_split('/\s+/', trim($output));
            if (isset($parts[0]) && filter_var($parts[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return $parts[0];
            }
        }
    }
    
    // Fallback: gethostbyname (might return hostname on failure)
    return gethostbyname($hostname);
}

$hostIPv4 = getIPv4($host);
$dsn = "pgsql:host=$hostIPv4;port=$port;dbname=$db;sslmode=require";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "<br>Host: $host<br>DB: $db<br>User: $user");
}
?>
