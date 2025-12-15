<?php

namespace App\Middleware;

class GuestMiddleware
{
    /**
     * Handle guest check - redirect if already logged in
     */
    public function handle(array $params = []): bool
    {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // If user is already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            // Load helpers for redirect function
            require_once __DIR__ . '/../helpers.php';
            redirect('/home');
        }

        return true;
    }
}
