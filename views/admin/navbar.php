<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container-fluid">
        <!-- Toggle Button -->
        <button class="btn btn-link me-3" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Navbar Brand (Optional) -->
        <a class="navbar-brand d-none d-md-block" href="/views/admin/admin_dashboard.php">
            Admin Panel
        </a>

        <!-- Right-side Navigation Items -->
        <div class="d-flex align-items-center ms-auto">
            <ul class="navbar-nav">
                <!-- Notifications -->
                <li class="nav-item me-3">
                    <a class="nav-link" href="#">
                        <i class="fas fa-bell"></i>
                    </a>
                </li>

                <!-- User Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" 
                       href="#" 
                       id="userDropdown" 
                       role="button" 
                       data-bs-toggle="dropdown" 
                       aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name'] ?? 'Admin') ?>&background=2a4e6c&color=fff" 
                             alt="Admin" 
                             class="user-avatar me-2">
                        <span class="d-none d-md-inline"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="/views/admin/profile.php">
                            <i class="fas fa-user-cog me-2"></i>Profile
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="/views/auth/logout.php"
                               onclick="return confirm('Are you sure you want to logout?')">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<style>
    .navbar {
    z-index: 1030; /* Higher than sidebar z-index */
}

.dropdown-menu {
    z-index: 1050; /* Higher than navbar z-index */
}
</style>