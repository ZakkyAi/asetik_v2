<?php
/**
 * Test MySQL connection
 */

echo "<h2>Testing MySQL Connection</h2>";

// Load .env
$host = 'localhost';
$db   = 'asetik';
$user = 'root';
$pass = '';
$port = '3306';

if (file_exists(__DIR__ . '/../.env')) {
    $envFile = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
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

echo "<p><strong>Configuration:</strong></p>";
echo "<ul>";
echo "<li>Host: $host</li>";
echo "<li>Port: $port</li>";
echo "<li>Database: $db</li>";
echo "<li>User: $user</li>";
echo "<li>Password: " . (empty($pass) ? '(empty)' : '(set)') . "</li>";
echo "</ul>";

// Test 1: Connect without database
echo "<h3>Test 1: Connect to MySQL Server</h3>";
try {
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "<p style='color: green;'>✓ Successfully connected to MySQL server!</p>";
    
    // Check if database exists
    echo "<h3>Test 2: Check if database '$db' exists</h3>";
    $stmt = $pdo->query("SHOW DATABASES LIKE '$db'");
    $result = $stmt->fetch();
    
    if ($result) {
        echo "<p style='color: green;'>✓ Database '$db' exists!</p>";
        
        // Test 3: Connect to the database
        echo "<h3>Test 3: Connect to database '$db'</h3>";
        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        echo "<p style='color: green;'>✓ Successfully connected to database '$db'!</p>";
        
        // Show tables
        echo "<h3>Test 4: List tables in database</h3>";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            echo "<p>Found " . count($tables) . " tables:</p>";
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>$table</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: orange;'>⚠ Database is empty (no tables found)</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ Database '$db' does NOT exist!</p>";
        echo "<p><strong>Solution:</strong> Create the database by running:</p>";
        echo "<pre>CREATE DATABASE $db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;</pre>";
        echo "<p>You can do this in phpMyAdmin or MySQL command line.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Connection failed: " . $e->getMessage() . "</p>";
    echo "<p><strong>Common solutions:</strong></p>";
    echo "<ul>";
    echo "<li>Make sure XAMPP MySQL is running</li>";
    echo "<li>Check that port 3306 is not blocked</li>";
    echo "<li>Verify username and password are correct</li>";
    echo "</ul>";
}
?>
