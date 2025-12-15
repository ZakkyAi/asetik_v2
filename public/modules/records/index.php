<?php
// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Load helpers
require_once(__DIR__ . "/../../../src/helpers.php");

// Check if user is not logged in and destroy the session
if (!isset($_SESSION['user_id'])) {
    session_destroy();
    redirect('/login');
}
require_once(__DIR__ . "/../../../src/config/dbConnection.php");

// Fetch records query
// Fetch records query
$records = $pdo->query("
    SELECT records.id_records, records.no_serial, records.no_inventaris, users.name AS user_name, users.age AS user_age, users.email AS user_email, records.record_time,
           users.description AS user_description, users.photo AS user_photo, users.divisi AS user_divisi,
           products.name AS product_name, products.photo AS product_photo, products.description AS product_description,
           records.status
    FROM records
    JOIN users ON records.id_users = users.id
    JOIN products ON records.id_products = products.id
")->fetchAll();

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
                        <a class="nav-link" href="<?= url('/products') ?>">Peripheral</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/records') ?>">Records</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/approve') ?>">Approve Repair</a>
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
                        <a class="nav-link" href="<?= url('/showdata') ?>">Show Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/apply-fix') ?>">Apply for Repair</a>
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

<br>
<a href="<?= url('/records/add') ?>" class="btn btn-add" style="margin-bottom: 10px;">Add New Records</a>

    <?php


    if (count($records) > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Serial Number</th>
                    <th>Nomor Inventaris</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Divisi</th> 
                    <th>Description</th>
                    <th>Photo</th>
                    <th>Product Name</th>
                    <th>Product Photo</th>
                    <th>Product Description</th>
                    <th>Record Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $counter = 1; // Initialize counter
                foreach ($records as $row): ?>
                    <tr>
                        <td><?php echo $counter++; ?></td> <!-- Increment counter -->
                        <td><?= $row['no_serial'] ?></td>
                        <td><?= $row['no_inventaris'] ?></td>
                        <td><?= $row['user_name'] ?></td>
                        <td><?= $row['user_email'] ?></td>
                        <td><?= $row['user_divisi'] ?></td> 
                        <td><?= $row['user_description'] ?></td>
                        <td>
                            <?= $row['user_photo'] ? "<img src='" . url('public/uploads/' . $row['user_photo']) . "' alt='User Photo' width='100'>" : "No Photo" ?>
                        </td>
                        <td><?= $row['product_name'] ?></td>
                        <td>
                            <?= $row['product_photo'] ? "<img src='" . url('public/uploads/' . $row['product_photo']) . "' alt='Product Photo' width='100'>" : "No Photo" ?>
                        </td>
                        <td><?= $row['product_description'] ?></td>
                        <td><?= $row['record_time'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td>
                            <a href="<?= route('/records/edit/{id}', ['id' => $row['id_records']]) ?>" class="btn btn-edit">Edit</a>
                            <a href="<?= route('/records/delete/{id}', ['id' => $row['id_records']]) ?>" onclick="return confirm('Are you sure?')" class="btn btn-delete">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No records found.</p>
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
</body>
</html>
