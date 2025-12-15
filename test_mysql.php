<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySQL Connection Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }
        h3 {
            color: #555;
            margin-top: 20px;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        .warning {
            color: #ffc107;
            font-weight: bold;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            padding: 8px;
            margin: 5px 0;
            background: #f8f9fa;
            border-left: 4px solid #007bff;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #28a745;
            overflow-x: auto;
        }
        .info-box {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔌 MySQL Connection Test for XAMPP</h2>
        
        <?php
        // Load .env
        $host = 'localhost';
        $db   = 'asetik';
        $user = 'root';
        $pass = '';
        $port = '3306';

        if (file_exists(__DIR__ . '/../.env')) {
            $envFile = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($envFile as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                if (strpos($line, '=') === false) continue;
                
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                if ($key === 'DB_HOST') $host = $value;
                if ($key === 'DB_NAME') $db = $value;
                if ($key === 'DB_USER') $user = $value;
                if ($key === 'DB_PASSWORD') $pass = $value;
                if ($key === 'DB_PORT') $port = $value;
            }
        }

        echo "<div class='info-box'>";
        echo "<p><strong>📋 Configuration from .env file:</strong></p>";
        echo "<ul>";
        echo "<li><strong>Host:</strong> $host</li>";
        echo "<li><strong>Port:</strong> $port</li>";
        echo "<li><strong>Database:</strong> $db</li>";
        echo "<li><strong>User:</strong> $user</li>";
        echo "<li><strong>Password:</strong> " . (empty($pass) ? '(empty)' : '(set)') . "</li>";
        echo "</ul>";
        echo "</div>";

        // Test 1: Connect without database
        echo "<h3>Test 1: Connect to MySQL Server</h3>";
        try {
            $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            echo "<p class='success'>✅ Successfully connected to MySQL server!</p>";
            
            // Check if database exists
            echo "<h3>Test 2: Check if database '$db' exists</h3>";
            $stmt = $pdo->query("SHOW DATABASES LIKE '$db'");
            $result = $stmt->fetch();
            
            if ($result) {
                echo "<p class='success'>✅ Database '$db' exists!</p>";
                
                // Test 3: Connect to the database
                echo "<h3>Test 3: Connect to database '$db'</h3>";
                $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
                $pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
                echo "<p class='success'>✅ Successfully connected to database '$db'!</p>";
                
                // Show tables
                echo "<h3>Test 4: List tables in database</h3>";
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                if (count($tables) > 0) {
                    echo "<p class='success'>✅ Found " . count($tables) . " tables:</p>";
                    echo "<ul>";
                    foreach ($tables as $table) {
                        // Count rows in each table
                        $countStmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
                        $count = $countStmt->fetch();
                        echo "<li><strong>$table</strong> (" . $count['count'] . " rows)</li>";
                    }
                    echo "</ul>";
                    
                    echo "<div style='margin-top: 30px; padding: 20px; background: #d4edda; border-radius: 5px;'>";
                    echo "<h3 style='color: #155724; margin-top: 0;'>🎉 All Tests Passed!</h3>";
                    echo "<p style='color: #155724;'>Your MySQL connection is working perfectly. You can now access your application at:</p>";
                    echo "<pre style='background: white; border-left-color: #28a745;'><a href='http://localhost/asetik_v2/public/modules/auth/login.php' style='color: #007bff; text-decoration: none;'>http://localhost/asetik_v2/public/modules/auth/login.php</a></pre>";
                    echo "<p style='color: #155724;'><strong>Default admin credentials:</strong></p>";
                    echo "<ul style='color: #155724;'>";
                    echo "<li>Username: <strong>admin</strong></li>";
                    echo "<li>Password: <strong>(check your database)</strong></li>";
                    echo "</ul>";
                    echo "</div>";
                    
                } else {
                    echo "<p class='warning'>⚠️ Database is empty (no tables found)</p>";
                    echo "<p>Please import the database schema:</p>";
                    echo "<pre>Run: import_database.bat</pre>";
                }
                
            } else {
                echo "<p class='error'>❌ Database '$db' does NOT exist!</p>";
                echo "<p><strong>Solution:</strong> Create the database by running:</p>";
                echo "<pre>CREATE DATABASE $db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;</pre>";
                echo "<p>You can do this in phpMyAdmin or MySQL command line.</p>";
            }
            
        } catch (PDOException $e) {
            echo "<p class='error'>❌ Connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin-top: 15px;'>";
            echo "<p><strong>🔧 Common solutions:</strong></p>";
            echo "<ul>";
            echo "<li>Make sure XAMPP MySQL is running (check XAMPP Control Panel)</li>";
            echo "<li>Check that port $port is not blocked or used by another service</li>";
            echo "<li>Verify username and password are correct</li>";
            echo "<li>Try restarting MySQL from XAMPP Control Panel</li>";
            echo "</ul>";
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>
