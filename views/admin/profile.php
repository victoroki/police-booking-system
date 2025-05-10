<?php

session_start();

define('ROOT_PATH', dirname(__DIR__, 2));

require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/config/database.php';


if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /login.php");
    exit;
}


$db = new Database();
$admin_id = $_SESSION['user_id'];

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception("Invalid form submission");
        }

        $full_name = htmlspecialchars($_POST['full_name']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        $db->query(
            "UPDATE users SET full_name = ?, email = ? WHERE id = ?",
            [$full_name, $email, $admin_id]
        );

        if (!empty($_POST['current_password'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            $user = $db->query("SELECT password FROM users WHERE id = ?", [$admin_id])[0];
            
            if (!password_verify($current_password, $user['password'])) {
                throw new Exception("Current password is incorrect");
            }

            if ($new_password !== $confirm_password) {
                throw new Exception("New passwords do not match");
            }

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $db->query("UPDATE users SET password = ? WHERE id = ?", [$hashed_password, $admin_id]);
        }

        $_SESSION['success_message'] = "Profile updated successfully";
        header("Location: profile.php");
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}


try {
    $admin = $db->query(
        "SELECT full_name, email, created_at 
         FROM users WHERE id = ?",
        [$admin_id]
    )[0];
} catch (Exception $e) {
    die("Error loading profile: " . $e->getMessage());
}


$page_title = "Admin Profile";
require_once ROOT_PATH . '/includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
</head>
<div class="container-fluid">
    <div class="row">
        <?php include __DIR__ . '/navbar.php'; ?>
        <?php include __DIR__ . '/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Admin Profile</h1>
            </div>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['success_message'] ?>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="full_name" 
                                           value="<?= htmlspecialchars($admin['full_name'] ?? '') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" 
                                           value="<?= htmlspecialchars($admin['email'] ?? '') ?>" required>
                                </div>

                                <div class="card mt-4 border-primary">
                                    <div class="card-header bg-primary text-white">
                                        Change Password
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Current Password</label>
                                            <input type="password" class="form-control" name="current_password">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">New Password</label>
                                            <input type="password" class="form-control" name="new_password">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" name="confirm_password">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary mt-3">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-body text-center">
                            <div class="mb-4">
                                <img src="/assets/images/admin-avatar.png" 
                                     class="rounded-circle" 
                                     width="150" 
                                     alt="Admin Avatar">
                            </div>
                            <h4><?= htmlspecialchars($admin['full_name'] ?? 'Admin') ?></h4>
                            <p class="text-muted mb-1"><?= htmlspecialchars($admin['email'] ?? '') ?></p>
                            <p class="text-muted small">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Member since <?= date('M Y', strtotime($admin['created_at'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>