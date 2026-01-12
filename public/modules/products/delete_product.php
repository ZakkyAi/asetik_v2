<?php
// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is not logged in and destroy the session
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header('Location: /asetik_v2/login');
    exit();
}

require_once(__DIR__ . "/../../../src/helpers.php");
require_once(__DIR__ . "/../../../src/config/dbConnection.php");

// Get ID from route parameter or GET parameter
$id = isset($id) ? $id : (isset($_GET['id']) ? $_GET['id'] : null);

if ($id) {
    try {
        // First check if product is being used in records
        $checkStmt = $pdo->prepare("SELECT COUNT(*) as count FROM records WHERE id_products = :id");
        $checkStmt->execute(['id' => $id]);
        $result = $checkStmt->fetch();
        
        if ($result['count'] > 0) {
            ?>
            <!DOCTYPE html>
            <html>
            <head><title>Delete Product</title></head>
            <body>
            <script>
                alert('Cannot delete this product because it is being used in <?= $result['count'] ?> record(s). Please delete or update those records first.');
                window.location.href = '<?= url('/products') ?>';
            </script>
            </body>
            </html>
            <?php
            exit();
        }
        
        // If no dependencies, proceed with deletion
        $query = "DELETE FROM products WHERE id = :id";
        $pdo->prepare($query)->execute(['id' => $id]);
        ?>
        <!DOCTYPE html>
        <html>
        <head><title>Delete Product</title></head>
        <body>
        <script>
            alert('Product deleted successfully!');
            window.location.href = '<?= url('/products') ?>';
        </script>
        </body>
        </html>
        <?php
    } catch (PDOException $e) {
        // Handle any other database errors
        $errorMsg = "Error deleting product.";
        if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
            $errorMsg = "Cannot delete this product because it is being used elsewhere in the system.";
        }
        ?>
        <!DOCTYPE html>
        <html>
        <head><title>Delete Product</title></head>
        <body>
        <script>
            alert('<?= addslashes($errorMsg) ?>');
            window.location.href = '<?= url('/products') ?>';
        </script>
        </body>
        </html>
        <?php
    }
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head><title>Delete Product</title></head>
    <body>
    <script>
        alert('Invalid product ID.');
        window.location.href = '<?= url('/products') ?>';
    </script>
    </body>
    </html>
    <?php
}
?>
