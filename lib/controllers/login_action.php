<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../config/database.php';

// Validate CSRF token
if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['login_error'] = "Invalid CSRF token";
    header("Location: login.php");
    exit;
}

// Sanitize inputs
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];
$remember = isset($_POST['remember']) ? true : false;

// Validate inputs
if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['login_error'] = "Please provide a valid email address";
    header("Location: login.php");
    exit;
}

if(empty($password)) {
    $_SESSION['login_error'] = "Please enter your password";
    header("Location: login.php");
    exit;
}

// Database connection
try {
    $db = new Database();
    $conn = $db->getConnection();

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, password, role, full_name FROM users WHERE email = ? AND status = 'active'");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($password, $user['password'])) {
        // Successful login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['full_name'];

        // Remember me functionality
        if($remember) {
            $token = bin2hex(random_bytes(32));
            $expiry = time() + 60 * 60 * 24 * 30; // 30 days

            setcookie('remember_token', $token, $expiry, '/');

            // Store token in database
            $stmt = $conn->prepare("UPDATE users SET remember_token = ?, token_expiry = ? WHERE id = ?");
            $stmt->execute([$token, date('Y-m-d H:i:s', $expiry), $user['id']]);
        }

        // Regenerate session ID for security
        session_regenerate_id(true);

        // Redirect to dashboard
        header("Location: ../dashboard.php");
        exit;
    } else {
        $_SESSION['login_error'] = "Invalid email or password";
        header("Location: login.php");
        exit;
    }
} catch(PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    $_SESSION['login_error'] = "A system error occurred. Please try again later.";
    header("Location: login.php");
    exit;
}
?>