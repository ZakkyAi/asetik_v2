<?php
/**
 * MySQL database connection for XAMPP
 */

// Get environment variables from .env file
$host = 'localhost';
$db   = 'asetik';
$user = 'root';
$pass = '';
$port = '3306';

// Load .env file if it exists
if (file_exists(__DIR__ . '/../../.env')) {
    $envFile = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envFile as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        
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

// MySQL DSN (no sslmode for local MySQL)
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_PERSISTENT         => true,  // Enable persistent connections
    PDO::ATTR_TIMEOUT            => 30,    // Connection timeout in seconds
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // Test the connection
    $pdo->query("SELECT 1");
} catch (\PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "<br>Host: $host<br>DB: $db<br>User: $user");
}
?>
