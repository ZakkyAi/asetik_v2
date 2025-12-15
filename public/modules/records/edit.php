<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    // Redirect to login page if not authorized
    header('Location: ../auth/login.php');
    exit();
}

// Include database connection file
require_once(__DIR__ . "/../../../src/config/dbConnection.php");
require_once(__DIR__ . "/../../../src/helpers.php");

// Initialize variables
$errorMessage = '';
$recordId = null;

// Check if 'id' is passed as route parameter
if (!isset($id)) {
    echo "No record ID specified.";
    exit();
}

$recordId = intval($id); // Sanitize input to prevent SQL injection

// Fetch the existing record details
$stmt = $pdo->prepare("SELECT no_serial, no_inventaris, status FROM records WHERE id_records = :id");
$stmt->execute(['id' => $recordId]);
$record = $stmt->fetch();

if ($record) {
    $noSerial = $record['no_serial'];
    $noInventaris = $record['no_inventaris'];
    $status = $record['status'];
} else {
    echo "Record not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noSerial = trim($_POST['no_serial']);
    $noInventaris = trim($_POST['no_inventaris']);
    $status = $_POST['status'];

    // Validate inputs
    if (empty($noSerial) || empty($noInventaris) || empty($status)) {
        $errorMessage = "All fields are required.";
    } else {
        // Update query
        $updateQuery = "UPDATE records SET no_serial = :no_serial, no_inventaris = :no_inventaris, status = :status WHERE id_records = :id";
        try {
            $stmt = $pdo->prepare($updateQuery);
            $stmt->execute([
                'no_serial' => $noSerial,
                'no_inventaris' => $noInventaris,
                'status' => $status,
                'id' => $recordId
            ]);
            header('Location: ' . url('/records'));
            exit();
        } catch (PDOException $e) {
            $errorMessage = "Failed to update the record: " . $e->getMessage();
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
        <a class="navbar-brand" href="../../index.php">
            <img src="../../assets/images/logo.png" alt="Logo" style="width: 150px;">
        </a>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="../../index.php">Home</a>
            </li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['level'] == 'admin'): ?>
                    <!-- Admin-specific menu items -->
                    <li class="nav-item">
                        <a class="nav-link" href="../users/index.php">User</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../products/index.php">Peripheral</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Records</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Approve Repair</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#">Peripheral Distribution</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Monthly Repair</a>
                    </li> -->
                <?php elseif ($_SESSION['level'] == 'normal_user'): ?>
                    <!-- Normal user-specific menu item -->
                    <li class="nav-item">
                        <a class="nav-link" href="../../showdata.php">Show Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../apply_fix.php">Apply for Repair</a>
                    </li>
                <?php endif; ?>
                <!-- Common Logout link for all logged-in users -->
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            <?php else: ?>
                <!-- Login link for non-logged-in users -->
                <li class="nav-item">
                    <a class="nav-link" href="../auth/login.php">Login</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<!-- Content Section -->
<div class="content" id="content">

<h1>Edit Record</h1>
<br>

<?php if (!empty($errorMessage)): ?>
    <div style="padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 20px;">
        <?= $errorMessage ?>
    </div>
<?php endif; ?>

<div style="max-width: 800px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <form method="POST">
        
        <div style="margin-bottom: 20px;">
            <label for="no_serial" style="display: block; font-weight: bold; margin-bottom: 5px;">Serial Number:</label>
            <input type="text" id="no_serial" name="no_serial" value="<?= htmlspecialchars($noSerial) ?>" required 
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="no_inventaris" style="display: block; font-weight: bold; margin-bottom: 5px;">Nomor Inventaris:</label>
            <input type="text" id="no_inventaris" name="no_inventaris" value="<?= htmlspecialchars($noInventaris) ?>" required 
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="status" style="display: block; font-weight: bold; margin-bottom: 5px;">Status:</label>
            <select id="status" name="status" required 
                    style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                <option value="good" <?= $status === 'good' ? 'selected' : '' ?>>Good</option>
                <option value="broken" <?= $status === 'broken' ? 'selected' : '' ?>>Broken</option>
                <option value="not taken" <?= $status === 'not taken' ? 'selected' : '' ?>>Not Taken</option>
                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="fixing" <?= $status === 'fixing' ? 'selected' : '' ?>>Fixing</option>
                <option value="decline" <?= $status === 'decline' ? 'selected' : '' ?>>Decline</option>
            </select>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-edit" style="margin-right: 10px;">Update Record</button>
            <a href="<?= url('/records') ?>" class="btn btn-delete">Back</a>
        </div>

    </form>
</div>

</div>
                
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
</body>
</html>

