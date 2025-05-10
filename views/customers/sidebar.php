<div class="sidebar-wrapper">
    <div class="sidebar">
        <div class="sidebar-brand">
            <h4 class="mb-1">Police Booking System</h4>
            <div class="text-muted small">Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Customer'); ?></div>
        </div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="/views/customers/dashboard.php"
                   class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="/views/Bookings/new.php"
                   class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'new.php' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-plus"></i> New Booking
                </a>
            </li>
            <li class="nav-item">
                <a href="/views/Bookings/list.php"
                   class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'list.php' ? 'active' : ''; ?>">
                    <i class="fas fa-list"></i> My Bookings
                </a>
            </li>
            <li class="nav-item">
                <a href="/views/customers/profile.php"
                   class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user"></i> Profile
                </a>
            </li>
        </ul>
    </div>
</div>