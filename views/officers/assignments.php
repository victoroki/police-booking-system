<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
redirectIfNotOfficer();

$db = new Database();
$officer_id = $_SESSION['user_id'];
error_log("Officer ID: " . $officer_id);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $booking_id = (int)$_POST['booking_id'];
        $new_status = $_POST['status'];

        $result = $db->query(
            "UPDATE bookings SET status = ? 
             WHERE id = ? AND officer_id = ?",
            [$new_status, $booking_id, $officer_id]
        );

        $_SESSION['success_message'] = "Status updated successfully!";
        header("Location: assignments.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error updating status: " . $e->getMessage();
    }
}


try {
    $assignments = $db->query(
        "SELECT 
            b.id AS booking_id,
            b.event_name,
            b.event_date,
            b.event_time,
            b.location,
            b.status,
            u.full_name AS customer_name
         FROM bookings b
         JOIN users u ON b.user_id = u.id
         WHERE b.officer_id = ?
         AND b.status IN ('pending', 'approved')
         ORDER BY b.event_date ASC",
        [$officer_id]
    ) ?: [];

    error_log("Assignments count: " . count($assignments)); 

} catch (Exception $e) {
    error_log("Assignments Error: " . $e->getMessage());
    $assignments = [];
    $error_message = "Database Error: " . $e->getMessage();
}
?>


<?php include __DIR__ . '/navbar.php'; ?>
<?php include __DIR__ . '/sidebar.php'; ?>
<div class="main-content">
    <div class="container-fluid p-4">
        <?php if (!empty($_SESSION['success_message'])): ?>
            <div class="alert alert-success mb-4">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger mb-4">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <h2 class="mb-4"><i class="fas fa-tasks me-2"></i> My Assignments</h2>

        <div class="card">
            <div class="card-body p-0">
                <?php if (!empty($assignments)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Event</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Customer</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignments as $assignment): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($assignment['event_name']) ?></td>
                                        <td><?= date('M j, Y', strtotime($assignment['event_date'])) ?></td>
                                        <td><?= date('g:i A', strtotime($assignment['event_time'])) ?></td>
                                        <td><?= htmlspecialchars($assignment['customer_name']) ?></td>
                                        <td><?= htmlspecialchars($assignment['location']) ?></td>
                                        <td>
                                            <span class="badge bg-<?=
                                                                    match ($assignment['status']) {
                                                                        'approved' => 'success',
                                                                        'rejected' => 'danger',
                                                                        default => 'warning'
                                                                    }
                                                                    ?>">
                                                <?= ucfirst($assignment['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($assignment['status'] === 'pending'): ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="booking_id" value="<?= $assignment['booking_id'] ?>">
                                                    <button type="submit" name="status" value="approved"
                                                        class="btn btn-sm btn-success me-1">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                    <button type="submit" name="status" value="rejected"
                                                        class="btn btn-sm btn-danger">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted">Action completed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                        <h4>No Assignments Found</h4>
                        <p class="text-muted">You don't have any assigned bookings yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>