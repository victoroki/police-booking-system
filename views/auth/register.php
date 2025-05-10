<?php
require_once '../../includes/auth.php';
require_once '../../config/database.php';

// Redirect if already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: ../dashboard.php");
    exit;
}

// Initialize error/success messages
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... existing validation code

    if (empty($errors)) {
        $db = new Database();
        $conn = $db->getConnection();
            // Insert new user
            $stmt = $conn->prepare(
                "INSERT INTO users (full_name, email, password, role) 
                 VALUES (?, ?, ?, ?)"
            );

            $password = $_POST['password'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $email = $_POST['email'];
            $full_name = $firstname . " " . $lastname;
            $role = "customer";

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param('ssss', $full_name, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $success = true;
                $_SESSION['registration_success'] = true;
                header("Location: login.php");
                exit;
            } else {
                $errors[] = "Registration failed: " . $conn->error;
            }
        }
    }
?>

<?php include '../../includes/header.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0"><i class="fas fa-user-plus me-2"></i>Create Your Account</h3>
                    </div>

                    <div class="card-body">
                        <?php if(!empty($errors['general'])): ?>
                            <div class="alert alert-danger"><?= $errors['general']; ?></div>
                        <?php endif; ?>

                        <?php if($success): ?>
                            <div class="alert alert-success"><?= $success; ?></div>
                        <?php else: ?>

                            <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="firstname" class="form-label">First Name *</label>
                                        <input type="text" class="form-control <?= isset($errors['firstname']) ? 'is-invalid' : '' ?>"
                                               id="firstname" name="firstname" value="<?= htmlspecialchars($firstname ?? ''); ?>" required>
                                        <?php if(isset($errors['firstname'])): ?>
                                            <div class="invalid-feedback"><?= $errors['firstname']; ?></div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="lastname" class="form-label">Last Name *</label>
                                        <input type="text" class="form-control <?= isset($errors['lastname']) ? 'is-invalid' : '' ?>"
                                               id="lastname" name="lastname" value="<?= htmlspecialchars($lastname ?? ''); ?>" required>
                                        <?php if(isset($errors['lastname'])): ?>
                                            <div class="invalid-feedback"><?= $errors['lastname']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                           id="email" name="email" value="<?= htmlspecialchars($email ?? ''); ?>" required
                                           placeholder="example@domain.com">
                                    <?php if(isset($errors['email'])): ?>
                                        <div class="invalid-feedback"><?= $errors['email']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                               id="password" name="password" required
                                               placeholder="At least 8 characters">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <?php if(isset($errors['password'])): ?>
                                        <div class="invalid-feedback"><?= $errors['password']; ?></div>
                                    <?php endif; ?>
                                    <small class="text-muted">Minimum 8 characters</small>
                                </div>

                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">Confirm Password *</label>
                                    <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                                           id="confirm_password" name="confirm_password" required
                                           placeholder="Re-enter your password">
                                    <?php if(isset($errors['confirm_password'])): ?>
                                        <div class="invalid-feedback"><?= $errors['confirm_password']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-user-plus me-2"></i>Register
                                    </button>
                                </div>
                            </form>

                            <div class="mt-4 text-center">
                                <p>Already have an account? <a href="login.php">Login here</a></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password visibility toggle
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const passwordInput = this.parentNode.querySelector('input');
                const icon = this.querySelector('i');

                if(passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            });
        });
    </script>

<?php include '../../includes/footer.php'; ?>