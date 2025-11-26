<?php
     $dbserver="localhost";
     $dbuser="root";
     $password="";
    $database="al_class_db";

    $con=mysqli_connect( $dbserver,  $dbuser,$password,$database);
     if(!$con){
     die("Error ".mysqli_connect_error());
     }
   
?>