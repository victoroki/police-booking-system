<?php
session_start(); // Must be at the very top
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

// Display error message if exists
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']);
}

// Redirect if already logged in
if (isLoggedIn()) {
    $redirect = match($_SESSION['user_role']) { // Use session role instead of $user
        'admin' => '/views/admin/dashboard.php',
        'officer' => '/views/officers/officer_dashboard.php',
        'customer' => '/views/customers/dashboard.php',
        default => '/'
    };
    header("Location: $redirect");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid form submission";
    } else {
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $remember = isset($_POST['remember']);

        try {
            $db = new Database();
            $conn = $db->getConnection();

            // Get role from users table
            $stmt = $conn->prepare("SELECT id, password, role, full_name FROM users WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['logged_in'] = true;

                // Handle remember me
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expiry = date('Y-m-d H:i:s', time() + 30 * 24 * 60 * 60);

                    $updateStmt = $conn->prepare("UPDATE users SET remember_token = ?, token_expiry = ? WHERE id = ?");
                    $updateStmt->bind_param('ssi', $token, $expiry, $user['id']);
                    $updateStmt->execute();

                    setcookie('remember_token', $token, time() + 30 * 24 * 60 * 60, '/');
                }

                // Fixed redirect paths
                $redirect = match($user['role']) {
                    'admin' => '/views/admin/dashboard.php',
                    'officer' => '/views/officers/officer_dashboard.php',
                    'customer' => '/views/customers/dashboard.php',
                    default => '/login.php'
                };
                
                header("Location: $redirect");
                exit;
            } else {
                $error = "Invalid email or password";
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $error = "A system error occurred. Please try again later.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Police Booking System - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-floating {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="login-container">
        <h2 class="text-center mb-4">Police Booking System</h2>
        <h4 class="text-center mb-4">Customer Login</h4>

        <?php if (isset($_SESSION['registration_success'])): ?>
            <div class="alert alert-success">
                Registration successful! Please login.
            </div>
            <?php unset($_SESSION['registration_success']); ?>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

            <div class="form-floating">
                <input type="email" class="form-control" id="email" name="email"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                       placeholder="name@example.com" required>
                <label for="email">Email address</label>
            </div>

            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password"
                       placeholder="Password" required>
                <label for="password">Password</label>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>

            <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
        </form>

        <div class="mt-3 text-center">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
            <p><a href="forgot_password.php">Forgot password?</a></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>