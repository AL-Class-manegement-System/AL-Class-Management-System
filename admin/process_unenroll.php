<?php
// admin/process_unenroll.php
include 'db_con.php';

// පරීක්ෂා කිරීම
if(isset($_GET['enroll_id']) && isset($_GET['sid'])) {
    
    $enroll_id = intval($_GET['enroll_id']);
    $student_id = intval($_GET['sid']);
    
    // Status 2 (Unenrolled) ලෙස Update කිරීම
    // Delete කරන්නේ නැත.
    $sql = "UPDATE enrollments SET status = 2 WHERE enrollment_id = ?";
    
    $stmt = $conn->prepare($sql);
    
    if($stmt) {
        $stmt->bind_param("i", $enroll_id);
        
        if($stmt->execute()) {
            // සාර්ථක නම් නැවත History පිටුවට යැවීම
            header("Location: student_enrollments.php?student_id=$student_id&msg=Student unenrolled successfully (Status updated to Unenrolled).");
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
    
} else {
    // දත්ත නොමැති නම් ආපසු යැවීම
    header("Location: student.php");
    exit();
}
?>