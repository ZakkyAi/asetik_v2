<?php
/**
 * Database connection - works for both local XAMPP and Railway deployment
 */

// Priority 1: Railway environment variables (from $_SERVER or getenv())
// Priority 2: .env file (for local development)
// Priority 3: Default values (XAMPP defaults)

// Load .env file FIRST if it exists (for local development)
if (file_exists(__DIR__ . '/../../.env')) {
    $envFile = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envFile as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        // Set all environment variables globally
        if (!isset($_SERVER[$key]) && !getenv($key)) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

// Now get database variables (after .env is loaded)
$host = $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost';
$db   = $_SERVER['DB_NAME'] ?? getenv('DB_NAME') ?? 'asetik';
$user = $_SERVER['DB_USER'] ?? getenv('DB_USER') ?? 'root';
$pass = $_SERVER['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? '';
$port = $_SERVER['DB_PORT'] ?? getenv('DB_PORT') ?? '3306';

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
