<?php
// edit_user.php

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
require_once(__DIR__ . "/../../../src/config/dbConnection.php");

// Check if ID is set (from route parameter)
if (!isset($id)) {
    header('Location: ' . url('/users'));
    exit();
}

$id = intval($id);

// Fetch user data
// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit();
}

// Update user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $divisi = $_POST['divisi'];

    $description = $_POST['description'];
    $username = $_POST['username'];
    $badge = $_POST['badge'];
    $level = $_POST['level'];

    // Handle photo upload
    // Handle photo upload
    if (!empty($_FILES['photo']['name'])) {
        $photo = $_FILES['photo']['name'];
        $target = "../../uploads/" . basename($photo);
    
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            $query = "UPDATE users SET name=:name, age=:age, email=:email, description=:description, username=:username, badge=:badge, level=:level, divisi=:divisi, photo=:photo WHERE id=:id";
            $params = [
                'name' => $name, 'age' => $age, 'email' => $email, 'description' => $description, 
                'username' => $username, 'badge' => $badge, 'level' => $level, 'divisi' => $divisi, 
                'photo' => $photo, 'id' => $id
            ];
        } else {
            echo "Failed to upload photo.";
            exit;
        }
    } else {
        $query = "UPDATE users SET name=:name, age=:age, email=:email, description=:description, username=:username, badge=:badge, level=:level, divisi=:divisi WHERE id=:id";
        $params = [
            'name' => $name, 'age' => $age, 'email' => $email, 'description' => $description, 
            'username' => $username, 'badge' => $badge, 'level' => $level, 'divisi' => $divisi, 
            'id' => $id
        ];
    }
    
    try {
        $pdo->prepare($query)->execute($params);
        header("Location: " . url('/users'));
    } catch (PDOException $e) {
        echo "Error updating record: " . $e->getMessage();
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
        <?php require_once(__DIR__ . "/../../../src/helpers.php"); ?>
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

<h1>Edit User</h1>
<br>

<div style="max-width: 800px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <form action="" method="POST" enctype="multipart/form-data">
        
        <div style="margin-bottom: 20px;">
            <label for="name" style="display: block; font-weight: bold; margin-bottom: 5px;">Name:</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required 
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="age" style="display: block; font-weight: bold; margin-bottom: 5px;">Age:</label>
            <input type="number" name="age" id="age" value="<?= htmlspecialchars($user['age']) ?>" 
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="email" style="display: block; font-weight: bold; margin-bottom: 5px;">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required 
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="divisi" style="display: block; font-weight: bold; margin-bottom: 5px;">Divisi:</label>
            <input type="text" name="divisi" id="divisi" value="<?= htmlspecialchars($user['divisi']) ?>" 
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="description" style="display: block; font-weight: bold; margin-bottom: 5px;">Description:</label>
            <textarea name="description" id="description" 
                      style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px; font-size: 14px; height: 100px; box-sizing: border-box; resize: vertical;"><?= htmlspecialchars($user['description']) ?></textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="username" style="display: block; font-weight: bold; margin-bottom: 5px;">Username:</label>
            <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required 
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="badge" style="display: block; font-weight: bold; margin-bottom: 5px;">Badge:</label>
            <input type="text" name="badge" id="badge" value="<?= htmlspecialchars($user['badge']) ?>" 
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="level" style="display: block; font-weight: bold; margin-bottom: 5px;">Level:</label>
            <select name="level" id="level" required 
                    style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                <option value="admin" <?= $user['level'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="normal_user" <?= $user['level'] === 'normal_user' ? 'selected' : '' ?>>Normal User</option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Current Photo:</label>
            <div style="padding: 10px; background: #f8f9fa; border: 2px solid #ddd; border-radius: 4px;">
                <?= ($user['photo'] ? "<img src='" . url('public/uploads/' . $user['photo']) . "' alt='Photo' style='max-width: 150px; border-radius: 4px;'>" : '<span style="color: #888;">No Photo</span>') ?>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="photo" style="display: block; font-weight: bold; margin-bottom: 5px;">Upload New Photo:</label>
            <input type="file" name="photo" id="photo" 
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-edit" style="margin-right: 10px;">Update User</button>
            <a href="<?= url('/users') ?>" class="btn btn-delete">Back</a>
        </div>

    </form>
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
