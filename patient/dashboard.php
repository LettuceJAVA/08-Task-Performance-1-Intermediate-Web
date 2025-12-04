<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

requireRole('patient');
$userId = getCurrentUserId();
$patientId = $pdo->query("SELECT id FROM patients WHERE user_id = $userId")->fetchColumn();

// Upcoming Appointments
$stmt = $pdo->prepare("
    SELECT a.*, u.name as doctor_name, s.name as service_name
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.id
    JOIN users u ON d.user_id = u.id
    JOIN services s ON a.service_id = s.id
    WHERE a.patient_id = ? AND a.start_datetime >= NOW()
    ORDER BY a.start_datetime ASC
    LIMIT 5
");
$stmt->execute([$patientId]);
$upcoming = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="h2 mb-4">Patient Dashboard</h1>
            
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card bg-light border-0 shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title text-primary">Need a Doctor?</h4>
                                <p class="card-text text-muted">Book an appointment with our specialists today.</p>
                            </div>
                            <a href="book.php" class="btn btn-primary btn-lg">Book Appointment</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">Upcoming Appointments</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Doctor</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcoming as $appt): ?>
                                <tr>
                                    <td><?php echo formatDateTime($appt['start_datetime']); ?></td>
                                    <td><?php echo h($appt['doctor_name']); ?></td>
                                    <td><?php echo h($appt['service_name']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $appt['status'] == 'confirmed' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($appt['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($upcoming)): ?>
                                <tr><td colspan="4" class="text-center">No upcoming appointments.</td></tr>
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
