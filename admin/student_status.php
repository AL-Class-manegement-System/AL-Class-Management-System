<?php
include 'db_con.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    
    $id = $_GET['id'];
    $status = $_GET['status'];

    // Toggle status (1 -> 0 or 0 -> 1)
    $new_status = ($status == 1) ? 0 : 1;

    $sql = "UPDATE students SET status = $new_status WHERE student_id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: student.php?msg=Status Updated Successfully");
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    header("Location: student.php");
}
$conn->close();
?>