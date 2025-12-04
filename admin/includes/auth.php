<?php
// Session එකක් පටන් ගෙන නැත්නම් පටන් ගන්න
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin කෙනෙක් Log වෙලා නැත්නම් Login Page එකට එලවන්න
if (!isset($_SESSION['is_admin_logged_in']) || $_SESSION['is_admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
?>