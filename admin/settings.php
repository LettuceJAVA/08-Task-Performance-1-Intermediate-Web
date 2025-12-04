<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

requireRole('admin');

require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="h2 mb-4">Settings</h1>
            <div class="alert alert-info">Settings configuration is coming soon.</div>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
