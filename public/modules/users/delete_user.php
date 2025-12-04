<?php
require_once(__DIR__ . "/../../../src/config/dbConnection.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Check if the user has associated repair records
    $checkStmt = $pdo->prepare("SELECT * FROM repair WHERE id_user = :id");
    $checkStmt->execute(['id' => $id]);
    $hasRepairs = $checkStmt->fetch();

    if ($hasRepairs) {
        // Display prompt that user cannot be deleted due to associated repair records
        echo "<script>
                alert('User cannot be deleted because they have associated repair records.');
                window.location.href = 'index.php';
              </script>";
    } else {
        // Proceed with deleting the user
        try {
            $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $deleteStmt->execute(['id' => $id]);
            echo "<script>
                    alert('User deleted successfully.');
                    window.location.href = 'index.php';
                  </script>";
        } catch (PDOException $e) {
            echo "Error deleting user: " . $e->getMessage();
        }
    }
} else {
    echo "<script>
            alert('Invalid user ID.');
            window.location.href = 'index.php';
          </script>";
}
?>
