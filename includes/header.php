<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include authentication functions
require_once __DIR__ . '/auth.php';


// Check if user is logged in for protected pages
$public_pages = ['login.php', 'register.php', 'unauthorized.php'];
$current_page = basename($_SERVER['PHP_SELF']);

if (!in_array($current_page, $public_pages)) {
    checkLoggedIn();
    
    // Role-based redirection
// Role-based redirection
    $customer_pages = [ 'dashboard.php','new.php', 'list.php', 'view.php'];
    $admin_pages = ['officers.php', 'dashboard.php', 'bookings.php', 'reports.php', 'profile.php'];
    $officer_pages = ['assignments.php', 'officer_dashboard.php'];

    if (in_array($current_page, $customer_pages)) {
        redirectIfNotCustomer();
    } elseif (in_array($current_page, $admin_pages)) {
        redirectIfNotAdmin();
    } elseif (in_array($current_page, $officer_pages)) {
        redirectIfNotOfficer();
    }
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Police Booking System - <?php echo $page_title ?? 'Dashboard'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        
        :root {
    --primary-color: #2a4e6c;
    --secondary-color: #3a6688;
    --accent-color: #e74c3c;
    --light-color: #f8f9fa;
    --dark-color: #212529;
    --sidebar-width: 250px;
    --navbar-height: 56px;
}

/* Reset default body margin */
body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f5f7fa;
}

/* Fixed navbar at the very top */
.navbar-custom {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: var(--navbar-height);
    background: white !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    z-index: 1030;
    display: flex;
    align-items: center;
}

/* Sidebar positioned right below navbar */
.sidebar-wrapper {
    position: fixed;
    top: var(--navbar-height);
    left: 0;
    bottom: 0;
    width: var(--sidebar-width);
    background: var(--primary-color);
    z-index: 1020;
    transition: transform 0.3s ease;
    overflow-y: auto;
}

/* Main content area */
.main-content {
    margin-left: var(--sidebar-width);
    margin-top: var(--navbar-height);
    padding: 20px;
    min-height: calc(100vh - var(--navbar-height));
    transition: margin-left 0.3s ease;
}

/* Remove any default margin from html */
html {
    margin: 0;
    padding: 0;
}

/* Sidebar styling */
.sidebar {
    height: 100%;
    color: white;
}

.sidebar-brand {
    padding: 1rem;
    background: rgba(0,0,0,0.1);
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.sidebar-nav {
    padding: 0;
    list-style: none;
}

.sidebar-nav .nav-link {
    padding: 0.75rem 1.5rem;
    color: rgba(255,255,255,0.8);
    display: flex;
    align-items: center;
    border-left: 4px solid transparent;
    transition: all 0.3s;
}

.sidebar-nav .nav-link:hover,
.sidebar-nav .nav-link.active {
    color: white;
    background: rgba(255,255,255,0.1);
    border-left: 4px solid var(--accent-color);
}

/* Toggle states */
.sidebar-collapsed .sidebar-wrapper {
    transform: translateX(-100%);
}

.sidebar-collapsed .main-content {
    margin-left: 0;
}

/* Responsive behavior */
@media (max-width: 768px) {
    .sidebar-wrapper {
        transform: translateX(-100%);
    }
    
    .sidebar-expanded .sidebar-wrapper {
        transform: translateX(0);
    }
}
    </style>
</head>
<body>