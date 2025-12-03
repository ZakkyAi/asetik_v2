<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    // Redirect to login page if not authorized
    header('Location: login/login.php');
    exit();
}

// Include database connection file
require_once("../dbConnection.php");

// Check if 'id' is passed as a GET parameter
if (isset($_GET['id'])) {
    $recordId = intval($_GET['id']); // Sanitize input to prevent SQL injection

    // Prepare the delete query
    // Prepare the delete query
    $query = "DELETE FROM records WHERE id_records = :id";
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $recordId]);
        // Record deleted successfully
        header("Location: index.php?message=Record deleted successfully");
        exit();
    } catch (PDOException $e) {
        // Handle errors during execution
        header("Location: index.php?error=Failed to delete the record: " . $e->getMessage());
        exit();
    }
} else {
    // Redirect if no 'id' is provided
    header("Location: index.php?error=No record ID specified");
    exit();
}
?>
