<?php
$page_title = 'Customer Dashboard';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';

// Get dashboard stats
$db = new Database();
$user_id = $_SESSION['user_id'];

// Initialize default values
// Initialize dashboard data
$stats = [
    'total' => 0,
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0
];

$recent_bookings = [];

try {
    // Get booking statistics
    $stats_query = "SELECT 
        COUNT(*) as total,
        SUM(status = 'pending') as pending,
        SUM(status = 'approved') as approved,
        SUM(status = 'rejected') as rejected
        FROM bookings 
        WHERE user_id = ?";
    
    $stats_result = $db->query($stats_query, [$user_id]);
    if (!empty($stats_result)) {
        $stats = array_merge($stats, $stats_result[0]);
    }

    // Get recent bookings with officer information
    $recent_query = "SELECT 
        b.id, b.event_name, b.event_date, b.location, b.status,
        b.created_at, u.full_name as officer_name
        FROM bookings b
        LEFT JOIN users u ON b.officer_id = u.id
        WHERE b.user_id = ?
        ORDER BY b.created_at DESC 
        LIMIT 5";
    
    $recent_bookings = $db->query($recent_query, [$user_id]) ?: [];

} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $_SESSION['error_message'] = "Error loading dashboard data. Please try again.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
    <div class="d-flex">
        <?php include __DIR__ . '/sidebar.php'; ?>

        <div class="main-content">


            <div class="container-fluid p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i> Dashboard Overview</h2>
                    <a href="/views/customer/bookings/new.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> New Booking
                    </a>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card stat-card total h-100">
                            <div class="card-body position-relative">
                                <i class="fas fa-calendar-check stat-icon"></i>
                                <h6 class="text-muted mb-2">Total Bookings</h6>
                                <h2 class="mb-3"><?php echo $stats['total']; ?></h2>
                                <a href="/views/bookings/list.php" class="text-primary text-decoration-none small fw-bold">
                                    View all <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card stat-card pending h-100">
                            <div class="card-body position-relative">
                                <i class="fas fa-clock stat-icon"></i>
                                <h6 class="text-muted mb-2">Pending</h6>
                                <h2 class="mb-3"><?php echo $stats['pending']; ?></h2>
                                <a href="/views/list.php?status=pending" class="text-warning text-decoration-none small fw-bold">
                                    View pending <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card stat-card approved h-100">
                            <div class="card-body position-relative">
                                <i class="fas fa-check-circle stat-icon"></i>
                                <h6 class="text-muted mb-2">Approved</h6>
                                <h2 class="mb-3"><?php echo $stats['approved']; ?></h2>
                                <a href="/views/bookings/list.php?status=approved" class="text-success text-decoration-none small fw-bold">
                                    View approved <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i> Recent Bookings</h5>
                        <a href="/views/customers/bookings/list.php" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-list me-1"></i> View All
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($recent_bookings)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Event Date</th>
                                            <th>Officer</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                            <!-- <th>Actions</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_bookings as $booking): ?>
                                            <tr>
                                                <td>#<?php echo htmlspecialchars($booking['id']); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($booking['event_date'])); ?></td>
                                                <td><?php echo htmlspecialchars($booking['officer_name'] ?? 'Not assigned'); ?></td>
                                                <td><?php echo htmlspecialchars($booking['location']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php
                                                        echo $booking['status'] == 'approved' ? 'success' :
                                                            ($booking['status'] == 'pending' ? 'warning' : 'secondary');
                                                    ?>">
                                                        <?php echo ucfirst($booking['status']); ?>
                                                    </span>
                                                </td>
                                                <!-- <td>
                                                    <a href="/views/customer/bookings/view.php?id=<?php echo $booking['id']; ?>"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i> View
                                                    </a>
                                                </td> -->
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h5>No recent bookings found</h5>
                                <p class="text-muted">You haven't made any bookings yet.</p>
                                <a href="/views/customer/bookings/new.php" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus me-1"></i> Create New Booking
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.main-content').classList.toggle('active');
        });
    </script>
</body>
</html>