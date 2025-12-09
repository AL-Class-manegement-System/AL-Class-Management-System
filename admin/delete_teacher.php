<?php
include 'db_con.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // ID එක ආරක්ෂිතව ලබා ගනී

    // 1. කලින් තිබ්බ Image එක Delete කිරීම (Server එකෙන්) - Optional
    $query = "SELECT image FROM teachers WHERE teacher_id = $id";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image_path = "../assets/images/teachers/" . $row['image'];
        if (file_exists($image_path)) {
            unlink($image_path); // ෆයිල් එක මකයි
        }
    }

    // 2. Database එකෙන් Record එක Delete කිරීම
    $sql = "DELETE FROM teachers WHERE teacher_id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: teachers.php?msg=Teacher deleted successfully");
    } else {
        header("Location: teachers.php?error=Error deleting record");
    }
} else {
    header("Location: teachers.php");
}
?>