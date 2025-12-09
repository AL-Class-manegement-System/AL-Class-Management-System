<?php
session_start();
require_once '../includes/connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']); // Student Reg No හෝ Teacher ID
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Please fill all required fields';
        header("Location: ../log/login.php");
        exit();
    }

    // ==========================================
    // 1. STUDENT LOGIN CHECK
    // ==========================================
    $query_st = "SELECT * FROM students WHERE reg_number = ?";
    $stmt_st = $conn->prepare($query_st);
    $stmt_st->bind_param("s", $username);
    $stmt_st->execute();
    $result_st = $stmt_st->get_result();

    if ($result_st->num_rows === 1) {
        $student = $result_st->fetch_assoc();

        // Student Password Check (ඔබේ ක්‍රමය අනුව NIC එක Password ලෙස)
        if ($student['status'] == 1 && $password == $student['nic']) {
            
            $_SESSION['user_type'] = 'student';
            $_SESSION['student_id'] = $student['reg_number'];
            $_SESSION['full_name'] = $student['full_name'];
            $_SESSION['profile_pic'] = $student['photo'];

            header("Location: ../student_portal"); // Student Portal එකට
            exit();
        }
    }

    // ==========================================
    // 2. TEACHER LOGIN CHECK (Student නොවේ නම් පමණක් මෙය බලයි)
    // ==========================================
    $query_tc = "SELECT * FROM teachers WHERE teacher_number = ?";
    $stmt_tc = $conn->prepare($query_tc);
    $stmt_tc->bind_param("s", $username);
    $stmt_tc->execute();
    $result_tc = $stmt_tc->get_result();

    if ($result_tc->num_rows === 1) {
        $teacher = $result_tc->fetch_assoc();

        // Teacher Password Check
        if ($teacher['status'] == 1 && $password == $teacher['password']) {
            
            $_SESSION['user_type'] = 'teacher';
            $_SESSION['teacher_id'] = $teacher['teacher_number']; // ID එක (Ex: TC2025001)
            $_SESSION['teacher_db_id'] = $teacher['teacher_id']; // DB ID (Ex: 1)
            $_SESSION['full_name'] = $teacher['full_name'];
            $_SESSION['profile_pic'] = $teacher['image'];

            header("Location: ../teacher_portal/pages/index.php"); // Teacher Portal එකට
            exit();
        }
    }

    // ==========================================
    // LOGIN FAILED
    // ==========================================
    $_SESSION['error'] = 'Invalid Username or Password';
    header("Location: ../log/login.php");
    exit();

} else {
    header("Location: ../log/login.php");
    exit();
}
?>