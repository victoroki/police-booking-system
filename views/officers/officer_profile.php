<?php
// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'officer') {
    header("Location: /login.php");
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

$db = new Database();
$officer_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception("Invalid form submission");
        }

        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Validate inputs
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            throw new Exception("All password fields are required");
        }

        if ($new_password !== $confirm_password) {
            throw new Exception("New passwords do not match");
        }

        // Get current password hash
        $result = $db->query(
            "SELECT password FROM users WHERE id = ?",
            [$officer_id]
        );
        
        if (empty($result)) {
            throw new Exception("Officer not found");
        }

        // Verify current password
        if (!password_verify($current_password, $result[0]['password'])) {
            throw new Exception("Current password is incorrect");
        }

        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $db->query(
            "UPDATE users SET password = ? WHERE id = ?",
            [$hashed_password, $officer_id]
        );

        $success = "Password updated successfully";

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get officer info
try {
    $officer = $db->query(
        "SELECT full_name, email FROM users WHERE id = ?",
        [$officer_id]
    )[0];
} catch (Exception $e) {
    die("Error loading profile: " . $e->getMessage());
}

$page_title = "Officer Profile";
require_once __DIR__ . '/../../includes/header.php';
?>

<body>
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="main-content">
        <div class="container-fluid p-4">
            <h2 class="mb-4">Officer Profile</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" 
                                       value="<?= htmlspecialchars($officer['full_name']) ?>" disabled>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" 
                                       value="<?= htmlspecialchars($officer['email']) ?>" disabled>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" class="form-control" 
                                           name="current_password" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control" 
                                           name="new_password" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" 
                                           name="confirm_password" required>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-lock me-2"></i> Change Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Password visibility toggle
        document.querySelectorAll('.password-toggle').forEach(button => {
            button.addEventListener('click', (e) => {
                const input = document.querySelector(button.dataset.target);
                input.type = input.type === 'password' ? 'text' : 'password';
                button.querySelector('i').classList.toggle('fa-eye-slash');
            });
        });
    </script>
</body>
</html>