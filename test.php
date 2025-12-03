<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Connection Test</title></head><body>";
echo "<h1>Testing Supabase Connection</h1>";

// For local development with .env file
if (file_exists(__DIR__ . '/.env')) {
    $envFile = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envFile as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
        $_ENV[trim($key)] = trim($value);
    }
    echo "<p>✓ .env file loaded manually</p>";
}

$host = $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?? $_ENV['DB_HOST'] ?? '';
$db   = $_SERVER['DB_NAME'] ?? getenv('DB_NAME') ?? $_ENV['DB_NAME'] ?? '';
$user = $_SERVER['DB_USER'] ?? getenv('DB_USER') ?? $_ENV['DB_USER'] ?? '';
$pass = $_SERVER['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? $_ENV['DB_PASSWORD'] ?? '';
$port = $_SERVER['DB_PORT'] ?? getenv('DB_PORT') ?? $_ENV['DB_PORT'] ?? '5432';

echo "<p>Host: $host</p>";
echo "<p>Database: $db</p>";
echo "<p>User: $user</p>";
echo "<p>Port: $port</p>";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";
    
    echo "<p>Attempting connection...</p>";
    
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    
    echo "<h2 style='color:green'>✓ Connection Successful!</h2>";
    
    $result = $pdo->query("SELECT version()");
    $version = $result->fetch();
    echo "<p>PostgreSQL: " . $version['version'] . "</p>";
    
} catch (Exception $e) {
    echo "<h2 style='color:red'>✗ Connection Failed</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</body></html>";
?>
