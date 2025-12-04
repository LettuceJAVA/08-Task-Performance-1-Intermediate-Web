<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../includes/csrf.php';

requireRole('doctor');
$userId = getCurrentUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token']);
    $specialty = trim($_POST['specialty']);
    $bio = trim($_POST['bio']);
    
    $stmt = $pdo->prepare("UPDATE doctors SET specialty = ?, bio = ? WHERE user_id = ?");
    $stmt->execute([$specialty, $bio, $userId]);
    setFlashMessage('success', 'Profile updated.');
    header("Location: profile.php");
    exit;
}

$doctor = $pdo->query("SELECT * FROM doctors WHERE user_id = $userId")->fetch();
$user = $pdo->query("SELECT * FROM users WHERE id = $userId")->fetch();

require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="h2 mb-4">My Profile</h1>
            <?php displayFlashMessage(); ?>
            
            <div class="card" style="max-width: 600px;">
                <div class="card-body">
                    <form method="POST">
                        <?php csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" value="<?php echo h($user['name']); ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?php echo h($user['email']); ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Specialty</label>
                            <input type="text" name="specialty" class="form-control" value="<?php echo h($doctor['specialty']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bio</label>
                            <textarea name="bio" class="form-control" rows="4"><?php echo h($doctor['bio']); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
