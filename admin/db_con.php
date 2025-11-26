<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "al_class_db";

// Connection එක සාදන්න
$conn = new mysqli($servername, $username, $password, $dbname);

// Connection එක check කරන්න
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>