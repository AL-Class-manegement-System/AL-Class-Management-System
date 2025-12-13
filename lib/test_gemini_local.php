<?php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['message'] = 'Hello, who are you?';
include 'gemini_chat_handler.php';
?>