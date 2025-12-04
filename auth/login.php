<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../includes/csrf.php';

redirectIfLoggedIn();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token']);
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            
            header("Location: /hospital-scheduler/{$user['role']}/dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}

$hide_nav = true; // Hide main nav on login page
require_once '../includes/header.php';
?>

<div class="auth-container">
    <div class="card auth-card">
        <div class="card-body">
            <div class="text-center mb-4">
                <h3 class="text-primary fw-bold"><i class="bi bi-hospital-fill"></i> Welcome Back</h3>
                <p class="text-muted">Login to your account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo h($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <?php csrf_field(); ?>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
            
            <div class="mt-3 text-center">
                <p class="small">Don't have an account? <a href="register.php">Register as Patient</a></p>
                <p class="small"><a href="../index.php">Back to Home</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
