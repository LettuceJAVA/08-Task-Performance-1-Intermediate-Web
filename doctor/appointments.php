<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../includes/csrf.php';

requireRole('doctor');
$userId = getCurrentUserId();
$doctorId = $pdo->query("SELECT id FROM doctors WHERE user_id = $userId")->fetchColumn();

// Handle Status Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token']);
    $apptId = $_POST['appointment_id'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ? AND doctor_id = ?");
    $stmt->execute([$status, $apptId, $doctorId]);
    setFlashMessage('success', "Appointment marked as $status.");
    header("Location: appointments.php");
    exit;
}

$appts = $pdo->query("
    SELECT a.*, u.name as patient_name, s.name as service_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN users u ON p.user_id = u.id
    JOIN services s ON a.service_id = s.id
    WHERE a.doctor_id = $doctorId
    ORDER BY a.start_datetime DESC
")->fetchAll();

require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="h2 mb-4">All Appointments</h1>
            <?php displayFlashMessage(); ?>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appts as $appt): ?>
                                <tr>
                                    <td><?php echo formatDateTime($appt['start_datetime']); ?></td>
                                    <td>
                                        <a href="patient_view.php?id=<?php echo $appt['patient_id']; ?>" class="text-decoration-none fw-bold">
                                            <?php echo h($appt['patient_name']); ?>
                                        </a>
                                    </td>
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
                                        <?php if ($appt['status'] === 'pending'): ?>
                                            <form method="POST" class="d-inline">
                                                <?php csrf_field(); ?>
                                                <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="btn btn-sm btn-success">Confirm</button>
                                            </form>
                                            <form method="POST" class="d-inline">
                                                <?php csrf_field(); ?>
                                                <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                            </form>
                                        <?php elseif ($appt['status'] === 'confirmed'): ?>
                                            <form method="POST" class="d-inline">
                                                <?php csrf_field(); ?>
                                                <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-sm btn-info text-white">Complete</button>
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
