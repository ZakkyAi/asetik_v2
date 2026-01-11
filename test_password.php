<?php
// Test password verification
$stored_hash = '$2y$10$ZdWSNZm/a6aB4BWkbUavPOb1EWuens9ENzzAWBiEAv7KhJZLLCeH2';

// Test different passwords
$test_passwords = ['123', 'admin', 'password', 'admin123', '12345'];

echo "<h2>Password Hash Testing</h2>";
echo "<p>Stored hash from database: <code>$stored_hash</code></p>";
echo "<hr>";

foreach ($test_passwords as $password) {
    $result = password_verify($password, $stored_hash);
    $status = $result ? '✅ MATCH' : '❌ NO MATCH';
    echo "<p>Testing password '<strong>$password</strong>': $status</p>";
}

echo "<hr>";
echo "<h3>Generate new hash for '123':</h3>";
$new_hash = password_hash('123', PASSWORD_DEFAULT);
echo "<p>New hash: <code>$new_hash</code></p>";
echo "<p>Verify '123' with new hash: " . (password_verify('123', $new_hash) ? '✅ WORKS' : '❌ FAILED') . "</p>";
?>
