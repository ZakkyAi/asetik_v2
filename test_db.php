<?php
/**
 * Test database connection
 */

require_once(__DIR__ . "/src/config/dbConnection.php");

echo "<h2>Database Connection Test</h2>";

try {
    // Test connection
    $result = $pdo->query("SELECT 1 as test");
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    // Test users table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $count = $stmt->fetch();
    echo "<p>✓ Users table accessible. Total users: " . $count['count'] . "</p>";
    
    // Test products table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
    $count = $stmt->fetch();
    echo "<p>✓ Products table accessible. Total products: " . $count['count'] . "</p>";
    
    // Test records table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM records");
    $count = $stmt->fetch();
    echo "<p>✓ Records table accessible. Total records: " . $count['count'] . "</p>";
    
    echo "<h3>Sample User Data:</h3>";
    $stmt = $pdo->query("SELECT id, name, username, level FROM users LIMIT 3");
    $users = $stmt->fetchAll();
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Username</th><th>Level</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['id']) . "</td>";
        echo "<td>" . htmlspecialchars($user['name']) . "</td>";
        echo "<td>" . htmlspecialchars($user['username']) . "</td>";
        echo "<td>" . htmlspecialchars($user['level']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p style='color: green; font-weight: bold;'>All tests passed! The database connection is working correctly.</p>";
    echo "<p><a href='index.php'>Go to Home</a> | <a href='public/login.php'>Go to Login</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
