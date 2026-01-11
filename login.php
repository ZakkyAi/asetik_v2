<?php
// Direct login access - bypasses redirects
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . "/src/config/dbConnection.php");

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password_user = $_POST['password_user'] ?? '';
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    
    // Get reCAPTCHA keys from environment
    $recaptcha_secret = getenv('RECAPTCHA_SECRET_KEY');

    // Verify reCAPTCHA
    if (empty($recaptcha_response)) {
        $error = "Please complete the reCAPTCHA verification!";
    } else {
        // Verify with Google
        $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
        $response = file_get_contents($verify_url . '?' . http_build_query([
            'secret' => $recaptcha_secret,
            'response' => $recaptcha_response,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ]));
        $response_data = json_decode($response);

        if (!$response_data->success) {
            $error = "reCAPTCHA verification failed. Please try again.";
        } elseif (!empty($username) && !empty($password_user)) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->execute(['username' => $username]);
                $user = $stmt->fetch();

                if ($user && isset($user['password_user']) && password_verify($password_user, $user['password_user'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['level'] = $user['level'];
                    
                    $success = "Login successful! Redirecting...";
                    header("refresh:2;url=/asetik_v2/public/index.php");
                } else {
                    $error = "Invalid username or password!";
                }
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        } else {
            $error = "Please fill in all fields!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Asetik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="flex h-screen bg-gray-100">
    <div class="w-full lg:w-1/2 flex flex-col justify-center items-center bg-white relative">
        <div class="absolute top-0 w-full h-4 bg-blue-500"></div>
        <h2 class="text-2xl font-bold mb-8 mt-4">Login to Asetik</h2>
        
        <?php if ($error): ?>
            <div class="w-1/2 mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="w-1/2 mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form action="" method="POST" class="w-1/2">
            <div class="mb-4 flex items-center border border-gray-300 rounded px-3 py-2">
                <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5.121 17.804A9.973 9.973 0 0112 16a9.973 9.973 0 016.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0zM12 2a10 10 0 100 20 10 10 0 000-20z">
                    </path>
                </svg>
                <input type="text" name="username" placeholder="Username" required
                    class="w-full border-none focus:outline-none">
            </div>
            <div class="mb-6 flex items-center border border-gray-300 rounded px-3 py-2 relative">
                <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 11c2.21 0 4 1.79 4 4s-1.79 4-4 4-4-1.79-4-4 1.79-4 4-4zm0 0c-2.21 0-4-1.79-4-4S9.79 3 12 3s4 1.79 4 4-1.79 4-4 4z">
                    </path>
                </svg>
                <input type="password" id="password" name="password_user" placeholder="Password" required
                    class="w-full border-none focus:outline-none">
                <button type="button" onclick="togglePassword()" class="absolute right-3 focus:outline-none">
                    <svg id="eye-icon" class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.942 5 12 5c4.059 0 8.269 2.943 9.542 7-1.273 4.057-5.483 7-9.542 7-4.058 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                </button>
            </div>
            
            <!-- reCAPTCHA widget -->
            <div class="mb-4 flex justify-center">
                <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars(getenv('RECAPTCHA_SITE_KEY')); ?>"></div>
            </div>
            
            <button type="submit" class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">
                Login
            </button>
        </form>
        
        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded w-1/2">
            <p class="text-sm text-blue-800"><strong>Default Admin Credentials:</strong></p>
            <p class="text-sm text-blue-600">Username: <strong>admin</strong></p>
            <p class="text-sm text-blue-600">Password: <em>(check database or try common passwords)</em></p>
        </div>
    </div>
    <div class="hidden lg:flex w-1/2 flex-col justify-center items-center bg-blue-600 relative">
        <img src="public/assets/images/logo.png" alt="Logo" class="w-1/2 mb-8" onerror="this.style.display='none'">
        <h1 class="text-white text-4xl font-bold">Asetik System</h1>
        <p class="text-white text-lg mt-4">Asset Management System</p>
    </div>
    <script>
        function togglePassword() {
            const passwordField = document.getElementById("password");
            const eyeIcon = document.getElementById("eye-icon");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                eyeIcon.classList.add("text-blue-500");
            } else {
                passwordField.type = "password";
                eyeIcon.classList.remove("text-blue-500");
            }
        }
    </script>
</body>
</html>
