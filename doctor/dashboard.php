<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

requireRole('doctor');
$userId = getCurrentUserId();
$doctorId = $pdo->query("SELECT id FROM doctors WHERE user_id = $userId")->fetchColumn();

// Stats
$today = date('Y-m-d');
$appt_today = $pdo->query("SELECT COUNT(*) FROM appointments WHERE doctor_id = $doctorId AND DATE(start_datetime) = '$today'")->fetchColumn();
$appt_pending = $pdo->query("SELECT COUNT(*) FROM appointments WHERE doctor_id = $doctorId AND status = 'pending'")->fetchColumn();

// Today's Appointments
$stmt = $pdo->prepare("
    SELECT a.*, p.user_id, u.name as patient_name, s.name as service_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN users u ON p.user_id = u.id
    JOIN services s ON a.service_id = s.id
    WHERE a.doctor_id = ? AND DATE(a.start_datetime) = ?
    ORDER BY a.start_datetime ASC
");
$stmt->execute([$doctorId, $today]);
$todays_appts = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="h2 mb-4">Doctor Dashboard</h1>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Appointments Today</h5>
                            <h2 class="display-4"><?php echo $appt_today; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-warning text-dark h-100">
                        <div class="card-body">
                            <h5 class="card-title">Pending Requests</h5>
                            <h2 class="display-4"><?php echo $appt_pending; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">Today's Schedule</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Patient</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($todays_appts as $appt): ?>
                                <tr>
                                    <td><?php echo formatTime($appt['start_datetime']); ?></td>
                                    <td><?php echo h($appt['patient_name']); ?></td>
                                    <td><?php echo h($appt['service_name']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $appt['status'] == 'confirmed' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($appt['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="appointments.php" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($todays_appts)): ?>
                                <tr><td colspan="5" class="text-center">No appointments today.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
