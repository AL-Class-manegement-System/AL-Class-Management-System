<?php
    $dbserver = "localhost";
    $dbuser = "root";
    $password = "";
    $database = "al_class_db";

    $conn = mysqli_connect($dbserver, $dbuser, $password, $database);
    
    if(!$conn){
        die("Error " . mysqli_connect_error());
    }
?>