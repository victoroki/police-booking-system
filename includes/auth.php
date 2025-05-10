<?php
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function isLoggedIn() {
    if (isset($_SESSION['user_id'])) {
        return true;
    }

    if (isset($_COOKIE['remember_token'])) {
        require_once __DIR__ . '/../config/database.php';
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT id, role, full_name FROM users WHERE remember_token = ? AND token_expiry > NOW()");
        $stmt->bind_param('s', $_COOKIE['remember_token']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['full_name'];
            return true;
        }
    }

    return false;
}

function checkLoggedIn() {
    if (!isLoggedIn()) {
        $_SESSION['error_message'] = 'Please login to continue';
        header('Location: /views/auth/login.php');
        exit;
    }
}

function isCustomer() {
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'customer';
}

function isAdmin() {
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'admin';
}

function isOfficer() {
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'officer';
}

function redirectIfNotCustomer() {
    if (!isCustomer()) {
        $_SESSION['error_message'] = 'Customer access required';
        header('Location: /views/auth/unauthorized.php');
        exit;
    }
}

function redirectIfNotAdmin() {
    if (!isAdmin()) {
        $_SESSION['error_message'] = 'Admin access required';
        header('Location: /views/auth/unauthorized.php');
        exit;
    }
}

function redirectIfNotOfficer() {
    if (!isOfficer()) {
        $_SESSION['error_message'] = 'Officer access required';
        header('Location: /views/auth/unauthorized.php');
        exit;
    }
}

function logout() {
    // Unset all session variables
    $_SESSION = array();

    // Delete session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), 
            '', 
            time() - 42000,
            $params["path"], 
            $params["domain"],
            $params["secure"], 
            $params["httponly"]
        );
    }

    // Delete remember me cookie
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
        
        // Also clear from database if exists
        require_once __DIR__ . '/../config/database.php';
        $db = new Database();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("UPDATE users SET remember_token = NULL, token_expiry = NULL WHERE remember_token = ?");
        $stmt->bind_param('s', $_COOKIE['remember_token']);
        $stmt->execute();
    }

    // Destroy the session
    session_destroy();

    // Redirect to login page
    header("Location: /views/auth/login.php");
    exit;
}

function requireRole($role) {
    checkLoggedIn();
    
    $allowed = false;
    switch ($role) {
        case 'admin':
            $allowed = isAdmin();
            break;
        case 'officer':
            $allowed = isOfficer();
            break;
        case 'customer':
            $allowed = isCustomer();
            break;
    }
    
    if (!$allowed) {
        $_SESSION['error_message'] = ucfirst($role) . ' access required';
        header('Location: /views/auth/unauthorized.php');
        exit;
    }
}