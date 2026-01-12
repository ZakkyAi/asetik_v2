<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header('Location: /asetik_v2/login');
    exit();
}

require_once(__DIR__ . "/../../../src/helpers.php");
require_once(__DIR__ . "/../../../src/config/dbConnection.php");

// Get ID from route parameter or GET parameter
$id = isset($id) ? $id : (isset($_GET['id']) ? $_GET['id'] : null);

if ($id) {
    // Check if the user has associated repair records
    $checkStmt = $pdo->prepare("SELECT * FROM repair WHERE id_user = :id");
    $checkStmt->execute(['id' => $id]);
    $hasRepairs = $checkStmt->fetch();

    if ($hasRepairs) {
        ?>
        <!DOCTYPE html>
        <html>
        <head><title>Delete User</title></head>
        <body>
        <script>
            alert('User cannot be deleted because they have associated repair records.');
            window.location.href = '<?= url('/users') ?>';
        </script>
        </body>
        </html>
        <?php
    } else {
        // Proceed with deleting the user
        try {
            $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $deleteStmt->execute(['id' => $id]);
            ?>
            <!DOCTYPE html>
            <html>
            <head><title>Delete User</title></head>
            <body>
            <script>
                alert('User deleted successfully.');
                window.location.href = '<?= url('/users') ?>';
            </script>
            </body>
            </html>
            <?php
        } catch (PDOException $e) {
            ?>
            <!DOCTYPE html>
            <html>
            <head><title>Delete User</title></head>
            <body>
            <script>
                alert('Error deleting user: <?= addslashes($e->getMessage()) ?>');
                window.location.href = '<?= url('/users') ?>';
            </script>
            </body>
            </html>
            <?php
        }
    }
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head><title>Delete User</title></head>
    <body>
    <script>
        alert('Invalid user ID.');
        window.location.href = '<?= url('/users') ?>';
    </script>
    </body>
    </html>
    <?php
}
?>
