<?php
    // $dbserver = "localhost";
    // $dbuser = "root";
    // $password = "";
    // $database = "al_class_db";

    // $conn = mysqli_connect($dbserver, $dbuser, $password, $database);
    
    // if(!$conn){
    //     die("Error " . mysqli_connect_error());
    // }
?>

<?php
$servername = "futuremindssite.site";
$username = "futuremi"; 
$password = "IQ4-ij.Sim33J2"; 
$dbname = "futuremi_al_class_db"; 

$conn = new mysqli($servername, $username, $password, $dbname);

$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>