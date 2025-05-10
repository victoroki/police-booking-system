<?php
$page_title = 'Manage Officers';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

// Only allow admins
redirectIfNotAdmin();

$db = new Database();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_officer'])) {
        // Add new officer logic
        $name = htmlspecialchars($_POST['full_name'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);

        if (empty($name) || empty($email) || empty($_POST['password'])) {
            $error_message = "All fields are required!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Invalid email format!";
        } else {
            try {
                $db->query(
                    "INSERT INTO users (full_name, email, password, role_id) 
                     SELECT ?, ?, ?, id FROM roles WHERE name = 'officer'",
                    [$name, $email, $password]
                );
                $_SESSION['success_message'] = "Officer added successfully!";
                header("Refresh:0"); 
                exit;
            } catch (Exception $e) {
                $error_message = "Error adding officer: " . $e->getMessage();
            }
        }

        $db->query(
            "INSERT INTO users (full_name, email, password, role_id) 
             SELECT ?, ?, ?, id FROM roles WHERE name = 'officer'",
            [$name, $email, $password]
        );
    } elseif (isset($_POST['delete_officer'])) {
        // Delete officer logic
        $officer_id = (int)$_POST['officer_id'];
        $db->query(
            "DELETE FROM users WHERE id = ? AND role_id = (SELECT id FROM roles WHERE name = 'officer')",
            [$officer_id]
        );
    }
}

// Get all officers
$officers = $db->query(
    "SELECT u.id, u.full_name, u.email FROM users u 
     JOIN roles r ON u.role_id = r.id 
     WHERE r.name = 'officer'"
) ?: [];
?>
    <?php include __DIR__ . '/navbar.php'; ?>
    <?php include __DIR__ . '/sidebar.php'; ?>

<div class="main-content">
<a  href="/views/auth/logout.php"
                               onclick="return confirm('Are you sure you want to logout?')">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a><
    <div class="container-fluid p-4">
        <h2 class="mb-4"><i class="fas fa-user-shield me-2"></i> Manage Officers</h2>
        
        <!-- Add Officer Form -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Add New Officer</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="full_name" required>
                        </div>
                        <div class="col-md-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="add_officer" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i> Add Officer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Officers List -->
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Officers List</h5>
                <span class="badge bg-primary"><?= count($officers) ?> officers</span>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($officers)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($officers as $officer): ?>
                                    <tr>
                                        <td>#<?= $officer['id'] ?></td>
                                        <td><?= htmlspecialchars($officer['full_name']) ?></td>
                                        <td><?= htmlspecialchars($officer['email']) ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="officer_id" value="<?= $officer['id'] ?>">
                                                <button type="submit" name="delete_officer" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Are you sure you want to delete this officer?')">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-user-shield fa-4x text-muted mb-3"></i>
                        <h4>No Officers Found</h4>
                        <p class="text-muted">You haven't added any officers yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/foooter.php'; ?>