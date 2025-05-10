<?php

session_start();

require_once __DIR__ . '/../../includes/auth.php';



if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: /login.php");
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$db = new Database();
$customer_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception("Invalid form submission");
        }

        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];


        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            throw new Exception("All password fields are required");
        }

        if ($new_password !== $confirm_password) {
            throw new Exception("New passwords do not match");
        }

        $result = $db->query(
            "SELECT password FROM users WHERE id = ?",
            [$customer_id]
        );
        
        if (empty($result)) {
            throw new Exception("Customer not found");
        }

        if (!password_verify($current_password, $result[0]['password'])) {
            throw new Exception("Current password is incorrect");
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $db->query(
            "UPDATE users SET password = ? WHERE id = ?",
            [$hashed_password, $customer_id]
        );

        $success = "Password updated successfully";

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}


try {
    $customer = $db->query(
        "SELECT full_name, email, created_at FROM users WHERE id = ?",
        [$customer_id]
    )[0];
} catch (Exception $e) {
    die("Error loading profile: " . $e->getMessage());
}

$page_title = "Customer Profile"; 



?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Police Booking System') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    

    <link rel="stylesheet" href="../../assets/css/sidebar.css">
</head>


<body>
    <?php include __DIR__ . '\navbar.php'; ?>
    <?php include __DIR__ . '\sidebar.php'; ?>

    <main class="main-content">
        <div class="container-fluid p-4">
            <h2 class="mb-4">My Profile</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" 
                                       value="<?= htmlspecialchars($customer['full_name']) ?>" disabled>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" 
                                       value="<?= htmlspecialchars($customer['email']) ?>" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                                <h5 class="mb-4">Change Password</h5>
                                
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
                                    <i class="fas fa-lock me-2"></i> Update Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>