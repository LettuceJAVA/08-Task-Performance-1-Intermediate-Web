<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../includes/csrf.php';

requireRole('admin');

$success = '';
$error = '';

// Handle Create Doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_doctor'])) {
    verify_csrf_token($_POST['csrf_token']);
    
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $specialty = trim($_POST['specialty']);
    $bio = trim($_POST['bio']);
    
    try {
        $pdo->beginTransaction();
        
        // Create User
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'doctor')");
        $stmt->execute([$name, $email, $hash]);
        $user_id = $pdo->lastInsertId();
        
        // Create Doctor Profile
        $stmt = $pdo->prepare("INSERT INTO doctors (user_id, specialty, bio) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $specialty, $bio]);
        
        $pdo->commit();
        $success = "Doctor created successfully.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error creating doctor: " . $e->getMessage();
    }
}

// Fetch Doctors
$doctors = $pdo->query("
    SELECT d.*, u.name, u.email 
    FROM doctors d 
    JOIN users u ON d.user_id = u.id 
    ORDER BY u.name
")->fetchAll();

require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="h2 mb-4">Manage Doctors</h1>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo h($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo h($error); ?></div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">Add New Doctor</div>
                        <div class="card-body">
                            <form method="POST">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="create_doctor" value="1">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Specialty</label>
                                    <input type="text" name="specialty" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Bio</label>
                                    <textarea name="bio" class="form-control" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Create Doctor</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Doctor List</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Specialty</th>
                                            <th>Email</th>
                                            <th>Joined</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($doctors as $doc): ?>
                                        <tr>
                                            <td><?php echo h($doc['name']); ?></td>
                                            <td><?php echo h($doc['specialty']); ?></td>
                                            <td><?php echo h($doc['email']); ?></td>
                                            <td><?php echo formatDate($doc['created_at']); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
