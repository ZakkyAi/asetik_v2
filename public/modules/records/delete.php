<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    // Redirect to login page if not authorized
    header('Location: /asetik_v2/login');
    exit();
}

// Include helpers and database connection
require_once(__DIR__ . "/../../../src/helpers.php");
require_once(__DIR__ . "/../../../src/config/dbConnection.php");

// Get ID from route parameter or GET parameter
$recordId = isset($id) ? intval($id) : (isset($_GET['id']) ? intval($_GET['id']) : null);

// Check if 'id' is available
if ($recordId) {
    try {
        // First check if record is being used in repair table
        $checkStmt = $pdo->prepare("SELECT COUNT(*) as count FROM repair WHERE id_record = :id");
        $checkStmt->execute(['id' => $recordId]);
        $result = $checkStmt->fetch();
        
        if ($result['count'] > 0) {
            ?>
            <!DOCTYPE html>
            <html>
            <head><title>Delete Record</title></head>
            <body>
            <script>
                alert('Cannot delete this record because it has <?= $result['count'] ?> associated repair(s). Please delete those repairs first.');
                window.location.href = '<?= url('/records') ?>';
            </script>
            </body>
            </html>
            <?php
            exit();
        }
        
        // If no dependencies, proceed with deletion
        $query = "DELETE FROM records WHERE id_records = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $recordId]);
        ?>
        <!DOCTYPE html>
        <html>
        <head><title>Delete Record</title></head>
        <body>
        <script>
            alert('Record deleted successfully!');
            window.location.href = '<?= url('/records') ?>';
        </script>
        </body>
        </html>
        <?php
        exit();
    } catch (PDOException $e) {
        // Handle errors during execution
        $errorMsg = "Failed to delete the record.";
        if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
            $errorMsg = "Cannot delete this record because it is being used elsewhere in the system.";
        }
        ?>
        <!DOCTYPE html>
        <html>
        <head><title>Delete Record</title></head>
        <body>
        <script>
            alert('<?= addslashes($errorMsg) ?>');
            window.location.href = '<?= url('/records') ?>';
        </script>
        </body>
        </html>
        <?php
        exit();
    }
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head><title>Delete Record</title></head>
    <body>
    <script>
        alert('No record ID specified.');
        window.location.href = '<?= url('/records') ?>';
    </script>
    </body>
    </html>
    <?php
    exit();
}
?>
