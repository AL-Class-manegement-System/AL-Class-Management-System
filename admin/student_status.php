<?php
// admin/student_status.php - Fixed to use prepared statement.
include 'db_con.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    
    $id = intval($_GET['id']);
    $status = intval($_GET['status']);

    // Toggle status (1 -> 0 or 0 -> 1)
    $new_status = ($status == 1) ? 0 : 1;

    // FIX: Use Prepared Statement for secure update
    $stmt = $conn->prepare("UPDATE students SET status = ? WHERE student_id = ?");
    
    if ($stmt) {
        $stmt->bind_param("ii", $new_status, $id);
        
        if ($stmt->execute()) {
            header("Location: student.php?msg=Status Updated Successfully");
        } else {
            header("Location: student.php?error=Error updating status: " . $stmt->error);
        }
        $stmt->close();
    } else {
        header("Location: student.php?error=Database Prepare Error");
    }
    
    exit(); // Always exit after redirect
} else {
    header("Location: student.php");
    exit();
}
$conn->close();
?>