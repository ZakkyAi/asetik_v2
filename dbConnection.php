<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load .env file only if it exists (for local development)
// On Railway, environment variables are already set
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Get environment variables (from .env file or Railway)
$host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? null);
$db   = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? null);
$user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? null);
$pass = getenv('DB_PASSWORD') ?: ($_ENV['DB_PASSWORD'] ?? null);
$port = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '5432');

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
