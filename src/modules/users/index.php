<?php
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
                /* Custom Styles for Tables */
.table th, .table td {
    text-align: center;
    vertical-align: middle;
}

/* User Details */
h1, h2 {
    color: #333;
}

/* Product Description Styling */
td img {
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Button Styling */
button.btn {
    margin-top: 5px;
    padding: 10px 15px;
}

/* No Photo Styling */
.no-photo {
    color: #888;
    font-style: italic;
    text-align: center;
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
        <a class="navbar-brand" href="../index.php">
            <img src="../../../public/assets/images/logo.png" alt="Logo" style="width: 150px;">
        </a>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="../index.php">Home</a>
            </li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['level'] == 'admin'): ?>
                    <!-- Admin-specific menu items -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">User</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../crud_products/index.php">Peripheral</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../records/index.php">Records</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="../approve.php">Approve Repair</a>
                    </li>

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
                    <a class="nav-link" href="../auth/logout.php">Logout</a>
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


<br>

<a href="add_user.php" class="btn btn-add" style="margin-bottom: 10px;">Add New User</a>


<?php
require_once(__DIR__ . "/../../config/dbConnection.php");

$users = $pdo->query("SELECT * FROM users")->fetchAll();
?>

<table border="1">
    <tr>
        <th>No</th>
        <th>Name</th>
        <th>Age</th>
        <th>Email</th>
        <th>Divisi</th>
        <th>Description</th>
        <th>Photo</th>
        <th>Username</th>
        <th>Badge</th>
        <th>Level</th>
        <th>Actions</th>
    </tr>
    <?php 
    $counter = 1;
    foreach ($users as $user): ?>
        <tr>
            <td><?php echo $counter++; ?></td>
            <td><?= $user['name'] ?></td>
            <td><?= $user['age'] ?></td>
            <td><?= $user['email'] ?></td>
            <td><?= $user['divisi'] ?></td>
            <td><?= $user['description'] ?></td>
            <td>
                <?= ($user['photo'] ? "<img src='../../../public/uploads/" . $user['photo'] . "' alt='Photo' width='100'>" : 'No Photo') ?>
            </td>
            <td><?= $user['username'] ?></td>
            <td><?= $user['badge'] ?></td>
            <td><?= $user['level'] ?></td>
            <td>
                <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-edit">Edit</a> 
                <a href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Are you sure?')" class="btn btn-delete">Delete</a> 
            </td>

        </tr>
    <?php endforeach; ?>
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
</script>
</body>
</html>
