<?php
/**
 * Supabase PostgreSQL Connection Test
 * This script tests the connection to your Supabase database
 */

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
    return true;
}

// Load .env file
$envLoaded = loadEnv(__DIR__ . '/.env');

// Database configuration - try to load from .env first, fallback to hardcoded values
$host = $_ENV['DB_HOST'] ?? 'db.jadwfkeagcceroypuqer.supabase.co';
$port = $_ENV['DB_PORT'] ?? '5432';
$dbname = $_ENV['DB_NAME'] ?? 'postgres';
$user = $_ENV['DB_USER'] ?? 'postgres';
$password = $_ENV['DB_PASSWORD'] ?? '';

// Display connection info (without password)
echo "=== Supabase Connection Test ===\n\n";
echo "Environment file: " . ($envLoaded ? "✓ Loaded from .env" : "✗ Not found, using defaults") . "\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "Database: $dbname\n";
echo "User: $user\n";
echo "Password: " . (empty($password) ? "NOT SET" : "***SET***") . "\n";
echo str_repeat("-", 50) . "\n\n";

// Check if password is set
if (empty($password)) {
    echo "ERROR: Please set DB_PASSWORD in your .env file before testing!\n";
    echo "You can find your password in Supabase Dashboard → Settings → Database → Database Password\n";
    exit(1);
}

try {
    // Create PDO connection string
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    
    echo "Attempting to connect to Supabase...\n\n";
    
    // Create PDO instance
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    echo "✓ SUCCESS: Connected to Supabase database successfully!\n\n";
    
    // Test query - Get PostgreSQL version
    $stmt = $pdo->query('SELECT version()');
    $version = $stmt->fetch();
    echo "PostgreSQL Version:\n" . $version['version'] . "\n\n";
    
    // Test query - Get current database
    $stmt = $pdo->query('SELECT current_database()');
    $current_db = $stmt->fetch();
    echo "Current Database: " . $current_db['current_database'] . "\n";
    
    // Test query - Get current user
    $stmt = $pdo->query('SELECT current_user');
    $current_user = $stmt->fetch();
    echo "Current User: " . $current_user['current_user'] . "\n\n";
    
    // List all tables in the database
    $stmt = $pdo->query("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        ORDER BY table_name
    ");
    $tables = $stmt->fetchAll();
    
    echo "Tables in Database:\n";
    if (count($tables) > 0) {
        foreach ($tables as $table) {
            echo "  - " . $table['table_name'] . "\n";
        }
    } else {
        echo "  (No tables found in the public schema)\n";
    }
    
    // Connection info
    echo "\n" . str_repeat("-", 50) . "\n";
    echo "Connection Details:\n";
    echo "PDO Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";
    echo "Server Info: " . $pdo->getAttribute(PDO::ATTR_SERVER_INFO) . "\n";
    echo "Connection Status: " . $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "\n";
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "✓ All tests passed! Your Supabase connection is working correctly.\n";
    echo str_repeat("=", 50) . "\n";
    
} catch (PDOException $e) {
    echo "✗ CONNECTION FAILED\n\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n\n";
    
    echo str_repeat("-", 50) . "\n";
    echo "Troubleshooting Tips:\n";
    echo "  - Make sure your password is correct\n";
    echo "  - Check if your IP address is allowed in Supabase\n";
    echo "  - Verify that the PostgreSQL PDO extension is installed (pdo_pgsql)\n";
    echo "  - Ensure SSL/TLS is enabled on your server\n";
    
    exit(1);
}

