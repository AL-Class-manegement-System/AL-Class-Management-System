<?php
     $dbserver="localhost";
     $dbuser="root";
     $password="";
    $database="web_project2";

    $con=mysqli_connect( $dbserver,  $dbuser,$password,$database);
     if(!$con){
     die("Error ".mysqli_connect_error());
     }
   
?>