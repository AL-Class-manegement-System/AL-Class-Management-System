<?php
session_start();
session_unset();
session_destroy();

// Login page ekata redirect karanawa
header("Location: login.php");
exit();
?>