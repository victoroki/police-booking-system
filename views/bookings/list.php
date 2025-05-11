<?php
$page_title = 'My Bookings';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../customers/navbar.php';
require_once __DIR__ . '/../customers/sidebar.php';
require_once __DIR__ . '/../../config/database.php';

$db = new Database();
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header('Location: /views/auth/login.php');
    exit;
}

try {

    $status_filter = $_GET['status'] ?? 'all';


    $query = "SELECT * FROM bookings WHERE user_id = ?";
    $params = [$user_id];

    if ($status_filter !== 'all' && in_array($status_filter, ['pending', 'approved', 'rejected'])) {
        $query .= " AND status = ?";
        $params[] = $status_filter;
    }

    $query .= " ORDER BY event_date DESC, created_at DESC";
    $bookings = $db->query($query, $params);
    
    
    if (!is_array($bookings)) {
        $bookings = [];
    }
    
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $bookings = [];
    $error_message = "Could not load bookings. Please try again later.";
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
<div class="main-content">
    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="fas fa-list me-2"></i> My Bookings</h2>
            <a href="/views/Booking/new.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> New Booking
            </a>
        </div>

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link <?= $status_filter === 'all' ? 'active' : '' ?>" 
                   href="?status=all">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $status_filter === 'pending' ? 'active' : '' ?>" 
                   href="?status=pending">Pending</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $status_filter === 'approved' ? 'active' : '' ?>" 
                   href="?status=approved">Approved</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $status_filter === 'rejected' ? 'active' : '' ?>" 
                   href="?status=rejected">Rejected</a>
            </li>
        </ul>

       
        <div class="card">
            <div class="card-body p-0">
                <?php if (!empty($bookings)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Event</th>
                                    <th>Date & Time</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <!-- <th>Actions</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td>#<?= str_pad($booking['id'], 6, '0', STR_PAD_LEFT) ?></td>
                                        <td><?= htmlspecialchars($booking['event_name']) ?></td>
                                        <td>
                                            <?= date('M j, Y', strtotime($booking['event_date'])) ?><br>
                                            <small class="text-muted"><?= date('g:i A', strtotime($booking['event_time'])) ?></small>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($booking['county']) ?><br>
                                            <small class="text-muted"><?= htmlspecialchars($booking['location']) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $booking['status'] === 'approved' ? 'success' : 
                                                ($booking['status'] === 'pending' ? 'warning' : 
                                                ($booking['status'] === 'rejected' ? 'danger' : 'secondary')) 
                                            ?>">
                                                <?= ucfirst($booking['status']) ?>
                                            </span>
                                        </td>
                                        <!-- <td>
                                            <a href="/views/Booking/view.php?id=<?= $booking['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary me-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($booking['status'] === 'pending'): ?>
                                                <a href="/views/Booking/edit.php?id=<?= $booking['id'] ?>" 
                                                   class="btn btn-sm btn-outline-secondary me-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="/views/Booking/cancel.php?id=<?= $booking['id'] ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td> -->
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                        <h4>No bookings found</h4>
                        <p class="text-muted">
                            <?= $status_filter !== 'all' ? "You don't have any {$status_filter} bookings." : "You haven't made any bookings yet." ?>
                        </p>
                        <a href="/views/Booking/new.php" class="btn btn-primary mt-2">
                            <i class="fas fa-plus me-2"></i> Create New Booking
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
<!-- 
<style>
    .table th {
        border-top: none;
        font-weight: 600;
        color: var(--dark-color);
        background: #f8f9fa;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .badge {
        padding: 0.35em 0.65em;
        font-weight: 500;
        font-size: 0.85em;
    }
    
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        color: var(--primary-color);
        border-bottom: 2px solid var(--primary-color);
    }
</style> -->