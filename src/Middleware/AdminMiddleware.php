<?php

namespace App\Middleware;

class AdminMiddleware
{
    /**
     * Handle admin authorization check
     */
    public function handle(array $params = []): bool
    {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /modules/login.php');
            exit();
        }

        // Check if user is admin
        if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
            http_response_code(403);
            echo "403 - Forbidden: Admin access required";
            exit();
        }

        return true;
    }
}
