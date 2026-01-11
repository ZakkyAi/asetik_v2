<?php
/**
 * Check Database Structure
 * This will show us what columns actually exist in the users table
 */

require_once(__DIR__ . "/src/config/dbConnection.php");

try {
    echo "<h2>Database Structure Check</h2>";
    
    // Get column information
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll();
    
    echo "<h3>Columns in 'users' table:</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td><strong>{$column['Field']}</strong></td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Try to get a sample user
    echo "<hr>";
    echo "<h3>Sample User Data (admin):</h3>";
    $stmt = $pdo->query("SELECT * FROM users WHERE username = 'admin' LIMIT 1");
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
        foreach ($user as $key => $value) {
            if (!is_numeric($key)) {
                $display_value = (strlen($value) > 60) ? substr($value, 0, 60) . '...' : $value;
                echo "<tr><td><strong>$key</strong></td><td>$display_value</td></tr>";
            }
        }
        echo "</table>";
    } else {
        echo "<p>No admin user found!</p>";
    }
    
} catch (PDOException $e) {
    echo "<h2>❌ Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
