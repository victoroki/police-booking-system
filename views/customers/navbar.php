<nav class="navbar navbar-expand-lg navbar-custom border-bottom">
    <div class="container-fluid px-3" style="height: var(--navbar-height);">
        <button class="btn btn-link p-0 sidebar-toggle" id="menu-toggle" style="height: var(--navbar-height);">
            <i class="fas fa-bars"></i>
        </button>

        <div class="d-flex align-items-center ms-auto" style="height: var(--navbar-height);">
            <ul class="navbar-nav">
                <!-- <li class="nav-item me-3">
                    <a class="nav-link" href="/views/Booking/new.php">
                        <i class="fas fa-plus-circle me-1"></i> New Request
                    </a>
                </li> -->
                <li class="nav-item me-3">
                    <a class="nav-link position-relative" href="/views/customers/notifications.php">
                        <i class="fas fa-bell"></i>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center py-0" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? 'User'); ?>&background=2a4e6c&color=fff" 
                             alt="User" class="user-avatar me-2">
                        <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Account'); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/views/customers/profile.php"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <!-- <li><a class="dropdown-item" href="/views/customers/settings.php"><i class="fas fa-cog me-2"></i> Settings</a></li> -->
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/views/auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>