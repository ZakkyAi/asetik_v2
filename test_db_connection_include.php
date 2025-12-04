<?php
// Test script that includes dbConnection.php to verify the fix
echo "Testing dbConnection.php logic...\n";

// Mock $_SERVER if needed, but dbConnection.php handles getenv
// We need to make sure we don't die() if we can avoid it, but dbConnection.php has die() calls.
// We can't easily catch die().
// But we can run it in a separate process.

include 'dbConnection.php';

if (isset($pdo)) {
    echo "SUCCESS: \$pdo is set.\n";
    echo "Connected to: " . $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "\n";
} else {
    echo "FAILURE: \$pdo is not set (and script didn't die?).\n";
}
