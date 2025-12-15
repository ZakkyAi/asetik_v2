<?php
/**
 * Comprehensive Route Testing Page
 * Tests all routes, helpers, and functionality
 */

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Load helpers
require_once __DIR__ . '/src/helpers.php';

// Test helper functions
$helperTests = [];

try {
    $helperTests['url()'] = url('/test');
    $helperTests['url() status'] = '✅ Working';
} catch (Exception $e) {
    $helperTests['url() status'] = '❌ Error: ' . $e->getMessage();
}

try {
    $helperTests['route()'] = route('/products/edit/{id}', ['id' => 5]);
    $helperTests['route() status'] = '✅ Working';
} catch (Exception $e) {
    $helperTests['route() status'] = '❌ Error: ' . $e->getMessage();
}

try {
    $helperTests['asset()'] = asset('images/logo.png');
    $helperTests['asset() status'] = '✅ Working';
} catch (Exception $e) {
    $helperTests['asset() status'] = '❌ Error: ' . $e->getMessage();
}

try {
    $helperTests['current_url()'] = current_url();
    $helperTests['current_url() status'] = '✅ Working';
} catch (Exception $e) {
    $helperTests['current_url() status'] = '❌ Error: ' . $e->getMessage();
}

try {
    $helperTests['is_route()'] = is_route('/test-all-routes.php') ? 'true' : 'false';
    $helperTests['is_route() status'] = '✅ Working';
} catch (Exception $e) {
    $helperTests['is_route() status'] = '❌ Error: ' . $e->getMessage();
}

