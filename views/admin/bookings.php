<?php
$page_title = 'Manage Bookings';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
redirectIfNotAdmin();

$db = new Database();
$status_filter = $_GET['status'] ?? 'all';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['assign_officer'])) {
        $booking_id = (int)$_POST['booking_id'];
        $officer_id = (int)$_POST['officer_id'];
        
        $db->query(
            "UPDATE bookings 
             SET officer_id = ?, status = 'approved'
             WHERE id = ?",
            [$officer_id, $booking_id]
        );
        
        $_SESSION['success_message'] = "Officer assigned successfully!";
    } elseif (isset($_POST['delete_booking'])) {
        $booking_id = (int)$_POST['booking_id'];
        $db->query("DELETE FROM bookings WHERE id = ?", [$booking_id]);
        $_SESSION['success_message'] = "Booking deleted successfully!";
    }
    
    header("Location: bookings.php");
    exit;
}

// Get all bookings
try {
    $query = "SELECT b.*, 
              u1.full_name AS customer_name,
              u2.full_name AS officer_name
              FROM bookings b
              JOIN users u1 ON b.user_id = u1.id
              LEFT JOIN users u2 ON b.officer_id = u2.id";
    
    $params = [];
    if ($status_filter !== 'all') {
        $query .= " WHERE b.status = ?";
        $params[] = $status_filter;
    }
    $query .= " ORDER BY b.created_at DESC";
    
    $bookings = $db->query($query, $params) ?: [];
    
    // Get all officers for dropdown
    $officers = $db->query(
        "SELECT id, full_name FROM users WHERE role = 'officer'"
    ) ?: [];
    
} catch (Exception $e) {
    error_log("Admin bookings error: " . $e->getMessage());
    $bookings = [];
    $error_message = "Error loading bookings. Please try again.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <style>
        .status-badge {
            font-size: 0.9rem;
            padding: 0.35rem 0.65rem;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        .assignment-dropdown {
            min-width: 200px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/navbar.php'; ?>
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-calendar-alt me-2"></i>Manage Bookings</h2>
                <div>
                    <a href="bookings.php?status=all" class="btn btn-sm btn-outline-secondary <?= $status_filter === 'all' ? 'active' : '' ?>">All</a>
                    <a href="bookings.php?status=pending" class="btn btn-sm btn-outline-warning <?= $status_filter === 'pending' ? 'active' : '' ?>">Pending</a>
                    <a href="bookings.php?status=approved" class="btn btn-sm btn-outline-success <?= $status_filter === 'approved' ? 'active' : '' ?>">Approved</a>
                    <a href="bookings.php?status=rejected" class="btn btn-sm btn-outline-danger <?= $status_filter === 'rejected' ? 'active' : '' ?>">Rejected</a>
                </div>
            </div>

            <?php if (!empty($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success_message']) ?>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body p-0">
                    <?php if (!empty($bookings)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Event</th>
                                        <th>Customer</th>
                                        <th>Date & Time</th>
                                        <th>Location</th>
                                        <th>Officer</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $booking): ?>
                                        <?php if (!isset($booking['booking_id'])) {
                                            error_log("Invalid booking entry: " . print_r($booking, true));
                                            continue;
                                        } ?>
                                        <tr>
                                            <td>#<?= $booking['booking_id'] ?></td>
                                            <td><?= htmlspecialchars($booking['event_name']) ?></td>
                                            <td><?= htmlspecialchars($booking['customer_name']) ?></td>
                                            <td>
                                                <?= date('M j, Y', strtotime($booking['event_date'])) ?><br>
                                                <small><?= date('g:i A', strtotime($booking['event_time'])) ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($booking['location']) ?></td>
                                            <td>
                                                <?= $booking['officer_name'] ?? '<span class="text-muted">Unassigned</span>' ?>
                                            </td>
                                            <td>
                                                <span class="badge status-badge bg-<?= 
                                                    match($booking['status']) {
                                                        'approved' => 'success',
                                                        'rejected' => 'danger',
                                                        default => 'warning'
                                                    }
                                                ?>">
                                                    <?= ucfirst($booking['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <?php if ($booking['status'] === 'pending'): ?>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                                            <div class="input-group">
                                                                <select name="officer_id" class="form-select form-select-sm assignment-dropdown" required>
                                                                    <option value="">Assign Officer</option>
                                                                    <?php foreach ($officers as $officer): ?>
                                                                        <option value="<?= $officer['id'] ?>">
                                                                            <?= htmlspecialchars($officer['full_name']) ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <button type="submit" name="assign_officer" 
                                                                        class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-save"></i>
                                                                </button>
                                                            </div>
                                                        </form>
                                                    <?php endif; ?>
                                                    
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                                        <button type="submit" name="delete_booking" 
                                                                class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Are you sure you want to delete this booking?')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                            <h4>No Bookings Found</h4>
                            <p class="text-muted">There are currently no bookings matching your filter.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>