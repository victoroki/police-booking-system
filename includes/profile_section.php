<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user data is available
if (!isset($user) || !is_array($user)) {
    die("User data not available");
}
?>

<div class="profile-container">
    <!-- Profile Header -->
    <div class="card shadow mb-4">
        <div class="card-body text-center">
            <div class="mb-3">
                <img src="<?= htmlspecialchars($user['avatar'] ?? '/assets/images/default-avatar.png') ?>" 
                     class="rounded-circle" 
                     width="150" 
                     alt="Profile Picture">
            </div>
            <h4><?= htmlspecialchars($user['full_name']) ?></h4>
            <p class="text-muted mb-1"><?= htmlspecialchars($user['email']) ?></p>
            <?php if (isset($user['created_at'])): ?>
            <p class="text-muted small">
                Member since <?= date('M Y', strtotime($user['created_at'])) ?>
            </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Profile Form -->
    <div class="card shadow">
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                <div class="form-group mb-3">
                    <label>Full Name</label>
                    <input type="text" class="form-control" name="full_name" 
                           value="<?= htmlspecialchars($user['full_name']) ?>" required>
                </div>

                <div class="form-group mb-3">
                    <label>Email Address</label>
                    <input type="email" class="form-control" name="email" 
                           value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="border-top pt-3">
                    <h5>Change Password</h5>
                    
                    <div class="form-group mb-3">
                        <label>Current Password</label>
                        <input type="password" class="form-control" name="current_password">
                    </div>

                    <div class="form-group mb-3">
                        <label>New Password</label>
                        <input type="password" class="form-control" name="new_password">
                    </div>

                    <div class="form-group mb-3">
                        <label>Confirm New Password</label>
                        <input type="password" class="form-control" name="confirm_password">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    field.type = field.type === 'password' ? 'text' : 'password';
}
</script>