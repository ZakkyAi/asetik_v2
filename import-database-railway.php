<?php
// Railway Database Import Script
// This script imports the SQL file to Railway MySQL

echo "=== Railway Database Import ===\n\n";

// Get database credentials from environment
$host = getenv('MYSQLHOST');
$port = getenv('MYSQLPORT');
$db   = getenv('MYSQLDATABASE');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');

echo "Connecting to MySQL...\n";
echo "Host: $host\n";
echo "Database: $db\n\n";

try {
    // Connect to MySQL
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "Connected successfully!\n\n";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db`");
    $pdo->exec("USE `$db`");
    
    echo "Using database: $db\n\n";
    
    // Read SQL file
    $sqlFile = __DIR__ . '/database/asetik (9).sql';
    if (!file_exists($sqlFile)) {
        die("Error: SQL file not found at $sqlFile\n");
    }
    
    echo "Reading SQL file...\n";
    $sql = file_get_contents($sqlFile);
    
    // Remove comments and split by semicolon
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && 
                   strpos($stmt, '--') !== 0 && 
                   strpos($stmt, '/*') !== 0;
        }
    );
    
    echo "Executing " . count($statements) . " SQL statements...\n\n";
    
    $executed = 0;
    foreach ($statements as $statement) {
        if (!empty(trim($statement))) {
            try {
                $pdo->exec($statement);
                $executed++;
                if ($executed % 10 == 0) {
                    echo "Executed $executed statements...\n";
                }
            } catch (PDOException $e) {
                // Skip errors for statements that might already exist
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo "Warning: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "\n✅ Database import completed successfully!\n";
    echo "Total statements executed: $executed\n";
    
} catch (PDOException $e) {
    die("❌ Error: " . $e->getMessage() . "\n");
}
