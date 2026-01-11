<?php
/**
 * Create New Admin User: admin2 / admin2
 * This creates a new admin user in the asetik_v2 database
 */

require_once(__DIR__ . "/src/config/dbConnection.php");

try {
    // Check if admin2 already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin2'");
    $stmt->execute();
    $existing = $stmt->fetch();
    
    if ($existing) {
        echo "<h2>⚠️ User Already Exists</h2>";
        echo "<p>Username 'admin2' already exists in the database.</p>";
        echo "<p>Updating password to 'admin2'...</p>";
        
        // Update existing user's password
        $password_hash = password_hash('admin2', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_user = :password WHERE username = 'admin2'");
        $stmt->execute(['password' => $password_hash]);
        
        echo "<p style='color:green;'>✅ Password updated successfully!</p>";
    } else {
        echo "<h2>Creating New Admin User</h2>";
        
        // Generate password hash for 'admin2'
        $password_hash = password_hash('admin2', PASSWORD_DEFAULT);
        
        // Insert new admin user
        $stmt = $pdo->prepare("
            INSERT INTO users (
                name, 
                age, 
                divisi, 
                email, 
                description, 
                photo, 
                password_user, 
                username, 
                badge, 
                level
            ) VALUES (
                :name,
                :age,
                :divisi,
                :email,
                :description,
                :photo,
                :password,
                :username,
                :badge,
                :level
            )
        ");
        
        $stmt->execute([
            'name' => 'Admin 2',
            'age' => 25,
            'divisi' => 'IT Administrator',
            'email' => 'admin2@asetik.com',
            'description' => 'Secondary Admin Account',
            'photo' => 'profile.jpg',
            'password' => $password_hash,
            'username' => 'admin2',
            'badge' => 'ADM-002',
            'level' => 'admin'
        ]);
        
        echo "<p style='color:green;'>✅ New admin user created successfully!</p>";
    }
    
    // Verify the user
    $stmt = $pdo->prepare("SELECT id, username, name, email, level, password_user FROM users WHERE username = 'admin2'");
    $stmt->execute();
    $user = $stmt->fetch();
    
    echo "<hr>";
    echo "<h3>User Details:</h3>";
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    echo "<tr><td><strong>ID</strong></td><td>{$user['id']}</td></tr>";
    echo "<tr><td><strong>Username</strong></td><td>{$user['username']}</td></tr>";
    echo "<tr><td><strong>Name</strong></td><td>{$user['name']}</td></tr>";
    echo "<tr><td><strong>Email</strong></td><td>{$user['email']}</td></tr>";
    echo "<tr><td><strong>Level</strong></td><td>{$user['level']}</td></tr>";
    echo "</table>";
    
    // Test the password
    $test = password_verify('admin2', $user['password_user']);
    echo "<p>Password 'admin2' verification: " . ($test ? '✅ WORKS' : '❌ FAILED') . "</p>";
    
    echo "<hr>";
    echo "<h3>✅ Login Credentials:</h3>";
    echo "<div style='background:#e8f5e9;padding:20px;border-radius:5px;'>";
    echo "<p style='font-size:18px;margin:5px 0;'><strong>Username:</strong> <code style='background:#fff;padding:5px 10px;border-radius:3px;'>admin2</code></p>";
    echo "<p style='font-size:18px;margin:5px 0;'><strong>Password:</strong> <code style='background:#fff;padding:5px 10px;border-radius:3px;'>admin2</code></p>";
    echo "</div>";
    
    echo "<p style='margin-top:20px;'><a href='/asetik_v2/login' style='display:inline-block;padding:12px 24px;background:#3b82f6;color:white;text-decoration:none;border-radius:5px;font-weight:bold;'>Go to Login Page</a></p>";
    
    echo "<hr>";
    echo "<p style='color:red;'><strong>⚠️ SECURITY:</strong> Delete this file (create_admin2.php) after use!</p>";
    
} catch (PDOException $e) {
    echo "<h2>❌ Error</h2>";
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Make sure the 'asetik_v2' database exists and has the 'users' table.</p>";
}
?>
