<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../includes/csrf.php';

requireRole('patient');
$userId = getCurrentUserId();
$patientId = $pdo->query("SELECT id FROM patients WHERE user_id = $userId")->fetchColumn();

// Handle Cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {
    verify_csrf_token($_POST['csrf_token']);
    $cancelId = $_POST['cancel_id'];
    
    $stmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ? AND patient_id = ?");
    $stmt->execute([$cancelId, $patientId]);
    setFlashMessage('success', 'Appointment cancelled.');
    header("Location: appointments.php");
    exit;
}

$appts = $pdo->query("
    SELECT a.*, u.name as doctor_name, s.name as service_name
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.id
    JOIN users u ON d.user_id = u.id
    JOIN services s ON a.service_id = s.id
    WHERE a.patient_id = $patientId
    ORDER BY a.start_datetime DESC
")->fetchAll();

require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="h2 mb-4">My Appointments</h1>
            <?php displayFlashMessage(); ?>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Doctor</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appts as $appt): ?>
                                <tr>
                                    <td><?php echo formatDateTime($appt['start_datetime']); ?></td>
                                    <td><?php echo h($appt['doctor_name']); ?></td>
                                    <td><?php echo h($appt['service_name']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo match($appt['status']) {
                                                'confirmed' => 'success',
                                                'pending' => 'warning',
                                                'cancelled' => 'danger',
                                                'completed' => 'info',
                                                default => 'secondary'
                                            };
                                        ?>">
                                            <?php echo ucfirst($appt['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($appt['status'] === 'pending' || $appt['status'] === 'confirmed'): ?>
                                            <form method="POST" onsubmit="return confirm('Are you sure you want to cancel?');">
                                                <?php csrf_field(); ?>
                                                <input type="hidden" name="cancel_id" value="<?php echo $appt['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
