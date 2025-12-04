<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is not logged in and destroy the session
if (!isset($_SESSION['user_id'])) {
    session_destroy();
    // Redirect to login page (optional)
    header('Location: login/login.php');
    exit();  // Make sure to stop the script after redirection
}
// Database connection
require_once("dbConnection.php");
// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $repairId = $_POST['repair_id'];
    $recordId = $_POST['record_id'];
    $action = $_POST['action'];
    $note = $_POST['note'] ?? ''; // Retrieve the note from the form input


    $repairQuery = "UPDATE records SET note_record = :note WHERE id_records = :recordId";
    
    try {
        $stmt = $pdo->prepare($repairQuery);
        $stmt->execute(['note' => $note, 'recordId' => $recordId]);

        if ($action == 'accept') {
            $pdo->prepare("UPDATE records SET status = 'fixing' WHERE id_records = :id")->execute(['id' => $recordId]);
        } elseif ($action == 'next') {
            $pdo->prepare("UPDATE records SET status = 'not taken' WHERE id_records = :id")->execute(['id' => $recordId]);
            $pdo->prepare("DELETE FROM repair WHERE id_repair = :id")->execute(['id' => $repairId]);
        } elseif ($action == 'decline') {
            $pdo->prepare("UPDATE records SET status = 'decline' WHERE id_records = :id")->execute(['id' => $recordId]);
            $pdo->prepare("DELETE FROM repair WHERE id_repair = :id")->execute(['id' => $repairId]);
        }

        echo "<script>alert('Repair request submitted successfully.');</script>";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Failed to submit repair request: " . $e->getMessage() . "');</script>";
    }
    
}

// Fetch repair records
$sql = "SELECT repair.id_repair, repair.id_record, users.name AS user_name, products.name AS product_name, repair.note, records.status, records.no_inventaris, records.note_record
        FROM repair
        JOIN users ON repair.id_user = users.id
        JOIN records ON repair.id_record = records.id_records
        JOIN products ON records.id_products = products.id";
$rows = $pdo->query($sql)->fetchAll();
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
        body {
            font-family: 'Oswald', sans-serif;

        }
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
        <a class="navbar-brand" href="index.php">
            <img src="logo/logo.png" alt="Logo" style="width: 150px;">
        </a>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="index.php">Home</a>
            </li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['level'] == 'admin'): ?>
                    <!-- Admin-specific menu items -->
                    <li class="nav-item">
                        <a class="nav-link" href="new_crud_admin/index.php">User</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="crud_products/index.php">Peripheral</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="records/index.php">Records</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="approve.php">Approve Repair</a>
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
                        <a class="nav-link" href="showdata.php">Show Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="apply_fix.php">Apply for Repair</a>
                    </li>
                <?php endif; ?>
                <!-- Common Logout link for all logged-in users -->
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            <?php else: ?>
                <!-- Login link for non-logged-in users -->
                <li class="nav-item">
                    <a class="nav-link" href="login/login.php">Login</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<!-- Content Section -->
<div class="content" id="content">
        <h1>Persetujuan Perbaikan.</h1>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>User Name</th>
                    <th>Product Name</th>
                    <th>No Inventaris</th>
                    <th>Catatan</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($rows) > 0): ?>
                    <?php 
                        $counter = 1;
                        foreach ($rows as $row): ?>
                        <tr>
                        <td><?php echo $counter++; ?></td>
                        <td><?= htmlspecialchars($row['user_name']) ?></td>
                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                            <td><?= htmlspecialchars($row['no_inventaris']) ?></td>
                            <td><?= htmlspecialchars($row['note_record']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td>
    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to accept this repair?');">
        <input type="hidden" name="repair_id" value="<?= $row['id_repair'] ?>">
        <input type="hidden" name="record_id" value="<?= $row['id_record'] ?>">
        <?php if ($row['status'] == 'pending'): ?>
            <button type="submit" name="action" value="accept" class="btn btn-add">Accept</button>
        <?php else: ?>
            <button type="submit" name="action" value="accept" class="btn btn-primary" disabled>Accept</button>
        <?php endif; ?>
    </form>
    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to mark this repair as complete?') && promptForNote();">
        <input type="hidden" name="repair_id" value="<?= $row['id_repair'] ?>">
        <input type="hidden" name="record_id" value="<?= $row['id_record'] ?>">
        <?php if ($row['status'] == 'fixing'): ?>
            <button type="submit" name="action" value="next" class="btn btn-edit">Complete</button>
        <?php else: ?>
            <button type="submit" name="action" value="next" class="btn btn-primary" disabled>Complete</button>
        <?php endif; ?>
    </form>
    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to decline this repair?') && promptForNote();">
        <input type="hidden" name="repair_id" value="<?= $row['id_repair'] ?>">
        <input type="hidden" name="record_id" value="<?= $row['id_record'] ?>">
        <?php if ($row['status'] == 'pending'): ?>
            <button type="submit" name="action" value="decline" class='btn btn-delete' >Decline</button>
        <?php else: ?>
            <button type="submit" name="action" value="decline" class="btn btn-primary" disabled>Decline</button>
        <?php endif; ?>
    </form>
</td>

                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No repair records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

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




    function confirmAction(message) {
        return confirm(message);
    }

    function promptForNote() {
    const note = prompt('Please enter a note for the customer:');

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