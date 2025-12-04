<?php
// Session eka patan aran nathnam patan gannawa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin kenek log wela nathnam Login page ekata yawanna
if (!isset($_SESSION['is_admin_logged_in']) || $_SESSION['is_admin_logged_in'] !== true) {
    // Dan inna thana anuwa path eka hadaganna
    header("Location: login.php");
    exit();
}
?>