<?php
require_once 'includes/db.php';

$password = 'Admin123!';
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ?");
    $stmt->execute([$hash]);
    
    echo "<h1>Success</h1>";
    echo "<p>All user passwords have been reset to: <strong>$password</strong></p>";
    echo "<p>You can now <a href='auth/login.php'>Login here</a>.</p>";
    
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
