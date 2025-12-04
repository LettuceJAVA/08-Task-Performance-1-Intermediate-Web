<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../includes/csrf.php';

requireRole('doctor');

if (!isset($_GET['id'])) {
    header("Location: appointments.php");
    exit;
}

$patientId = $_GET['id'];

// Fetch Patient Info
$stmt = $pdo->prepare("
    SELECT p.*, u.name, u.email 
    FROM patients p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.id = ?
");
$stmt->execute([$patientId]);
$patient = $stmt->fetch();

if (!$patient) {
    die("Patient not found.");
}

// Handle File Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    verify_csrf_token($_POST['csrf_token']);
    
    $file = $_FILES['document'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('doc_', true) . '.' . $ext;
        $targetPath = $uploadDir . $filename;
        
        // Allow only specific types
        $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        if (in_array(strtolower($ext), $allowed)) {
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $stmt = $pdo->prepare("INSERT INTO patient_documents (patient_id, filename, original_name) VALUES (?, ?, ?)");
                $stmt->execute([$patientId, $filename, $file['name']]);
                setFlashMessage('success', 'Document uploaded successfully.');
            } else {
                setFlashMessage('danger', 'Failed to move uploaded file.');
            }
        } else {
            setFlashMessage('danger', 'Invalid file type.');
        }
    } else {
        setFlashMessage('danger', 'Error uploading file.');
    }
    header("Location: patient_view.php?id=" . $patientId);
    exit;
}

// Fetch Documents
$documents = $pdo->prepare("SELECT * FROM patient_documents WHERE patient_id = ? ORDER BY uploaded_at DESC");
$documents->execute([$patientId]);
$docs = $documents->fetchAll();

// Fetch Appointment History
$history = $pdo->prepare("
    SELECT a.*, s.name as service_name, d.user_id as doctor_uid
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    JOIN doctors d ON a.doctor_id = d.id
    WHERE a.patient_id = ?
    ORDER BY a.start_datetime DESC
");
$history->execute([$patientId]);
$appointments = $history->fetchAll();

require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">Patient Details</h1>
                <a href="appointments.php" class="btn btn-outline-secondary">Back to Appointments</a>
            </div>
            
            <?php displayFlashMessage(); ?>
            
            <div class="row">
                <!-- Patient Info -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">Profile</div>
                        <div class="card-body">
                            <h4 class="card-title"><?php echo h($patient['name']); ?></h4>
                            <p class="text-muted mb-1"><i class="bi bi-envelope me-2"></i> <?php echo h($patient['email']); ?></p>
                            <p class="text-muted mb-1"><i class="bi bi-telephone me-2"></i> <?php echo h($patient['phone']); ?></p>
                            <p class="text-muted mb-1"><i class="bi bi-calendar me-2"></i> Born: <?php echo formatDate($patient['dob']); ?></p>
                            <hr>
                            <h6>Address</h6>
                            <p class="small"><?php echo h($patient['address']); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Documents -->
                <div class="col-md-8 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Medical Documents</span>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data" class="mb-4 p-3 bg-light rounded">
                                <?php csrf_field(); ?>
                                <label class="form-label fw-bold">Upload New Document</label>
                                <div class="input-group">
                                    <input type="file" name="document" class="form-control" required>
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                </div>
                                <div class="form-text">Allowed: PDF, JPG, PNG, DOCX</div>
                            </form>
                            
                            <div class="list-group">
                                <?php foreach ($docs as $doc): ?>
                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                                        <a href="../uploads/<?php echo h($doc['filename']); ?>" target="_blank" class="text-decoration-none text-dark">
                                            <?php echo h($doc['original_name']); ?>
                                        </a>
                                    </div>
                                    <small class="text-muted"><?php echo formatDate($doc['uploaded_at']); ?></small>
                                </div>
                                <?php endforeach; ?>
                                <?php if (empty($docs)): ?>
                                    <p class="text-center text-muted my-3">No documents found.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Appointment History -->
            <div class="card">
                <div class="card-header">Appointment History</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $appt): ?>
                                <tr>
                                    <td><?php echo formatDateTime($appt['start_datetime']); ?></td>
                                    <td><?php echo h($appt['service_name']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $appt['status'] == 'completed' ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($appt['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo h($appt['notes']); ?></td>
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
