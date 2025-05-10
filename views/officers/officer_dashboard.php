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
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
</head>
<body>

<?php include_once '../../includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include_once './navbar.php'; ?>
        <?php include_once './sidebar.php'; ?>
        

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Officer Dashboard</h1>
                <div class="text-muted">Logged in as: <?= htmlspecialchars($_SESSION['user_name']) ?></div>
            </div>

           
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-danger text-white shadow">
                        <div class="card-body">
                            <h5 class="card-title">Pending Assignments</h5>
                            <h2><?= $pending ?></h2>
                            <small>Active Cases</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body">
                            <h5 class="card-title">Completed</h5>
                            <h2><?= $completed ?></h2>
                            <small>This Month</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body">
                            <h5 class="card-title">Upcoming</h5>
                            <h2><?= $upcoming ?></h2>
                            <small>Bookings</small>
                        </div>
                    </div>
                </div>
            </div>

         
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">Recent Assignments</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($assignments)): ?>
                        <div class="alert alert-info">No current assignments</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Case #</th>
                                        <th>Event</th>
                                        <th>Customer</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assignments as $assignment): ?>
                                    <tr>
                                        <td>PS-<?= str_pad($assignment['id'], 4, '0', STR_PAD_LEFT) ?></td>
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
                                            <?= date('H:i', strtotime($assignment['event_time'])) ?>
                                        </td>
                                        <td>
                                            <a href="/views/bookings/view.php?id=<?= $assignment['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                               <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>