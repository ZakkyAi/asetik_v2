<?php
// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login/login.php');
    exit();
}

require_once("../dbConnection.php");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_users = $_POST['id_users'];
    $id_products = $_POST['id_products'];
    $status = $_POST['status'];
    $no_serial = $_POST['no_serial'];
    $no_inventaris = $_POST['no_inventaris'];

    // Insert query
    // Insert query
    $query = "INSERT INTO records (id_users, id_products, status, no_serial, no_inventaris) VALUES (:id_users, :id_products, :status, :no_serial, :no_inventaris)";
    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'id_users' => $id_users,
            'id_products' => $id_products,
            'status' => $status,
            'no_serial' => $no_serial,
            'no_inventaris' => $no_inventaris
        ]);
        // Redirect after successful insertion
        header('Location: index.php');
        exit();
    } catch (PDOException $e) {
        $error = "Error adding record: " . $e->getMessage();
    }
}

// Fetch users and products for display
$users = $pdo->query("SELECT id, name, photo FROM users")->fetchAll();
$products = $pdo->query("SELECT id, name, photo FROM products")->fetchAll();
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
        <a class="navbar-brand" href="../index.php">
            <img src="../logo/logo.png" alt="Logo" style="width: 150px;">
        </a>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="../index.php">Home</a>
            </li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['level'] == 'admin'): ?>
                    <!-- Admin-specific menu items -->
                    <li class="nav-item">
                        <a class="nav-link" href="../new_crud_admin/index.php">User</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../crud_products/index.php">Peripheral</a>
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
                        <a class="nav-link" href="showdata.php">Show Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="apply_fix.php">Apply for Repair</a>
                    </li>
                <?php endif; ?>
                <!-- Common Logout link for all logged-in users -->
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
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


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add New Record</title>
        <!-- Include Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container--default .select2-results__option img {
                width: 30px;
                height: 30px;
                margin-right: 10px;
                vertical-align: middle;
            }
            .select2-container {
        width: 40% !important; /* Ensures full width */
    }

    .select2-selection {
        min-width: 300px; /* Adjust this value to make the dropdown wider */
    }

        </style>
    </head>
    <body>
        <h1>Add New Record</h1>
        
        <?php if (isset($error)): ?>
            <p style="color: red;"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST" action="create.php">
            <label for="id_users">Select User:</label>
            <select name="id_users" id="id_users" required>
                <option value="">-- Select User --</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>" data-image="<?= $user['photo'] ?>">
                        <?= $user['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br><br>

            <label for="id_products">Select Product:</label>
            <select name="id_products" id="id_products" required>
                <option value="">-- Select Product --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['id'] ?>" data-image="<?= $product['photo'] ?>">
                        <?= $product['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br><br>

            <label for="status">Status:</label>
            <select name="status" id="status" required>
                <option value="good">Good</option>
                <option value="broken">Broken</option>
                <option value="not taken">Not Taken</option>
                <option value="pending">Pending</option>
                <option value="decline">Decline</option>
            </select>
            <br><br>

            <label for="no_serial">Serial Number:</label>
            <input type="text" name="no_serial" id="no_serial" required>
            <br><br>

            <label for="no_inventaris">Nomor Inventaris:</label>
            <input type="text" name="no_inventaris" id="no_inventaris" required>
            <br><br>

            <button type="submit">Add Record</button>
        </form>

        <!-- Include jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Include Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            // Initialize Select2 with image support
            $('#id_users').select2({
                templateResult: formatUserOption,
                templateSelection: formatUserOption,
                escapeMarkup: function (markup) { return markup; }
            });

            $('#id_products').select2({
                templateResult: formatProductOption,
                templateSelection: formatProductOption,
                escapeMarkup: function (markup) { return markup; }
            });

            function formatUserOption(option) {
                if (!option.id) {
                    return option.text;
                }
                var imgSrc = "../upload_image/uploads/" + $(option.element).data('image');
                var markup = '<img src="' + imgSrc + '" alt="User Photo" style="width: 30px; height: 30px;"> ' + option.text;
                return markup;
            }

            function formatProductOption(option) {
                if (!option.id) {
                    return option.text;
                }
                var imgSrc = "../upload_image/uploads_products/" + $(option.element).data('image');
                var markup = '<img src="' + imgSrc + '" alt="Product Photo" style="width: 30px; height: 30px;"> ' + option.text;
                return markup;
            }
        </script>
    </body>
    </html>
            <div style="padding-top: 10px;">
            <a href="index.php" class="btn btn-add" sytle="margin-top: 10px; margin-bottom: 10px ;">back</a>
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

