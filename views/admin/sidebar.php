<div class="sidebar-wrapper">
    <div class="sidebar">
        <div class="sidebar-brand">
            <h4 class="mb-1">Police Booking System</h4>
            <div class="text-muted small">Admin Dashboard</div>
        </div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="/views/admin/dashboard.php"
                   class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="/views/admin/officer.php"
                   class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'officer.php' ? 'active' : '' ?>">
                    <i class="fas fa-user-shield"></i> Manage Officers
                </a>
            </li>
            <li class="nav-item">
                <a href="/views/admin/bookings.php"
                   class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-check"></i> All Bookings
                </a>
            </li>
            <li class="nav-item">
                <a href="/views/admin/reports.php"
                   class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </li>
            <li class="nav-item">
                <a href="/views/admin/profile.php"
                   class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>">
                    <i class="fas fa-user-cog"></i> Admin Profile
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

.sidebar {
    height: 100%;
    overflow-y: auto;
    color: white;
}

.sidebar-nav .nav-link {
    padding: 0.75rem 1.5rem;
    color: rgba(255,255,255,0.8);
    border-left: 4px solid transparent;
    transition: all 0.3s;
}

.sidebar-nav .nav-link:hover,
.sidebar-nav .nav-link.active {
    background: rgba(255,255,255,0.1);
    color: white;
    border-left: 4px solid #e74c3c;
}

.sidebar-nav .nav-link i {
    width: 20px;
    margin-right: 0.75rem;
}

.sidebar-brand {
    padding: 1.5rem;
    background: rgba(0,0,0,0.1);
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

@media (max-width: 768px) {
    .sidebar-wrapper {
        margin-left: -250px;
    }
    
    .sidebar-expanded .sidebar-wrapper {
        margin-left: 0;
    }
}
</style>