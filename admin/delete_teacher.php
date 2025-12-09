<?php
// admin/delete_teacher.php - Fixed to use prepared statement for secure deletion.
include 'db_con.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Safely get the ID

    // 1. Delete the image file from the server (Optional but recommended)
    $image_query = "SELECT image FROM teachers WHERE teacher_id = ?";
    $image_stmt = $conn->prepare($image_query);
    
    if ($image_stmt) {
        $image_stmt->bind_param("i", $id);
        $image_stmt->execute();
        $result = $image_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $image_path = "../assets/images/teachers/" . $row['image'];
            if (!empty($row['image']) && file_exists($image_path)) {
                unlink($image_path); // Delete the file
            }
        }
        $image_stmt->close();
    }

    // 2. Delete the record from the Database (FIX: Use Prepared Statement)
    $delete_sql = "DELETE FROM teachers WHERE teacher_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    
    if ($delete_stmt) {
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            header("Location: teachers.php?msg=Teacher deleted successfully");
        } else {
            header("Location: teachers.php?error=Error deleting record: " . $delete_stmt->error);
        }
        $delete_stmt->close();
    } else {
         header("Location: teachers.php?error=Database Prepare Error");
    }
    
    exit();

} else {
    header("Location: teachers.php");
    exit();
}
?>