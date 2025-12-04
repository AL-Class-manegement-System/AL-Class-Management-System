<?php
session_start();
session_unset();
session_destroy();

// Login Page එකට යවන්න
header("Location: login.php");
exit();
?>