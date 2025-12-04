<?php
/**
 * Database Auto-Setup Script
 * Automatically creates tables and inserts sample data on first run
 */

require_once(__DIR__ . '/../src/config/dbConnection.php');

function setupDatabase($pdo) {
    try {
        // Check if tables already exist
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Database already set up!'];
        }

        // Start transaction
        $pdo->beginTransaction();

        // Create products table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `products` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(100) NOT NULL,
              `photo` varchar(255) NOT NULL,
              `description` text NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Create users table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `users` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(100) NOT NULL,
              `age` int(11) DEFAULT NULL,
              `divisi` varchar(100) NOT NULL,
              `email` varchar(100) NOT NULL,
              `description` text DEFAULT NULL,
              `photo` varchar(255) DEFAULT NULL,
              `password_user` varchar(255) NOT NULL,
              `username` varchar(100) NOT NULL,
              `badge` varchar(30) DEFAULT NULL,
              `level` enum('admin','normal_user') NOT NULL DEFAULT 'normal_user',
              PRIMARY KEY (`id`),
              UNIQUE KEY `email` (`email`),
              UNIQUE KEY `username` (`username`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Create records table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `records` (
              `id_records` int(11) NOT NULL AUTO_INCREMENT,
              `id_users` int(11) DEFAULT NULL,
              `id_products` int(11) DEFAULT NULL,
              `status` enum('good','broken','not taken','pending','fixing','decline') NOT NULL DEFAULT 'good',
              `no_serial` varchar(255) NOT NULL,
              `no_inventaris` varchar(255) NOT NULL,
              `note_record` text NOT NULL,
              `record_time` timestamp NOT NULL DEFAULT current_timestamp(),
              PRIMARY KEY (`id_records`),
              KEY `fk_records_users` (`id_users`),
              KEY `fk_records_products` (`id_products`),
              CONSTRAINT `fk_records_products` FOREIGN KEY (`id_products`) REFERENCES `products` (`id`) ON DELETE SET NULL,
              CONSTRAINT `fk_records_users` FOREIGN KEY (`id_users`) REFERENCES `users` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Create repair table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `repair` (
              `id_repair` int(11) NOT NULL AUTO_INCREMENT,
              `id_user` int(11) NOT NULL,
              `id_record` int(11) NOT NULL,
              `note` text NOT NULL,
              `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
              PRIMARY KEY (`id_repair`),
              KEY `fk_repair_users` (`id_user`),
              KEY `fk_repair_records` (`id_record`),
              CONSTRAINT `fk_repair_records` FOREIGN KEY (`id_record`) REFERENCES `records` (`id_records`) ON DELETE CASCADE,
              CONSTRAINT `fk_repair_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Insert sample products
        $pdo->exec("
            INSERT INTO `products` (`id`, `name`, `photo`, `description`) VALUES
            (4, 'Keyboard Logitech', 'Logitech-K120.png', 'Office Keyboard'),
            (5, 'FantechATOM63', 'FantechATOM63.png', 'Keyboard Gaming'),
            (6, 'FantechKatanaSVX9s', 'FantechKatanaSVX9s.jpg', 'Mouse Gaming'),
            (7, 'FantechVx6 Phantom ii', 'FantechVx6Phantomii.jpg', 'Mouse Gaming'),
            (8, 'Gamen Titan V', 'gamenTitanV.jpg', 'Keyboard Gaming'),
            (9, 'Logitech B100', 'LogitechB100.jpg', 'Office Mouse'),
            (10, 'Logitech B175', 'LogitechB175.jpg', 'Office Mouse Wireless'),
            (11, 'Redragon P035', 'RedragonP035.jpg', 'keyboard hand rest'),
            (12, 'Rexus MX5.2', 'RexusMX5,2.jpg', 'Gaming'),
            (13, 'Rexus Mx10RGB', 'RexusMx10RGB.jpg', 'Gaming Keyboard'),
            (15, 'Logitech Mouse B100', 'LogitechB100.jpg', 'Office Mouse'),
            (16, 'Logitech B100', 'LogitechB100.jpg', 'Keyboard Office Wired')
        ");

        // Insert sample users (password: admin for all)
        $pdo->exec("
            INSERT INTO `users` (`id`, `name`, `age`, `divisi`, `email`, `description`, `photo`, `password_user`, `username`, `badge`, `level`) VALUES
            (18, 'admin', 20, 'Layanan TI', 'admin@gmail.com', 'Mr.Admin', 'profile.jpg', '\$2y\$10\$ZdWSNZm/a6aB4BWkbUavPOb1EWuens9ENzzAWBiEAv7KhJZLLCeH2', 'admin', '12-34', 'admin'),
            (20, 'Im0somn1s', 20, 'Layanan TI', 'Im0somn1s@gmail.com', 'Mahasiswa', 'man.jpg', '\$2y\$10\$EmSyorc5Rv6xQhYtdBKzgunx.46M0kUGNHCyYNQEOq.l7KC6a1oqK', 'Im0somn1s', '12-34', 'normal_user'),
            (21, 'Harjay', 22, 'Layanan TI', 'harjay@gmail.com', 'Mahasiswa', 'woman.jpg', '\$2y\$10\$NMR2ag2l2sbK3RToVKFlDunmD0LAYydT5aeiRIHHX0AlqbLWUKnVi', '123', '12-34', 'normal_user'),
            (22, 'Elda', 19, 'Layanan TI', 'elda@gmail.com', 'Mahasiswa', 'woman.jpg', '\$2y\$10\$6hw218NMYzZka8nnaqoLOuFXQNEqj8sFZZjtj5nuakWTq1SAXqWvq', 'elda', '12-34', 'normal_user')
        ");

        // Insert sample records
        $pdo->exec("
            INSERT INTO `records` (`id_records`, `id_users`, `id_products`, `status`, `no_serial`, `no_inventaris`, `note_record`, `record_time`) VALUES
            (24, 20, 9, 'good', '2348PUBG123', '555DAWQ2', '34', '2025-01-31 14:20:35'),
            (25, 20, 13, 'fixing', '2348FF', '343TES', '', '2025-01-29 20:04:58')
        ");

        // Commit transaction
        $pdo->commit();

        return ['success' => true, 'message' => 'Database setup completed successfully!'];

    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Setup failed: ' . $e->getMessage()];
    }
}

// Run setup
$result = setupDatabase($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Asetik v2</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        .icon {
            font-size: 64px;
            text-align: center;
            margin-bottom: 20px;
        }
        .success { color: #10b981; }
        .error { color: #ef4444; }
        h1 {
            text-align: center;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .message {
            background: #f3f4f6;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
            font-size: 18px;
        }
        .info {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info h3 {
            color: #1e40af;
            margin-bottom: 10px;
        }
        .credentials {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .tables {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin: 20px 0;
        }
        .table-item {
            background: #10b981;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($result['success']): ?>
            <div class="icon success">✅</div>
            <h1>Database Setup Complete!</h1>
            <div class="message">
                <?php echo htmlspecialchars($result['message']); ?>
            </div>

            <div class="info">
                <h3>📊 Tables Created:</h3>
                <div class="tables">
                    <div class="table-item">products</div>
                    <div class="table-item">users</div>
                    <div class="table-item">records</div>
                    <div class="table-item">repair</div>
                </div>
            </div>

            <div class="info">
                <h3>🔑 Default Login Credentials:</h3>
                <div class="credentials">
                    <strong>Admin Account:</strong><br>
                    Username: <code>admin</code><br>
                    Password: <code>admin</code>
                </div>
            </div>

            <div class="info">
                <h3>✨ What's Next?</h3>
                <p>Your database is ready! You can now:</p>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li>Login to your application</li>
                    <li>Manage products and users</li>
                    <li>Create records</li>
                    <li>Start using the system!</li>
                </ul>
            </div>

            <a href="index.php" class="btn">Go to Application →</a>

        <?php else: ?>
            <div class="icon error">❌</div>
            <h1>Setup Failed</h1>
            <div class="message">
                <?php echo htmlspecialchars($result['message']); ?>
            </div>
            <div class="info">
                <h3>🔧 Troubleshooting:</h3>
                <ul style="margin-left: 20px;">
                    <li>Check database connection settings in .env</li>
                    <li>Verify MySQL service is running</li>
                    <li>Check error logs for details</li>
                </ul>
            </div>
            <a href="setup.php" class="btn">Try Again</a>
        <?php endif; ?>
    </div>
</body>
</html>
