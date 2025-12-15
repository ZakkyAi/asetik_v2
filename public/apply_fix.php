<?php
// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    session_destroy();
    // Redirect to login page
    header('Location: modules/auth/login.php');
    exit();
}

require_once(__DIR__ . "/../src/config/dbConnection.php");

// Connect to the database
// Connection is already established in dbConnection.php as $pdo

// Get logged-in user ID
$userId = $_SESSION['user_id'];

// Fetch broken products related to the user
$brokenProductsQuery = "
    SELECT 
        records.id_records, 
        records.id_products, 
        records.no_serial, 
        records.record_time, 
        records.status AS record_status, 
        records.no_inventaris,
        records.note_record,
        products.name AS product_name, 
        products.photo AS product_photo, 
        products.description AS product_description
    FROM records
    LEFT JOIN products ON records.id_products = products.id
    WHERE records.id_users = :userId AND (records.status = 'broken' OR records.status = 'pending' OR records.status = 'decline')";

$stmt = $pdo->prepare($brokenProductsQuery);
$stmt->execute(['userId' => $userId]);
$brokenProducts = $stmt->fetchAll();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['apply_for_repair'])) {
        $recordId = $_POST['record_id'];
        $note = $_POST['note'] ?? ''; // Retrieve the note from the form input
    
        // Update the record status to pending and save the note
        // Update the record status to pending and save the note
        $repairQuery = "UPDATE records SET status = 'pending', note_record = :note WHERE id_records = :recordId AND status = 'broken'";
        $insertRepairQuery = "INSERT INTO repair (id_user, id_record, created_at) VALUES (:userId, :recordId, NOW())";
    
        try {
            $pdo->beginTransaction();
            $pdo->prepare($repairQuery)->execute(['note' => $note, 'recordId' => $recordId]);
            $pdo->prepare($insertRepairQuery)->execute(['userId' => $userId, 'recordId' => $recordId]);
            $pdo->commit();

            echo "<script>alert('Repair request submitted successfully.');</script>";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "<script>alert('Failed to submit repair request: " . $e->getMessage() . "');</script>";
        }
    }
     elseif (isset($_POST['delete_product'])) {
        $recordId = $_POST['record_id'];

        // Update the record status to good
        // Update the record status to good
        $deleteQuery = "UPDATE records SET status = 'good' WHERE id_records = :recordId";
        try {
            $pdo->prepare($deleteQuery)->execute(['recordId' => $recordId]);
            echo "<script>alert('Product marked as good successfully.');</script>";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (Exception $e) {
            echo "<script>alert('Failed to update product status: " . $e->getMessage() . "');</script>";
        }
    } elseif (isset($_POST['decline_product'])) {
        $recordId = $_POST['record_id'];

        $declineQuery = "UPDATE records SET status = 'decline' WHERE id_records = :recordId";
        try {
            $pdo->prepare($declineQuery)->execute(['recordId' => $recordId]);
            echo "<script>alert('Product marked as good successfully.');</script>";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (Exception $e) {
            echo "<script>alert('Failed to update product status: " . $e->getMessage() . "');</script>";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asetik</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65Vohh0fJkhDi1ozh4c" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Oswald', sans-serif;
        }
        /* Sidebar styles */
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: rgb(0, 82, 170);
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            transform: translateX(0);
        }

        .sidebar.closed {
            transform: translateX(-250px);
        }

        .sidebar .nav-item {
            margin-bottom: 10px;
        }

        .sidebar .nav-link {
            color: white;
            text-decoration: none;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .content.expanded {
            margin-left: 0;
        }

        /* Toggle button styles */
        .toggle-btn {
            position: fixed;
            left: 250px;
            background-color: rgb(10, 108, 213);
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            z-index: 1000;
            transition: left 0.3s ease;
            border-radius: 3px;
        }

        .sidebar ul {
            list-style: none;
            padding-left: 2rem;
            margin: 0;
            font-family: 'Oswald', sans-serif;
        }

        .sidebar img {
            margin-left: 1rem;
            margin-bottom: 2rem;
        }
        .table th, .table td {
    text-align: center;
    vertical-align: middle;
}
table, td, th {
    border: 3px solid !important;
    border-collapse: collapse;
    padding: 5px;
}

.btn {
        display: inline-block;
        padding: 10px 20px;
        font-size: 14px;
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        border-radius: 5px;
        border: none;
        cursor: pointer;
    }

    .btn-edit {
        background-color: #007bff; /* Blue color */
        color: #fff;
    }

    .btn-edit:hover {
        background-color: #0056b3;
    }

    .btn-delete {
        background-color: #dc3545; /* Red color */
        color: #fff;
    }

    .btn-delete:hover {
        background-color: #a71d2a;
    }
    .btn-add {
    background-color: #28a745; /* Green color */
    color: white;
    font-weight: bold;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.btn-add:hover {
    background-color: #218838; /* Darker green on hover */
}

    </style>
</head>
<body>

<!-- Sidebar Toggle Button -->
<button class="toggle-btn" id="toggleSidebar">☰</button>

<!-- Sidebar Section -->
<div class="sidebar" id="sidebar">
    <div class="container-fluid">
        <?php require_once(__DIR__ . "/../src/helpers.php"); ?>
        <a class="navbar-brand" href="<?= url('/home') ?>">
            <img src="<?= asset('images/logo.png') ?>" alt="Logo" style="width: 150px;">
        </a>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="<?= url('/home') ?>">Home</a>
            </li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['level'] == 'admin'): ?>
                    <!-- Admin-specific menu items -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/users') ?>">User</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/products') ?>">Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/records') ?>">Records</a>
                    </li>
                <?php elseif ($_SESSION['level'] == 'normal_user'): ?>
                    <!-- Normal user-specific menu item -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/showdata') ?>">Show Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/apply-fix') ?>">Apply for Repair</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/take-back') ?>">Pick Up Repair</a>
                    </li>
                <?php endif; ?>
                <!-- Common Logout link for all logged-in users -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/logout') ?>">Logout</a>
                </li>
            <?php else: ?>
                <!-- Login link for non-logged-in users -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/login') ?>">Login</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<!-- Content Section -->
<div class="content" id="content">



<h1>Ajukan Perbaikan Peripheral</h1>
<p>Dibawah ini adalah list-list barang Anda yang rusak. Anda bisa mengajukan perbaikan.</p>

<?php if (count($brokenProducts) > 0): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Product Name</th>
                <th>Product Photo</th>
                <th>Product Description</th>
                <th>Serial Number</th>
                <th>No Inventaris</th>
                <th>Note</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $counter = 1;
        foreach ($brokenProducts as $product): ?>
            <tr>
                <td><?php echo $counter++ ?></td>
                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                <td>
                    <?php if ($product['product_photo']): ?>
                        <img src="upload_image/uploads_products/<?php echo htmlspecialchars($product['product_photo']); ?>" alt="Product Photo" class="product-photo" style="width: 150px;">
                    <?php else: ?>
                        No Photo
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($product['product_description'] ?: 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($product['no_serial']); ?></td>
                <td><?php echo htmlspecialchars($product['no_inventaris']); ?></td>
                <td><?php echo htmlspecialchars($product['note_record']); ?></td>
                <td><?php echo ucfirst($product['record_status']); ?></td>
                <td>
                    <?php if ($product['record_status'] === 'broken'): ?>
                        <form method="POST" style="display: inline-block;">
                            <input type="hidden" name="record_id" value="<?php echo htmlspecialchars($product['id_records']); ?>">
                            <button type="submit" name="apply_for_repair" class="btn btn-add" onclick="return confirmAction('Apply for repair?') && promptForNote()">Apply for Repair</button>
                        </form>
                        <br>
                        <form method="POST" style="display: inline-block;">
                            <input type="hidden" name="record_id" value="<?php echo htmlspecialchars($product['id_records']); ?>">
                            <button type="submit" name="delete_product" class="btn btn-delete" onclick="return confirmAction('Cancel this product?')">Delete</button>
                        </form>
                    <?php elseif ($product['record_status'] === 'decline'): ?>
                        <button class="btn btn-secondary" disabled>Declined</button>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>Pending</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No broken products found.</p>
<?php endif; ?>


                
</div>

<!-- Bootstrap 5 JavaScript and Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0v8FqO4rx0z6FMqU5tq1VqW58RclPb1Hlmh0fJkhDi1ozh4c" crossorigin="anonymous"></script>
<script>
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('closed');
        content.classList.toggle('expanded');

        if (sidebar.classList.contains('closed')) {
            toggleBtn.style.left = '20px';
        } else {
            toggleBtn.style.left = '250px';
        }
    });
</script>
<script>
    function confirmAction(message) {
        return confirm(message);
    }

    function promptForNote() {
    const note = prompt('Please enter a note for the repair request:');

    if (note) {
        const form = event.target.closest('form'); // Get the closest form
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'note';
        hiddenInput.value = note;
        form.appendChild(hiddenInput); // Append the note input to the form
        return true;
    } else {
        return false;
    }
}


</script>
</body>
</html>



