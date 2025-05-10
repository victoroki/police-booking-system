<?php
$page_title = 'Unauthorized Access';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 text-center">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="fas fa-ban fa-5x text-danger"></i>
                    </div>
                    <h1 class="h3 mb-3">Unauthorized Access</h1>
                    <p class="lead mb-4">
                        You don't have permission to access this page.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="/views/auth/login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i> Login Page
                        </a>
                        <a href="/" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i> Home Page
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<style>
    body {
        background-color: #f8f9fa;
    }
    .card {
        border: none;
        border-radius: 10px;
    }
</style>