<?php
/**
 * Update Admin Password to '123'
 * Run this file once to update the admin password
 */

require_once(__DIR__ . "/src/config/dbConnection.php");

try {
    // Generate new hash for password '123'
    $new_password = '123';
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update the admin user
    $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE username = 'admin'");
    $stmt->execute(['password' => $new_hash]);
    
    echo "<h2>✅ Password Update Successful!</h2>";
    echo "<p>Admin password has been updated to: <strong>123</strong></p>";
    echo "<p>New hash: <code>$new_hash</code></p>";
    
    // Verify the update
    $stmt = $pdo->prepare("SELECT id, username, name, level, password FROM users WHERE username = 'admin'");
    $stmt->execute();
    $user = $stmt->fetch();
    
    echo "<hr>";
    echo "<h3>Verification:</h3>";
    echo "<p>User ID: {$user['id']}</p>";
    echo "<p>Username: {$user['username']}</p>";
    echo "<p>Name: {$user['name']}</p>";
    echo "<p>Level: {$user['level']}</p>";
    
    // Test the new password
    $test = password_verify('123', $user['password']);
    echo "<p>Password '123' verification: " . ($test ? '✅ WORKS' : '❌ FAILED') . "</p>";
    
    echo "<hr>";
    echo "<p><strong>You can now login with:</strong></p>";
    echo "<ul>";
    echo "<li>Username: <strong>admin</strong></li>";
    echo "<li>Password: <strong>123</strong></li>";
    echo "</ul>";
    echo "<p><a href='/asetik_v2/login' style='display:inline-block;margin-top:20px;padding:10px 20px;background:#3b82f6;color:white;text-decoration:none;border-radius:5px;'>Go to Login Page</a></p>";
    
    echo "<hr>";
    echo "<p style='color:red;'><strong>⚠️ IMPORTANT:</strong> Delete this file (update_admin_password.php) after use for security!</p>";
    
} catch (PDOException $e) {
    echo "<h2>❌ Error updating password</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
