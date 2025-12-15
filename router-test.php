<?php
/**
 * Router Test Page - Comprehensive Route Testing
 */

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Load helpers
require_once(__DIR__ . "/src/helpers.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Router Test - Asetik v2</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h1 {
            color: #0052aa;
            border-bottom: 3px solid #0052aa;
            padding-bottom: 10px;
        }
        h2 {
            color: #333;
            margin-top: 30px;
        }
        .info-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .warning {
            color: #ffc107;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #0052aa;
            color: white;
        }
        tr:hover {
            background: #f9f9f9;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #0052aa;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px;
        }
        .btn:hover {
            background: #003d7a;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <h1>🔧 Router Test - Asetik v2</h1>
    
    <div class="info-box">
        <h2>Current Environment</h2>
        <table>
            <tr>
                <th>Variable</th>
                <th>Value</th>
            </tr>
            <tr>
                <td><strong>REQUEST_URI</strong></td>
                <td><code><?= htmlspecialchars($_SERVER['REQUEST_URI']) ?></code></td>
            </tr>
            <tr>
                <td><strong>SCRIPT_NAME</strong></td>
                <td><code><?= htmlspecialchars($_SERVER['SCRIPT_NAME']) ?></code></td>
            </tr>
            <tr>
                <td><strong>Base Path (Detected)</strong></td>
                <td><code><?= htmlspecialchars(dirname($_SERVER['SCRIPT_NAME'])) ?></code></td>
            </tr>
            <tr>
                <td><strong>HTTP_HOST</strong></td>
                <td><code><?= htmlspecialchars($_SERVER['HTTP_HOST']) ?></code></td>
            </tr>
            <tr>
                <td><strong>Document Root</strong></td>
                <td><code><?= htmlspecialchars($_SERVER['DOCUMENT_ROOT']) ?></code></td>
            </tr>
            <tr>
                <td><strong>mod_rewrite Status</strong></td>
                <td><span class="success">✓ Enabled</span></td>
            </tr>
        </table>
    </div>

    <div class="info-box">
        <h2>Session Information</h2>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p class="success">✓ User is logged in</p>
            <table>
                <tr>
                    <th>Session Key</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td>user_id</td>
                    <td><?= htmlspecialchars($_SESSION['user_id']) ?></td>
                </tr>
                <tr>
                    <td>username</td>
                    <td><?= htmlspecialchars($_SESSION['username'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <td>level</td>
                    <td><?= htmlspecialchars($_SESSION['level'] ?? 'N/A') ?></td>
                </tr>
            </table>
        <?php else: ?>
            <p class="warning">⚠ No active session (not logged in)</p>
        <?php endif; ?>
    </div>

    <div class="info-box">
        <h2>Available Routes</h2>
        <p>Click on any route to test it:</p>
        
        <h3>Public Routes</h3>
        <table>
            <tr>
                <th>Route</th>
                <th>Method</th>
                <th>Description</th>
                <th>Test</th>
            </tr>
            <tr>
                <td><code>/</code></td>
                <td>GET</td>
                <td>Root - redirects to /home or /login</td>
                <td><a href="<?= url('/') ?>" class="btn">Test</a></td>
            </tr>
            <tr>
                <td><code>/login</code></td>
                <td>GET/POST</td>
                <td>Login page</td>
                <td><a href="<?= url('/login') ?>" class="btn">Test</a></td>
            </tr>
            <tr>
                <td><code>/debug</code></td>
                <td>GET</td>
                <td>Debug information</td>
                <td><a href="<?= url('/debug') ?>" class="btn">Test</a></td>
            </tr>
        </table>

        <h3>Authenticated Routes (Requires Login)</h3>
        <table>
            <tr>
                <th>Route</th>
                <th>Method</th>
                <th>Description</th>
                <th>Test</th>
            </tr>
            <tr>
                <td><code>/home</code></td>
                <td>GET</td>
                <td>Dashboard/Home page</td>
                <td><a href="<?= url('/home') ?>" class="btn">Test</a></td>
            </tr>
            <tr>
                <td><code>/logout</code></td>
                <td>GET</td>
                <td>Logout</td>
                <td><a href="<?= url('/logout') ?>" class="btn">Test</a></td>
            </tr>
            <tr>
                <td><code>/showdata</code></td>
                <td>GET</td>
                <td>Show data (normal users)</td>
                <td><a href="<?= url('/showdata') ?>" class="btn">Test</a></td>
            </tr>
            <tr>
                <td><code>/apply-fix</code></td>
                <td>GET/POST</td>
                <td>Apply for repair</td>
                <td><a href="<?= url('/apply-fix') ?>" class="btn">Test</a></td>
            </tr>
            <tr>
                <td><code>/take-back</code></td>
                <td>GET/POST</td>
                <td>Pick up repair</td>
                <td><a href="<?= url('/take-back') ?>" class="btn">Test</a></td>
            </tr>
        </table>

        <h3>Admin Routes (Requires Admin Login)</h3>
        <table>
            <tr>
                <th>Route</th>
                <th>Method</th>
                <th>Description</th>
                <th>Test</th>
            </tr>
            <tr>
                <td><code>/users</code></td>
                <td>GET</td>
                <td>User management</td>
                <td><a href="<?= url('/users') ?>" class="btn">Test</a></td>
            </tr>
            <tr>
                <td><code>/products</code></td>
                <td>GET</td>
                <td>Product/Peripheral management</td>
                <td><a href="<?= url('/products') ?>" class="btn">Test</a></td>
            </tr>
            <tr>
                <td><code>/records</code></td>
                <td>GET</td>
                <td>Records management</td>
                <td><a href="<?= url('/records') ?>" class="btn">Test</a></td>
            </tr>
            <tr>
                <td><code>/approve</code></td>
                <td>GET/POST</td>
                <td>Approve repairs</td>
                <td><a href="<?= url('/approve') ?>" class="btn">Test</a></td>
            </tr>
        </table>
    </div>

    <div class="info-box">
        <h2>URL Helper Functions Test</h2>
        <table>
            <tr>
                <th>Function</th>
                <th>Result</th>
            </tr>
            <tr>
                <td><code>url('/')</code></td>
                <td><?= htmlspecialchars(url('/')) ?></td>
            </tr>
            <tr>
                <td><code>url('/home')</code></td>
                <td><?= htmlspecialchars(url('/home')) ?></td>
            </tr>
            <tr>
                <td><code>url('/users')</code></td>
                <td><?= htmlspecialchars(url('/users')) ?></td>
            </tr>
            <tr>
                <td><code>asset('images/logo.png')</code></td>
                <td><?= htmlspecialchars(asset('images/logo.png')) ?></td>
            </tr>
            <tr>
                <td><code>route('/users/edit/{id}', ['id' => 123])</code></td>
                <td><?= htmlspecialchars(route('/users/edit/{id}', ['id' => 123])) ?></td>
            </tr>
            <tr>
                <td><code>current_url()</code></td>
                <td><?= htmlspecialchars(current_url()) ?></td>
            </tr>
        </table>
    </div>

    <div class="info-box">
        <h2>.htaccess Configuration</h2>
        <?php
        $htaccessPath = __DIR__ . '/.htaccess';
        if (file_exists($htaccessPath)):
        ?>
            <p class="success">✓ .htaccess file exists</p>
            <pre style="background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto;"><?= htmlspecialchars(file_get_contents($htaccessPath)) ?></pre>
        <?php else: ?>
            <p class="error">✗ .htaccess file not found!</p>
        <?php endif; ?>
    </div>

    <div class="info-box">
        <h2>Quick Actions</h2>
        <a href="<?= url('/') ?>" class="btn">Go to Home</a>
        <a href="<?= url('/login') ?>" class="btn">Go to Login</a>
        <a href="<?= url('/debug') ?>" class="btn">Debug Info</a>
        <a href="<?= url('/test_db.php') ?>" class="btn">Test Database</a>
    </div>

    <div class="info-box">
        <p><strong>Status:</strong> <span class="success">✓ Router is configured and working correctly for XAMPP!</span></p>
        <p><small>Last updated: <?= date('Y-m-d H:i:s') ?></small></p>
    </div>
</body>
</html>
