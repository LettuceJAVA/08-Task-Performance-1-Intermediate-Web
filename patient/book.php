<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../includes/csrf.php';

requireRole('patient');
$userId = getCurrentUserId();
$patientId = $pdo->query("SELECT id FROM patients WHERE user_id = $userId")->fetchColumn();

$doctors = $pdo->query("SELECT d.id, u.name, d.specialty FROM doctors d JOIN users u ON d.user_id = u.id")->fetchAll();
$services = $pdo->query("SELECT * FROM services")->fetchAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token']);
    
    $doctor_id = $_POST['doctor_id'];
    $service_id = $_POST['service_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $notes = trim($_POST['notes']);
    
    $start_datetime = "$date $time";
    // Calculate end time based on service duration (default 30 mins if not found)
    $stmt = $pdo->prepare("SELECT duration_minutes FROM services WHERE id = ?");
    $stmt->execute([$service_id]);
    $duration = $stmt->fetchColumn() ?: 30;
    
    $end_datetime = date('Y-m-d H:i:s', strtotime("+$duration minutes", strtotime($start_datetime)));
    
    // Check availability
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM appointments 
        WHERE doctor_id = ? 
        AND status != 'cancelled'
        AND (
            (start_datetime < ? AND end_datetime > ?) OR
            (start_datetime < ? AND end_datetime > ?)
        )
    ");
    $stmt->execute([$doctor_id, $end_datetime, $start_datetime, $end_datetime, $start_datetime]);
    
    if ($stmt->fetchColumn() > 0) {
        $error = "The selected time slot is not available.";
    } else {
        // Book it
        $stmt = $pdo->prepare("
            INSERT INTO appointments (patient_id, doctor_id, service_id, start_datetime, end_datetime, notes, created_by, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->execute([$patientId, $doctor_id, $service_id, $start_datetime, $end_datetime, $notes, $userId]);
        
        setFlashMessage('success', 'Appointment request sent successfully!');
        header("Location: appointments.php");
        exit;
    }
}

require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="h2 mb-4">Book Appointment</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo h($error); ?></div>
            <?php endif; ?>
            
            <div class="card" style="max-width: 700px;">
                <div class="card-body">
                    <form method="POST">
                        <?php csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Doctor</label>
                                <select name="doctor_id" class="form-select" required>
                                    <option value="">Select Doctor</option>
                                    <?php foreach ($doctors as $doc): ?>
                                        <option value="<?php echo $doc['id']; ?>"><?php echo h($doc['name']); ?> (<?php echo h($doc['specialty']); ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Service</label>
                                <select name="service_id" class="form-select" required>
                                    <option value="">Select Service</option>
                                    <?php foreach ($services as $srv): ?>
                                        <option value="<?php echo $srv['id']; ?>"><?php echo h($srv['name']); ?> (<?php echo $srv['duration_minutes']; ?> mins)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Time</label>
                                <select name="time" class="form-select" required>
                                    <option value="">Select Time</option>
                                    <?php 
                                    $start = strtotime('09:00');
                                    $end = strtotime('17:00');
                                    while ($start < $end) {
                                        $timeStr = date('H:i', $start);
                                        $displayStr = date('h:i A', $start);
                                        echo "<option value='$timeStr'>$displayStr</option>";
                                        $start = strtotime('+30 minutes', $start);
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Confirm Booking</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
