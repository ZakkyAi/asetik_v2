<?php
// Database connection for Railway deployment
// Uses environment variables directly without dotenv

// Get environment variables directly
$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'postgres';
$user = getenv('DB_USER') ?: 'postgres';
$pass = getenv('DB_PASSWORD') ?: '';
$port = getenv('DB_PORT') ?: '5432';

// Fallback to .env file for local development only
if (file_exists(__DIR__ . '/.env') && empty($pass)) {
    require_once __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
    
    $host = getenv('DB_HOST') ?: $_ENV['DB_HOST'] ?? $host;
    $db   = getenv('DB_NAME') ?: $_ENV['DB_NAME'] ?? $db;
    $user = getenv('DB_USER') ?: $_ENV['DB_USER'] ?? $user;
    $pass = getenv('DB_PASSWORD') ?: $_ENV['DB_PASSWORD'] ?? $pass;
    $port = getenv('DB_PORT') ?: $_ENV['DB_PORT'] ?? $port;
}

$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
