<?php
// AL-Class-Management-System/admin/includes/auth.php - Authentication Check

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If admin is not logged in, redirect to the login page
if (!isset($_SESSION['is_admin_logged_in']) || $_SESSION['is_admin_logged_in'] !== true) {
    // Redirect to the login page
    header("Location: login.php");
    exit();
}
?>