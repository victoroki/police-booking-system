<?php
$page_title = 'System Reports';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../lib/controllers/ReportController.php';

redirectIfNotAdmin();

$db = new Database();
$reportController = new ReportController($db);

// Default date range (last 30 days)
$startDate = date('Y-m-d', strtotime('-30 days'));
$endDate = date('Y-m-d');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'] ?? $startDate;
    $endDate = $_POST['end_date'] ?? $endDate;
    $reportType = $_POST['report_type'] ?? 'bookings';
    $statusFilter = $_POST['status'] ?? null;

    try {
        $reportData = $reportController->generateReport(
            $reportType,
            $startDate,
            $endDate,
            $statusFilter
        );

        if (isset($_POST['export_excel'])) {
            $reportController->exportToExcel(
                $reportData,
                $reportType . '_report_' . date('Y-m-d')
            );
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<?php include __DIR__ . '/navbar.php'; ?>
<?php include __DIR__ . '/sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid p-4">
        <h2 class="mb-4"><i class="fas fa-chart-bar me-2"></i> System Reports</h2>

        <!-- Report Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Report Type</label>
                            <select name="report_type" class="form-select">
                                <option value="bookings" <?= ($reportType ?? '') === 'bookings' ? 'selected' : '' ?>>Bookings Report</option>
                                <option value="officer-performance" <?= ($reportType ?? '') === 'officer-performance' ? 'selected' : '' ?>>Officer Performance</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" 
                                   value="<?= htmlspecialchars($startDate) ?>" required>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" 
                                   value="<?= htmlspecialchars($endDate) ?>" required>
                        </div>
                        
                        <div class="col-md-3" id="status-filter-container" 
                             style="display: <?= ($reportType ?? '') === 'bookings' ? 'block' : 'none' ?>">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending" <?= ($statusFilter ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="approved" <?= ($statusFilter ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="rejected" <?= ($statusFilter ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" name="generate_report" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i> Filter
                        </button>
                        <button type="submit" name="export_excel" class="btn btn-success ms-2">
                            <i class="fas fa-file-excel me-2"></i> Export to Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Report Results -->
        <div class="card">
            <div class="card-body">
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error_message) ?>
                    </div>
                <?php elseif (isset($reportData)): ?>
                    <h4 class="mb-3">
                        <?= ($reportType === 'bookings') ? 'Bookings Report' : 'Officer Performance Report' ?>
                        <small class="text-muted">(<?= date('M j, Y', strtotime($startDate)) ?> to <?= date('M j, Y', strtotime($endDate)) ?>)</small>
                    </h4>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <?php foreach (array_keys($reportData[0] ?? []) as $header): ?>
                                        <th><?= ucwords(str_replace('_', ' ', $header)) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reportData as $row): ?>
                                    <tr>
                                        <?php foreach ($row as $cell): ?>
                                            <td><?= htmlspecialchars($cell) ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Showing <?= count($reportData) ?> records
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-chart-pie fa-4x text-muted mb-3"></i>
                        <h4>No Report Generated</h4>
                        <p class="text-muted">Use the filters above to generate a report</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide status filter based on report type
document.querySelector('[name="report_type"]').addEventListener('change', function() {
    const statusContainer = document.getElementById('status-filter-container');
    statusContainer.style.display = this.value === 'bookings' ? 'block' : 'none';
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>