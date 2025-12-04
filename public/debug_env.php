<?php
// Debug environment variables
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Environment Debug</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #0f0; }
        h2 { color: #0ff; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #333; padding: 10px; text-align: left; }
        th { background: #333; color: #0ff; }
        .found { color: #0f0; }
        .missing { color: #f00; }
    </style>
</head>
<body>
    <h1>🔍 Environment Variables Debug</h1>
    
    <h2>MySQL Variables (Railway)</h2>
    <table>
        <tr><th>Variable</th><th>Value</th><th>Status</th></tr>
        <?php
        $mysqlVars = ['MYSQLHOST', 'MYSQLPORT', 'MYSQLDATABASE', 'MYSQLUSER', 'MYSQLPASSWORD'];
        foreach ($mysqlVars as $var) {
            $value = $_SERVER[$var] ?? getenv($var) ?? null;
            $status = $value ? '<span class="found">✓ Found</span>' : '<span class="missing">✗ Missing</span>';
            $display = $value ? (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) : 'NOT SET';
            echo "<tr><td>$var</td><td>$display</td><td>$status</td></tr>";
        }
        ?>
    </table>

    <h2>DB Variables (Local)</h2>
    <table>
        <tr><th>Variable</th><th>Value</th><th>Status</th></tr>
        <?php
        $dbVars = ['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASSWORD'];
        foreach ($dbVars as $var) {
            $value = $_SERVER[$var] ?? getenv($var) ?? null;
            $status = $value ? '<span class="found">✓ Found</span>' : '<span class="missing">✗ Missing</span>';
            $display = $value ? (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) : 'NOT SET';
            echo "<tr><td>$var</td><td>$display</td><td>$status</td></tr>";
        }
        ?>
    </table>

    <h2>What dbConnection.php Will Use</h2>
    <?php
    $host = $_SERVER['MYSQLHOST'] ?? getenv('MYSQLHOST') ?? $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost';
    $db   = $_SERVER['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?? $_SERVER['DB_NAME'] ?? getenv('DB_NAME') ?? 'asetik_v2';
    $user = $_SERVER['MYSQLUSER'] ?? getenv('MYSQLUSER') ?? $_SERVER['DB_USER'] ?? getenv('DB_USER') ?? 'root';
    $port = $_SERVER['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? $_SERVER['DB_PORT'] ?? getenv('DB_PORT') ?? '3306';
    $pass = $_SERVER['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD') ?? $_SERVER['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? '';
    ?>
    <table>
        <tr><th>Config</th><th>Value</th></tr>
        <tr><td>Host</td><td><?php echo htmlspecialchars($host); ?></td></tr>
        <tr><td>Port</td><td><?php echo htmlspecialchars($port); ?></td></tr>
        <tr><td>Database</td><td><?php echo htmlspecialchars($db); ?></td></tr>
        <tr><td>User</td><td><?php echo htmlspecialchars($user); ?></td></tr>
        <tr><td>Password</td><td><?php echo $pass ? '***SET***' : 'NOT SET'; ?></td></tr>
    </table>

    <h2>DSN String</h2>
    <pre><?php echo "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4"; ?></pre>

    <h2>All Environment Variables</h2>
    <details>
        <summary>Click to expand (<?php echo count($_SERVER); ?> variables)</summary>
        <table>
            <tr><th>Key</th><th>Value</th></tr>
            <?php
            ksort($_SERVER);
            foreach ($_SERVER as $key => $value) {
                if (is_string($value)) {
                    $display = strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value;
                    echo "<tr><td>" . htmlspecialchars($key) . "</td><td>" . htmlspecialchars($display) . "</td></tr>";
                }
            }
            ?>
        </table>
    </details>
</body>
</html>
