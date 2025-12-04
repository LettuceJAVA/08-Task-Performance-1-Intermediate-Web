<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../includes/csrf.php';

redirectIfLoggedIn();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token']);
    
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $dob = $_POST['dob'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($dob)) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            try {
                $pdo->beginTransaction();
                
                // Create User
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'patient')");
                $stmt->execute([$name, $email, $hash]);
                $user_id = $pdo->lastInsertId();
                
                // Create Patient Profile
                $stmt = $pdo->prepare("INSERT INTO patients (user_id, dob, phone, address) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $dob, $phone, $address]);
                
                $pdo->commit();
                
                setFlashMessage('success', 'Registration successful! Please login.');
                header("Location: login.php");
                exit;
                
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}

$hide_nav = true;
require_once '../includes/header.php';
?>

<div class="auth-container">
    <div class="card auth-card" style="max-width: 500px;">
        <div class="card-body">
            <div class="text-center mb-4">
                <h3 class="text-primary fw-bold">Create Account</h3>
                <p class="text-muted">Register as a new patient</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo h($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <?php csrf_field(); ?>
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name *</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo h($_POST['name'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo h($_POST['email'] ?? ''); ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password *</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="dob" class="form-label">Date of Birth *</label>
                    <input type="date" class="form-control" id="dob" name="dob" value="<?php echo h($_POST['dob'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo h($_POST['phone'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="2"><?php echo h($_POST['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Register</button>
                </div>
            </form>
            
            <div class="mt-3 text-center">
                <p class="small">Already have an account? <a href="login.php">Login here</a></p>
                <p class="small"><a href="../index.php">Back to Home</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
