<?php
/**
 * Restore Admin Password to 'admin'
 * This reverses the previous change and restores the original password
 */

require_once(__DIR__ . "/src/config/dbConnection.php");

try {
    // Restore the original password hash for 'admin'
    // This is the hash from the original database dump
    $original_hash = '$2y$10$ZdWSNZm/a6aB4BWkbUavPOb1EWuens9ENzzAWBiEAv7KhJZLLCeH2';
    
    // Update the admin user back to original password
    $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE username = 'admin'");
    $stmt->execute(['password' => $original_hash]);
    
    echo "<h2>✅ Password Restored Successfully!</h2>";
    echo "<p>Admin password has been restored to: <strong>admin</strong></p>";
    echo "<p>Original hash restored: <code>$original_hash</code></p>";
    
    // Verify the restoration
    $stmt = $pdo->prepare("SELECT id, username, name, level, password FROM users WHERE username = 'admin'");
    $stmt->execute();
    $user = $stmt->fetch();
    
    echo "<hr>";
    echo "<h3>Verification:</h3>";
    echo "<p>User ID: {$user['id']}</p>";
    echo "<p>Username: {$user['username']}</p>";
    echo "<p>Name: {$user['name']}</p>";
    echo "<p>Level: {$user['level']}</p>";
    
    // Test the restored password
    $test = password_verify('admin', $user['password']);
    echo "<p>Password 'admin' verification: " . ($test ? '✅ WORKS' : '❌ FAILED') . "</p>";
    
    echo "<hr>";
    echo "<p><strong>Admin credentials are now:</strong></p>";
    echo "<ul>";
    echo "<li>Username: <strong>admin</strong></li>";
    echo "<li>Password: <strong>admin</strong></li>";
    echo "</ul>";
    
    echo "<hr>";
    echo "<p style='color:green;'><strong>✅ Database has been restored to original state</strong></p>";
    echo "<p style='color:red;'><strong>⚠️ IMPORTANT:</strong> Delete this file (restore_admin_password.php) after use for security!</p>";
    
} catch (PDOException $e) {
    echo "<h2>❌ Error restoring password</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
