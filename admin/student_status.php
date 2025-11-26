<?php
include 'db_con.php';

// ID සහ Status එක ලැබුනු බව තහවුරු කරගැනීම
if (isset($_GET['id']) && isset($_GET['status'])) {
    
    $id = $_GET['id'];
    $status = $_GET['status'];

    // අලුත් Status එක තීරණය කිරීම (1 නම් 0 කරන්න, 0 නම් 1 කරන්න)
    $new_status = ($status == 1) ? 0 : 1;

    // Database එක update කිරීම
    $sql = "UPDATE students SET status = $new_status WHERE student_id = $id";

    if ($conn->query($sql) === TRUE) {
        // සාර්ථක නම් නැවත students page එකට යවන්න
        header("Location: students.php?msg=Status Updated Successfully");
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    // වැරදි විදියට ආවොත් ආපසු යවන්න
    header("Location: students.php");
}

$conn->close();
?>