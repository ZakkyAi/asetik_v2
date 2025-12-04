<?php
// Include the database connection file
// Include the database connection file
// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is not logged in and destroy the session
if (!isset($_SESSION['user_id'])) {
    session_destroy();
    // Redirect to login page (optional)
    header('Location: ../auth/login.php');
    exit();  // Make sure to stop the script after redirection
}
require_once(__DIR__ . "/../../config/dbConnection.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Delete product from the database
    $query = "DELETE FROM products WHERE id = :id";
    try {
        $pdo->prepare($query)->execute(['id' => $id]);
    } catch (PDOException $e) {
        die("Error deleting product: " . $e->getMessage());
    }

    echo "Product deleted successfully!";
    header('Location: index.php'); // Redirect to the main page
}
?>
