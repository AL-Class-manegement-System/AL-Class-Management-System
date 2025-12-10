<?php
// admin/delete_timetable.php - Prepared Statement භාවිතයෙන් පන්තියක් මකා දැමීම
session_start();
include('includes/auth.php'); 
include('db_con.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Safely get the ID

   
    $delete_sql = "DELETE FROM classes WHERE class_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    
    if ($delete_stmt) {
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            // සාර්ථක වූ විට timetable.php වෙත හරවා යවයි.
            header("Location: timetable.php?msg=Class deleted successfully!");
        } else {
            header("Location: timetable.php?error=Error deleting record: " . $delete_stmt->error);
        }
        $delete_stmt->close();
    } else {
         header("Location: timetable.php?error=Database Prepare Error: " . $conn->error);
    }
    
    exit();

} else {
    header("Location: timetable.php?error=Invalid class ID provided.");
    exit();
}
?>