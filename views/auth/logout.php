<?php
require_once __DIR__ . '/../../includes/auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Call the logout function
logout();

// The logout function already contains redirect,
// but we'll add an extra safety measure
header("Location: /views/auth/login.php");
exit;
?>