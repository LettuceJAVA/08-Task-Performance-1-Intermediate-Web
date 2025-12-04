<?php
// includes/helpers.php

// Sanitize output
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Format date
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Format time
function formatTime($time) {
    return date('h:i A', strtotime($time));
}

// Format datetime
function formatDateTime($datetime) {
    return date('M d, Y h:i A', strtotime($datetime));
}

// Stub for email sending
function send_email_stub($to, $subject, $body) {
    // In a real application, integrate PHPMailer here.
    // For now, we'll just log it to a file or do nothing.
    // error_log("Email to $to: $subject - $body");
    return true;
}

// Flash messages
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function displayFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type']; // success, danger, warning, info
        $message = $_SESSION['flash']['message'];
        echo "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                {$message}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
        unset($_SESSION['flash']);
    }
}
