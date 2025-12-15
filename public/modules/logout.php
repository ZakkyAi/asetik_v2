<?php
// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . "/../../src/helpers.php");

session_unset(); // Remove all session variables
session_destroy(); // Destroy the session

redirect('/login'); // Redirect to login page
