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
        
        // Verify booking exists and is unassigned
        $existing = $db->query(
            "SELECT id FROM bookings WHERE id = ? AND officer_id IS NULL",
            [$booking_id]
        );
        
        if (empty($existing)) {
            $_SESSION['error_message'] = "Booking already assigned or doesn't exist";
        } else {
            $db->query(
                "UPDATE bookings 
                 SET officer_id = ?, updated_at = NOW()
                 WHERE id = ?",
                [$officer_id, $booking_id]
            );
            
            $_SESSION['success_message'] = "Officer assigned successfully!";
            
            // Add notification for the officer
            // $db->query(
            //     "INSERT INTO notifications (user_id, message, type)
            //      VALUES (?, ?, ?)",
            //     [$officer_id, "New booking assigned (ID: $booking_id)", "assignment"]
            // );
        }
    } elseif (isset($_POST['delete_booking'])) {
        $booking_id = (int)$_POST['booking_id'];
        $db->query("DELETE FROM bookings WHERE id = ?", [$booking_id]);
        $_SESSION['success_message'] = "Booking deleted successfully!";
    }
    
    header("Location: bookings.php");
    exit;
}

try {
    // Original query without county handling
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

    // Original officers query
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
        .stat-card {
            border-left: 4px solid;
            transition: all 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .stat-total { border-color: #007bff; }
        .stat-pending { border-color: #ffc107; }
        .stat-approved { border-color: #28a745; }
        .stat-rejected { border-color: #dc3545; }
        .stat-unassigned { border-color: #6c757d; }
        .county-badge {
            font-size: 0.75rem;
            background-color: #e9ecef;
            color: #495057;
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
                    <a href="bookings.php?status=unassigned" class="btn btn-sm btn-outline-info <?= $status_filter === 'unassigned' ? 'active' : '' ?>">Unassigned</a>
                </div>
            </div>

            <?php if (!empty($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success_message']) ?>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['error_message']) ?>
                    <?php unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card stat-card stat-total">
                        <div class="card-body py-2">
                            <h6 class="card-title">Total</h6>
                            <h4 class="mb-0"><?= $stats['total'] ?? 0 ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card stat-card stat-pending">
                        <div class="card-body py-2">
                            <h6 class="card-title">Pending</h6>
                            <h4 class="mb-0"><?= $stats['pending'] ?? 0 ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card stat-card stat-approved">
                        <div class="card-body py-2">
                            <h6 class="card-title">Approved</h6>
                            <h4 class="mb-0"><?= $stats['approved'] ?? 0 ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card stat-card stat-rejected">
                        <div class="card-body py-2">
                            <h6 class="card-title">Rejected</h6>
                            <h4 class="mb-0"><?= $stats['rejected'] ?? 0 ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card stat-card stat-unassigned">
                        <div class="card-body py-2">
                            <h6 class="card-title">Unassigned</h6>
                            <h4 class="mb-0"><?= $stats['unassigned'] ?? 0 ?></h4>
                        </div>
                    </div>
                </div>
            </div>

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
                                        <tr data-county="<?= htmlspecialchars($booking['customer_county'] ?? '') ?>">
                                            <td>#<?= $booking['id'] ?></td>
                                            <td>
                                                <?= htmlspecialchars($booking['event_name']) ?>
                                                <?php if (!empty($booking['customer_county'])): ?>
                                                    <span class="county-badge ms-2"><?= htmlspecialchars($booking['customer_county']) ?></span>
                                                <?php endif; ?>
                                            </td>
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
                                                    <?php if ($booking['status'] === 'pending' || empty($booking['officer_id'])): ?>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                                            <div class="input-group">
                                                                <select name="officer_id" class="form-select form-select-sm assignment-dropdown" required>
                                                                    <option value="">Assign Officer</option>
                                                                    <?php foreach ($officers as $officer): ?>
                                                                        <option value="<?= $officer['id'] ?>" 
                                                                                data-county="<?= htmlspecialchars($officer['county'] ?? '') ?>">
                                                                            <?= htmlspecialchars($officer['full_name']) ?>
                                                                            <?php if (!empty($officer['county'])): ?>
                                                                                (<?= htmlspecialchars($officer['county']) ?>)
                                                                            <?php endif; ?>
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
                                                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
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

    <script>
    // Filter officers by county when assigning
    document.addEventListener('DOMContentLoaded', function() {
        const bookingRows = document.querySelectorAll('tr[data-county]');
        
        bookingRows.forEach(row => {
            const county = row.getAttribute('data-county');
            const select = row.querySelector('select[name="officer_id"]');
            
            if (select && county) {
                // Show only officers from matching county or without county specified
                Array.from(select.options).forEach(option => {
                    if (option.value === "") return; // Keep the "Assign Officer" option
                    
                    const optionCounty = option.getAttribute('data-county');
                    option.style.display = (!optionCounty || optionCounty === county) ? '' : 'none';
                });
                
                // Add event listener to show all options when dropdown opens
                select.addEventListener('mousedown', function() {
                    Array.from(this.options).forEach(option => {
                        option.style.display = '';
                    });
                });
                
                // Re-filter when dropdown closes
                select.addEventListener('change', function() {
                    Array.from(this.options).forEach(option => {
                        const optionCounty = option.getAttribute('data-county');
                        option.style.display = (!optionCounty || optionCounty === county) ? '' : 'none';
                    });
                });
            }
        });
    });
    </script>
</body>
</html>