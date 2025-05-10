<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container-fluid px-3">
        <!-- Sidebar Toggle -->
        <button class="btn btn-link me-3" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Officer Navigation -->
        <div class="d-flex align-items-center ms-auto">
            <ul class="navbar-nav">
                <!-- Notifications -->
                <li class="nav-item me-3">
                    <a class="nav-link" href="/views/officer/notifications.php">
                        <i class="fas fa-bell"></i>
                    </a>
                </li>

                <!-- User Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" 
                       href="#" 
                       id="officerDropdown" 
                       role="button" 
                       data-bs-toggle="dropdown" 
                       aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name'] ?? 'Officer') ?>&background=2a4e6c&color=fff" 
                             alt="Officer" 
                             class="user-avatar me-2">
                        <span class="d-none d-md-inline"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Officer') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="officerDropdown">
                        <li><a class="dropdown-item" href="/views/officer/profile.php">
                            <i class="fas fa-user me-2"></i> Profile
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="/views/auth/logout.php" 
                               onclick="return confirm('Are you sure you want to logout?')">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>