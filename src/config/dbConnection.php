<?php
/**
 * MySQL Database Connection
 * Compatible with Railway MySQL and local XAMPP
 */

// Get environment variables (Railway MYSQL* variables take priority)
$host = $_SERVER['MYSQLHOST'] ?? getenv('MYSQLHOST') ?? $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost';
$db   = $_SERVER['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?? $_SERVER['DB_NAME'] ?? getenv('DB_NAME') ?? 'asetik_v2';
$user = $_SERVER['MYSQLUSER'] ?? getenv('MYSQLUSER') ?? $_SERVER['DB_USER'] ?? getenv('DB_USER') ?? 'root';
$pass = $_SERVER['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD') ?? $_SERVER['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? '';
$port = $_SERVER['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? $_SERVER['DB_PORT'] ?? getenv('DB_PORT') ?? '3306';

// For local development with .env file
if (empty($pass) && file_exists(__DIR__ . '/../../.env')) {
    $envFile = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envFile as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        if ($key === 'DB_HOST' || $key === 'MYSQLHOST') $host = $value;
        if ($key === 'DB_NAME' || $key === 'MYSQLDATABASE') $db = $value;
        if ($key === 'DB_USER' || $key === 'MYSQLUSER') $user = $value;
        if ($key === 'DB_PASSWORD' || $key === 'MYSQLPASSWORD') $pass = $value;
        if ($key === 'DB_PORT' || $key === 'MYSQLPORT') $port = $value;
    }
}

// Create MySQL DSN
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

// PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // More detailed error for debugging
    $error = "Connection failed: " . $e->getMessage();
    $error .= "<br>Host: $host:$port";
    $error .= "<br>Database: $db";
    $error .= "<br>User: $user";
    
    // Don't show password in error
    if (empty($pass)) {
        $error .= "<br><strong>Password is empty! Please check your .env file.</strong>";
    }
    
    die($error);
}
?>
