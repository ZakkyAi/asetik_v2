<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Connection Test</title></head><body>";
echo "<h1>Testing Supabase Connection</h1>";

require_once __DIR__ . '/vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    echo "<p>✓ .env file loaded</p>";
    
    $host = $_ENV['DB_HOST'];
    $db   = $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];
    $pass = $_ENV['DB_PASS'];
    $port = $_ENV['DB_PORT'] ?? '5432';
    
    echo "<p>Host: $host</p>";
    echo "<p>Database: $db</p>";
    echo "<p>User: $user</p>";
    echo "<p>Port: $port</p>";
    
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
