<?php
// includes/auth.php
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user role
function getCurrentRole() {
    return $_SESSION['role'] ?? null;
}

// Get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Middleware to require login
function requireLogin() {
    // Prevent caching
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    if (!isLoggedIn()) {
        header("Location: /hospital-scheduler/auth/login.php");
        exit;
    }
}

// Middleware to require specific role
function requireRole($role) {
    requireLogin();
    if (getCurrentRole() !== $role) {
        // Redirect to their appropriate dashboard if they are logged in but wrong role
        $userRole = getCurrentRole();
        header("Location: /hospital-scheduler/$userRole/dashboard.php");
        exit;
    }
}

// Redirect if already logged in (for login/register pages)
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        $role = getCurrentRole();
        header("Location: /hospital-scheduler/$role/dashboard.php");
        exit;
    }
}
