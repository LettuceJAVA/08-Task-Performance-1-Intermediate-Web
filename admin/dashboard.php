<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

requireRole('admin');

// Fetch Stats
$stats = [];
$stats['patients'] = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$stats['doctors'] = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
$stats['appointments_today'] = $pdo->query("SELECT COUNT(*) FROM appointments WHERE DATE(start_datetime) = CURDATE()")->fetchColumn();
$stats['pending'] = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn();

// Fetch Upcoming Appointments
$stmt = $pdo->query("
    SELECT a.*, p.user_id as p_uid, u_p.name as patient_name, d.user_id as d_uid, u_d.name as doctor_name, s.name as service_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN users u_p ON p.user_id = u_p.id
    JOIN doctors d ON a.doctor_id = d.id
    JOIN users u_d ON d.user_id = u_d.id
    JOIN services s ON a.service_id = s.id
    WHERE a.start_datetime >= NOW()
    ORDER BY a.start_datetime ASC
    LIMIT 5
");
$upcoming = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="h2 mb-4 text-primary">Admin Dashboard</h1>
            
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Patients</h5>
                            <h2 class="display-4"><?php echo $stats['patients']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Doctors</h5>
                            <h2 class="display-4"><?php echo $stats['doctors']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Today's Appts</h5>
                            <h2 class="display-4"><?php echo $stats['appointments_today']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark h-100">
                        <div class="card-body">
                            <h5 class="card-title">Pending</h5>
                            <h2 class="display-4"><?php echo $stats['pending']; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Upcoming Appointments -->
            <div class="card mb-4">
                <div class="card-header">
                    Upcoming Appointments
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcoming as $appt): ?>
                                <tr>
                                    <td><?php echo formatDateTime($appt['start_datetime']); ?></td>
                                    <td><?php echo h($appt['patient_name']); ?></td>
                                    <td><?php echo h($appt['doctor_name']); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo h($appt['service_name']); ?></span></td>
                                    <td>
                                        <span class="badge bg-<?php echo $appt['status'] == 'confirmed' ? 'success' : ($appt['status'] == 'pending' ? 'warning' : 'secondary'); ?>">
                                            <?php echo ucfirst($appt['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($upcoming)): ?>
                                <tr><td colspan="5" class="text-center">No upcoming appointments.</td></tr>
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
