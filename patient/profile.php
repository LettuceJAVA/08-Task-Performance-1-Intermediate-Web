<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../includes/csrf.php';

requireRole('patient');
$userId = getCurrentUserId();
$patientId = $pdo->query("SELECT id FROM patients WHERE user_id = $userId")->fetchColumn();

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    verify_csrf_token($_POST['csrf_token']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    $stmt = $pdo->prepare("UPDATE patients SET phone = ?, address = ? WHERE user_id = ?");
    $stmt->execute([$phone, $address, $userId]);
    setFlashMessage('success', 'Profile updated.');
    header("Location: profile.php");
    exit;
}

$patient = $pdo->query("SELECT * FROM patients WHERE user_id = $userId")->fetch();
$user = $pdo->query("SELECT * FROM users WHERE id = $userId")->fetch();
$documents = $pdo->query("SELECT * FROM patient_documents WHERE patient_id = $patientId ORDER BY uploaded_at DESC")->fetchAll();

require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="h2 mb-4">My Profile</h1>
            <?php displayFlashMessage(); ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">Personal Information</div>
                        <div class="card-body">
                            <form method="POST">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="update_profile" value="1">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" value="<?php echo h($user['name']); ?>" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="<?php echo h($user['email']); ?>" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="text" class="form-control" value="<?php echo h($patient['dob']); ?>" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" value="<?php echo h($patient['phone']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" class="form-control" rows="3"><?php echo h($patient['address']); ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Medical Documents</div>
                        <div class="card-body">
                            <h6 class="mb-3">My Documents</h6>
                            <?php if (empty($documents)): ?>
                                <p class="text-muted small">No documents available.</p>
                            <?php else: ?>

                                <ul class="list-group list-group-flush">
                                    <?php foreach ($documents as $doc): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-file-earmark-text me-2"></i>
                                            <a href="../uploads/<?php echo h($doc['filename']); ?>" target="_blank" class="text-decoration-none">
                                                <?php echo h($doc['original_name']); ?>
                                            </a>
                                        </div>
                                        <small class="text-muted"><?php echo formatDate($doc['uploaded_at']); ?></small>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
