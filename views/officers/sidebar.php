<div class="sidebar-wrapper">
    <div class="sidebar">
        <div class="sidebar-brand">
            <h4 class="mb-1">Police Booking System</h4>
            <div class="text-muted small">Officer Dashboard</div>
        </div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="/views/officers/officer_dashboard.php"
                   class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'officer_dashboard.php' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="/views/officers/assignments.php"
                   class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'assignments.php' ? 'active' : '' ?>">
                    <i class="fas fa-tasks"></i> My Assignments
                </a>
            </li>
            <li class="nav-item">
                <a href="/views/officers/officer_profile.php"
                   class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'officer_profile.php' ? 'active' : '' ?>">
                    <i class="fas fa-user"></i> My Profile
                </a>
            </li>
        </ul>
    </div>
</div>

<style>
.sidebar-wrapper {
    width: 250px;
    position: fixed;
    top: 56px; /* Navbar height */
    left: 0;
    bottom: 0;
    background: #2a4e6c;
    z-index: 1000;
    transition: all 0.3s;
}

.sidebar-nav .nav-link {
    padding: 0.75rem 1.5rem;
    color: rgba(255,255,255,0.8);
    border-left: 4px solid transparent;
    transition: all 0.3s;
}

.sidebar-nav .nav-link.active,
.sidebar-nav .nav-link:hover {
    background: rgba(255,255,255,0.1);
    color: white;
    border-left: 4px solid #e74c3c;
}

.sidebar-nav .nav-link i {
    width: 20px;
    margin-right: 0.75rem;
}
</style>