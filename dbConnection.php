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

// Force IPv4 resolution to avoid IPv6 connection issues on Railway
$hostIPv4 = $host;
// Try dns_get_record for A records (IPv4)
if (function_exists('dns_get_record')) {
    $records = dns_get_record($host, DNS_A);
    if (!empty($records) && isset($records[0]['ip'])) {
        $hostIPv4 = $records[0]['ip'];
    }
}

// Fallback to gethostbyname if dns_get_record failed or didn't find anything
if ($hostIPv4 === $host) {
    $ip = gethostbyname($host);
    if ($ip !== $host) {
        $hostIPv4 = $ip;
    }
}

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
