<?php 
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'officer') {
    header("Location: /login.php");
    exit;
}
include_once '../../includes/auth.php';
include_once '../../config/database.php';



$db = new Database();
$officer_id = $_SESSION['user_id'];


try {

    $pending = $db->query(
        "SELECT COUNT(*) AS count 
        FROM bookings 
        WHERE officer_id = ? AND status IN ('assigned', 'pending')",
        [$officer_id]
    )[0]['count'];


    $completed = $db->query(
        "SELECT COUNT(*) AS count 
        FROM bookings 
        WHERE officer_id = ? 
        AND status = 'completed' 
        AND MONTH(event_date) = MONTH(CURRENT_DATE())",
        [$officer_id]
    )[0]['count'];

    $upcoming = $db->query(
        "SELECT COUNT(*) AS count 
        FROM bookings 
        WHERE officer_id = ? 
        AND event_date >= CURDATE() 
        AND status NOT IN ('completed', 'canceled')",
        [$officer_id]
    )[0]['count'];

 
    $assignments = $db->query(
        "SELECT b.id, b.event_name, b.status, b.event_date, b.event_time, 
                u.full_name AS customer_name 
         FROM bookings b
         JOIN users u ON b.user_id = u.id
         WHERE b.officer_id = ?
         ORDER BY b.event_date DESC
         LIMIT 5",
        [$officer_id]
    );

} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Dashboard - Police Booking System</title>
    <style>
        /* Main content area spacing */
        .main-content {
            margin-left: 250px; /* Same as sidebar width */
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        
        /* When sidebar is collapsed */
        body.sidebar-collapsed .main-content {
            margin-left: 80px; /* Adjust to match collapsed sidebar width */
        }
        
        /* Card styling */
        .dashboard-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            border-bottom: 1px solid rgba(0,0,0,0.1);
            background-color: #f8f9fa;
        }
        
        /* Status badges */
        .badge {
            font-size: 0.85rem;
            padding: 0.35em 0.65em;
            font-weight: 500;
        }
        
        /* Table styling */
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            background-color: #f8f9fa;
            border-top: none;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            
            body.sidebar-collapsed .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body class="<?= isset($_COOKIE['sidebarCollapsed']) && $_COOKIE['sidebarCollapsed'] === 'true' ? 'sidebar-collapsed' : '' ?>">

<?php include_once '../../includes/header.php'; ?>
<?php include_once './navbar.php'; ?>
<?php include_once './sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Officer Dashboard</h1>
            <div class="text-muted">Logged in as: <?= htmlspecialchars($_SESSION['user_name']) ?></div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4 g-4">
            <div class="col-md-4">
                <div class="card dashboard-card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Pending Assignments</h5>
                                <h2 class="mb-0"><?= $pending ?></h2>
                                <small>Active Cases</small>
                            </div>
                            <i class="fas fa-tasks fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Completed</h5>
                                <h2 class="mb-0"><?= $completed ?></h2>
                                <small>This Month</small>
                            </div>
                            <i class="fas fa-check-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Upcoming</h5>
                                <h2 class="mb-0"><?= $upcoming ?></h2>
                                <small>Bookings</small>
                            </div>
                            <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Assignments -->
        <div class="card dashboard-card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Recent Assignments
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($assignments)): ?>
                    <div class="alert alert-info mb-0">No current assignments</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Case #</th>
                                    <th>Event</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignments as $assignment): ?>
                                <tr>
                                    <td class="fw-bold">PS-<?= str_pad($assignment['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                    <td><?= htmlspecialchars($assignment['event_name']) ?></td>
                                    <td><?= htmlspecialchars($assignment['customer_name']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= match($assignment['status']) {
                                            'completed' => 'success',
                                            'canceled' => 'secondary',
                                            'in_progress' => 'warning',
                                            default => 'primary'
                                        } ?>">
                                            <?= ucfirst(str_replace('_', ' ', $assignment['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= date('M j, Y', strtotime($assignment['event_date'])) ?>
                                        <small class="text-muted"><?= date('H:i', strtotime($assignment['event_time'])) ?></small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

</body>
</html>