<?php

namespace App\Middleware;

class AuthMiddleware
{
    /**
     * Handle authentication check
     */
    public function handle(array $params = []): bool
    {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            // Load helpers for redirect function
            require_once __DIR__ . '/../helpers.php';
            redirect('/login');
        }

        return true;
    }
}
