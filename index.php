<?php
require_once 'includes/auth.php';
// If logged in, redirect to dashboard
redirectIfLoggedIn();
require_once 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="display-4 fw-bold text-primary">Modern Healthcare Scheduling</h1>
            <p class="lead text-muted">Manage appointments, doctors, and patients with ease. A streamlined experience for modern hospitals.</p>
            <div class="d-grid gap-2 d-md-block">
                <a href="auth/register.php" class="btn btn-primary btn-lg px-4 gap-3">Get Started</a>
                <a href="auth/login.php" class="btn btn-outline-secondary btn-lg px-4">Login</a>
            </div>
        </div>
        <div class="col-md-6 text-center">
            <img src="assets/img/hospital-illustration.png" alt="Hospital Illustration" class="img-fluid rounded-3 shadow-lg">
        </div>
    </div>
    
    <div class="row mt-5 pt-5">
        <div class="col-md-4 mb-4">
            <div class="card h-100 p-4 text-center">
                <i class="bi bi-calendar-check display-4 text-primary mb-3"></i>
                <h3>Easy Booking</h3>
                <p class="text-muted">Patients can book appointments online in seconds.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 p-4 text-center">
                <i class="bi bi-people display-4 text-primary mb-3"></i>
                <h3>Doctor Profiles</h3>
                <p class="text-muted">View doctor specialties and availability.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 p-4 text-center">
                <i class="bi bi-shield-check display-4 text-primary mb-3"></i>
                <h3>Secure Records</h3>
                <p class="text-muted">Your medical data is safe and secure.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
