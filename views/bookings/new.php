<?php
$page_title = 'New Booking';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    
    
    $event_name = htmlspecialchars($_POST['event_name'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? '';
    $county = htmlspecialchars($_POST['county'] ?? '');
    $location = htmlspecialchars($_POST['location'] ?? '');
    $phone = htmlspecialchars($_POST['phone'] ?? '');
    $user_id = $_SESSION['user_id'] ?? null;
    

    $errors = [];
    if (empty($event_name)) $errors[] = 'Event name is required';
    if (empty($event_date)) $errors[] = 'Event date is required';
    if (empty($event_time)) $errors[] = 'Event time is required';
    if (empty($county)) $errors[] = 'County is required';
    if (empty($location)) $errors[] = 'Location is required';
    if (empty($phone)) $errors[] = 'Phone number is required';
    if (!preg_match('/^[0-9]{10,15}$/', $phone)) $errors[] = 'Invalid phone number format';
    
    if (empty($errors) && $user_id) {
        try {
            
            $result = $db->query(
                "INSERT INTO bookings 
                (user_id, event_name, event_date, event_time, county, location, phone, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())",
                [$user_id, $event_name, $event_date, $event_time, $county, $location, $phone]
            );
            
            if ($result) {
                $_SESSION['success_message'] = 'Booking request submitted successfully!';
                header('Location: /views/bookings/list.php');
                exit;
            }
        } catch (Exception $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
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
    <div class="d-flex">
        
        <?php include __DIR__ . '/../customers/sidebar.php'; ?>

        
        <div class="main-content">
           
            <?php include __DIR__ . '/../customers/navbar.php'; ?>

            <div class="container-fluid p-4">
                <div class="card booking-card">
                    <div class="card-header booking-header">
                        <h3 class="mb-0"><i class="fas fa-calendar-plus me-2"></i> New Police Booking Request</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <strong>Error!</strong> Please fix the following issues:
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form action="/views/bookings/new.php" method="POST">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="event_name" class="form-label">Event Name/Type</label>
                                    <input type="text" class="form-control" id="event_name" name="event_name" 
                                           value="<?php echo htmlspecialchars($_POST['event_name'] ?? ''); ?>" 
                                           required placeholder="e.g. Wedding Ceremony, Corporate Event">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="event_date" class="form-label">Event Date</label>
                                    <input type="date" class="form-control" id="event_date" name="event_date" 
                                           value="<?php echo htmlspecialchars($_POST['event_date'] ?? ''); ?>" 
                                           min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="event_time" class="form-label">Event Time</label>
                                    <input type="time" class="form-control" id="event_time" name="event_time" 
                                           value="<?php echo htmlspecialchars($_POST['event_time'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="county" class="form-label">County</label>
                                    <select class="form-select county-select" id="county" name="county" required>
                                        <option value="" disabled selected>Select your county</option>
                                        <option value="Kisii" <?php echo (isset($_POST['county']) && $_POST['county'] === 'Kisii') ? 'selected' : ''; ?>>Kisii</option>
                                        <option value="Nyamira" <?php echo (isset($_POST['county']) && $_POST['county'] === 'Nyamira') ? 'selected' : ''; ?>>Nyamira</option>
                                        <option value="Migori" <?php echo (isset($_POST['county']) && $_POST['county'] === 'Migori') ? 'selected' : ''; ?>>Migori</option>
                                        <option value="Kisumu" <?php echo (isset($_POST['county']) && $_POST['county'] === 'Kisumu') ? 'selected' : ''; ?>>Kisumu</option>
                                        <option value="Nairobi" <?php echo (isset($_POST['county']) && $_POST['county'] === 'Nairobi') ? 'selected' : ''; ?>>Nairobi</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="location" class="form-label">Specific Location/Venue</label>
                                    <input type="text" class="form-control" id="location" name="location" 
                                           value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>" 
                                           required placeholder="e.g. Nyamira Town Hall, Ground Floor">
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="phone" class="form-label">Contact Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" 
                                           required placeholder="e.g. 0712345678" pattern="[0-9]{10,15}">
                                    <small class="text-muted">Format: 07XXXXXXXX or 2547XXXXXXXX</small>
                                </div>
                                
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-submit btn-lg w-100">
                                        <i class="fas fa-paper-plane me-2"></i> Submit Booking Request
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   
    <script>
        document.getElementById('event_date').min = new Date().toISOString().split('T')[0];
        
     
        document.querySelector('form').addEventListener('submit', function(e) {
            const phone = document.getElementById('phone').value;
            if (!/^[0-9]{10,15}$/.test(phone)) {
                alert('Please enter a valid phone number (10-15 digits)');
                e.preventDefault();
            }
        });
    </script>
    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>
</body>

</html>