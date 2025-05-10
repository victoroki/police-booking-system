<?php

session_start();


require_once __DIR__ . '/../../includes/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /login.php");
    exit;
}

require_once __DIR__ . '/../../config/database.php';


$db = new Database();
$connection = $db->getConnection();

function getCount($connection, $table, $condition = '') {
    $table = $connection->real_escape_string($table);
    $query = "SELECT COUNT(*) FROM `$table`";
    
    if ($condition) {
        $query .= " WHERE $condition";
    }
    
    $result = $connection->query($query);
    return $result ? (int)$result->fetch_row()[0] : 0;
}

$bookingCount = getCount($connection, 'bookings');
$officerCount = getCount($connection, 'users', "role = 'officer'");
$pendingCount = getCount($connection, 'bookings', "status = 'Pending'");
$approvedCount = getCount($connection, 'bookings', "status = 'Approved'");

$connection->close();

$page_title = "Admin Dashboard";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Police Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2a4e6c;
            --secondary-color: #3a6688;
            --accent-color: #e74c3c;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --sidebar-width: 250px;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--primary-color);
            color: white;
            position: fixed;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .sidebar-brand {
            padding: 1.5rem 1rem;
            background: rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .sidebar-nav {
            padding: 0;
            list-style: none;
        }
        
        .sidebar-nav .nav-item {
            width: 100%;
        }
        
        .sidebar-nav .nav-link {
            padding: 1rem;
            color: rgba(255,255,255,0.8);
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }
        
        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            text-decoration: none;
        }
        
        .sidebar-nav .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            transition: all 0.3s;
        }
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            background: white !important;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }
        
        .stat-card {
            border-left: 4px solid;
        }
        
        .stat-card.total {
            border-left-color: var(--primary-color);
        }
        
        .stat-card.pending {
            border-left-color: #f39c12;
        }
        
        .stat-card.approved {
            border-left-color: #27ae60;
        }
        
        .stat-icon {
            font-size: 2rem;
            opacity: 0.3;
            position: absolute;
            right: 1.5rem;
            top: 1.5rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .badge {
            padding: 0.35em 0.65em;
            font-weight: 500;
            font-size: 0.85em;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--dark-color);
            background: #f8f9fa;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .empty-state {
            padding: 3rem;
            text-align: center;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -var(--sidebar-width);
            }
            
            .main-content {
                width: 100%;
                margin-left: 0;
            }
            
            .sidebar.active {
                margin-left: 0;
            }
            
            .main-content.active {
                margin-left: var(--sidebar-width);
            }
        }
    </style>
</head>

<body class="bg-light">
    <?php include __DIR__ . '/navbar.php'; ?>
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="main-content">
        <div class="container-fluid">
            <h1 class="mb-4">Dashboard Overview</h1>
            
            <div class="row g-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card card-counter bg-primary text-white shadow-lg">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar-check fa-3x mb-3"></i>
                            <h2 class="h5">Total Bookings</h2>
                            <div class="display-4 fw-bold"><?= $bookingCount ?></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card card-counter bg-success text-white shadow-lg">
                        <div class="card-body text-center">
                            <i class="fas fa-user-shield fa-3x mb-3"></i>
                            <h2 class="h5">Total Officers</h2>
                            <div class="display-4 fw-bold"><?= $officerCount ?></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card card-counter bg-warning text-dark shadow-lg">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-3x mb-3"></i>
                            <h2 class="h5">Pending Bookings</h2>
                            <div class="display-4 fw-bold"><?= $pendingCount ?></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card card-counter bg-info text-white shadow-lg">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <h2 class="h5">Approved Bookings</h2>
                            <div class="display-4 fw-bold"><?= $approvedCount ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>