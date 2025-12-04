<?php
// includes/csrf.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF Token
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Output CSRF Input Field
function csrf_field() {
    $token = csrf_token();
    echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

// Verify CSRF Token
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        die('CSRF validation failed.');
    }
    return true;
}