// Define all routes to test
$routesToTest = [
    'Public Routes' => [
        ['GET', '/', 'Home (redirects)'],
        ['GET', '/router-test', 'Router Test Page'],
        ['GET', '/debug', 'Debug Info'],
    ],
    'Guest Routes (Not Logged In)' => [
        ['GET', '/login', 'Login Page'],
        ['POST', '/login', 'Login Form Submit'],
    ],
    'Authenticated Routes (Logged In)' => [
        ['GET', '/home', 'Dashboard'],
        ['GET', '/dashboard', 'Dashboard (alias)'],
        ['GET', '/logout', 'Logout'],
        ['GET', '/showdata', 'Show Data'],
        ['GET', '/apply-fix', 'Apply for Repair'],
        ['POST', '/apply-fix', 'Submit Repair Request'],
        ['GET', '/take-back', 'Pick Up Repair'],
        ['POST', '/take-back', 'Submit Pick Up'],
    ],
    'Admin Routes (Admin Only)' => [
        ['GET', '/users', 'User List'],
        ['GET', '/users/add', 'Add User'],
        ['POST', '/users/add', 'Create User'],
        ['GET', '/users/edit/1', 'Edit User'],
        ['POST', '/users/edit/1', 'Update User'],
        ['GET', '/users/delete/1', 'Delete User'],
        
        ['GET', '/products', 'Product List'],
        ['GET', '/peripherals', 'Product List (alias)'],
        ['GET', '/products/add', 'Add Product'],
        ['POST', '/products/add', 'Create Product'],
        ['GET', '/products/edit/1', 'Edit Product'],
        ['POST', '/products/edit/1', 'Update Product'],
        ['GET', '/products/delete/1', 'Delete Product'],
        
        ['GET', '/records', 'Records List'],
        ['GET', '/records/add', 'Add Record'],
        ['POST', '/records/add', 'Create Record'],
        ['GET', '/records/edit/1', 'Edit Record'],
        ['POST', '/records/edit/1', 'Update Record'],
        ['GET', '/records/delete/1', 'Delete Record'],
        
        ['GET', '/approve', 'Approve Repairs'],
        ['POST', '/approve', 'Submit Approval'],
    ],
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Route Testing - Asetik v2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background: #f5f5f5;
        }
        .test-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .route-item {
            padding: 8px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }
        .route-item:hover {
            background: #f8f9fa;
        }
        .method-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            margin-right: 10px;
            border-radius: 4px;
            font-weight: bold;
            min-width: 50px;
            text-align: center;
        }
        .method-get {
            background: #28a745;
            color: white;
        }
        .method-post {
            background: #007bff;
            color: white;
        }
        .test-link {
            color: #0066cc;
            text-decoration: none;
            flex: 1;
        }
        .test-link:hover {
            text-decoration: underline;
        }
        .status-icon {
            margin-left: 10px;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-bottom: 20px;
        }
        .success-box {
            background: #e8f5e9;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            margin-bottom: 20px;
        }
        .warning-box {
            background: #fff3e0;
            border-left: 4px solid #FF9800;
            padding: 15px;
            margin-bottom: 20px;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">🧪 Comprehensive Route Testing</h1>
        
        <!-- Session Status -->
        <div class="test-section">
            <h3>Session Status</h3>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="success-box">
                    <strong>✅ Logged In</strong><br>
                    User ID: <?= $_SESSION['user_id'] ?><br>
                    Username: <?= $_SESSION['username'] ?? 'N/A' ?><br>
                    Level: <?= $_SESSION['level'] ?? 'N/A' ?>
                </div>
            <?php else: ?>
                <div class="warning-box">
                    <strong>⚠️ Not Logged In</strong><br>
                    Some routes will redirect you to login.
                </div>
            <?php endif; ?>
        </div>

        <!-- Helper Functions Test -->
        <div class="test-section">
            <h3>Helper Functions Test</h3>
            <table class="table table-sm">
                <tr>
                    <th>Function</th>
                    <th>Result</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td><code>url('/test')</code></td>
                    <td><code><?= htmlspecialchars($helperTests['url()']) ?></code></td>
                    <td><?= $helperTests['url() status'] ?></td>
                </tr>
                <tr>
                    <td><code>route('/products/edit/{id}', ['id' => 5])</code></td>
                    <td><code><?= htmlspecialchars($helperTests['route()']) ?></code></td>
                    <td><?= $helperTests['route() status'] ?></td>
                </tr>
                <tr>
                    <td><code>asset('images/logo.png')</code></td>
                    <td><code><?= htmlspecialchars($helperTests['asset()']) ?></code></td>
                    <td><?= $helperTests['asset() status'] ?></td>
                </tr>
                <tr>
                    <td><code>current_url()</code></td>
                    <td><code><?= htmlspecialchars($helperTests['current_url()']) ?></code></td>
                    <td><?= $helperTests['current_url() status'] ?></td>
                </tr>
                <tr>
                    <td><code>is_route('/test-all-routes.php')</code></td>
                    <td><code><?= htmlspecialchars($helperTests['is_route()']) ?></code></td>
                    <td><?= $helperTests['is_route() status'] ?></td>
                </tr>
            </table>
        </div>

        <!-- Routes Test -->
        <?php foreach ($routesToTest as $category => $routes): ?>
        <div class="test-section">
            <h3><?= $category ?></h3>
            <div class="info-box">
                <strong>ℹ️ Testing Instructions:</strong><br>
                Click on any route to test it. GET routes will navigate directly, POST routes are listed for reference.
            </div>
            
            <?php foreach ($routes as $route): ?>
                <?php 
                    [$method, $path, $description] = $route;
                    $fullUrl = url($path);
                    $methodClass = strtolower($method) === 'get' ? 'method-get' : 'method-post';
                ?>
                <div class="route-item">
                    <span class="method-badge <?= $methodClass ?>"><?= $method ?></span>
                    <?php if ($method === 'GET'): ?>
                        <a href="<?= $fullUrl ?>" class="test-link" target="_blank">
                            <strong><?= $path ?></strong> - <?= $description ?>
                        </a>
                        <span class="status-icon">🔗</span>
                    <?php else: ?>
                        <span class="test-link">
                            <strong><?= $path ?></strong> - <?= $description ?>
                        </span>
                        <span class="status-icon">📝</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>

        <!-- Quick Actions -->
        <div class="test-section">
            <h3>Quick Actions</h3>
            <div class="btn-group" role="group">
                <a href="<?= url('/') ?>" class="btn btn-primary">Home</a>
                <a href="<?= url('/login') ?>" class="btn btn-success">Login</a>
                <a href="<?= url('/router-test') ?>" class="btn btn-info">Router Test</a>
                <a href="<?= url('/debug') ?>" class="btn btn-warning">Debug</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= url('/logout') ?>" class="btn btn-danger">Logout</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- File Check -->
        <div class="test-section">
            <h3>File Existence Check</h3>
            <table class="table table-sm">
                <tr>
                    <th>File</th>
                    <th>Status</th>
                </tr>
                <?php
                $filesToCheck = [
                    'src/Router/Router.php',
                    'src/routes.php',
                    'src/helpers.php',
                    'src/Middleware/AuthMiddleware.php',
                    'src/Middleware/AdminMiddleware.php',
                    'src/Middleware/GuestMiddleware.php',
                    'public/index.php',
                    'public/modules/login.php',
                    'public/modules/logout.php',
                    'public/modules/products/index.php',
                    'public/modules/users/index.php',
                    'public/modules/records/index.php',
                ];
                
                foreach ($filesToCheck as $file):
                    $exists = file_exists(__DIR__ . '/' . $file);
                ?>
                <tr>
                    <td><code><?= $file ?></code></td>
                    <td><?= $exists ? '✅ Exists' : '❌ Missing' ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- Documentation Links -->
        <div class="test-section">
            <h3>Documentation</h3>
            <ul>
                <li><a href="START_HERE.md" target="_blank">📖 Start Here</a></li>
                <li><a href="ROUTER_QUICK_START.md" target="_blank">⚡ Quick Start Guide</a></li>
                <li><a href="ROUTER_DOCUMENTATION.md" target="_blank">📚 Full Documentation</a></li>
                <li><a href="ROUTER_FIXES_SUMMARY.md" target="_blank">🔧 Fixes Summary</a></li>
                <li><a href="ROUTER_TESTING_CHECKLIST.md" target="_blank">✅ Testing Checklist</a></li>
            </ul>
        </div>

        <!-- System Info -->
        <div class="test-section">
            <h3>System Information</h3>
            <table class="table table-sm">
                <tr>
                    <th>Item</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td>PHP Version</td>
                    <td><?= phpversion() ?></td>
                </tr>
                <tr>
                    <td>Server Software</td>
                    <td><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></td>
                </tr>
                <tr>
                    <td>Document Root</td>
                    <td><code><?= $_SERVER['DOCUMENT_ROOT'] ?></code></td>
                </tr>
                <tr>
                    <td>Script Name</td>
                    <td><code><?= $_SERVER['SCRIPT_NAME'] ?></code></td>
                </tr>
                <tr>
                    <td>Request URI</td>
                    <td><code><?= $_SERVER['REQUEST_URI'] ?></code></td>
                </tr>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
