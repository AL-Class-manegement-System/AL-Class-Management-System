<?php
session_start();
require_once '../includes/connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. Data ලබා ගැනීම සහ පිරිසිදු කිරීම
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 2. VALIDATION SECTION
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Please fill all required fields';
        header("Location: ../log/login.php");
        exit();
    }

    // 3. User Authentication
    $query_st = "SELECT * FROM students WHERE (reg_number = ? OR email = ?)";
    $stmt_st = $conn->prepare($query_st);
    $stmt_st->bind_param("ss", $username, $username);
    $stmt_st->execute();
    $result_st = $stmt_st->get_result();

    // User කෙනෙක් සිටීදැයි පරීක්ෂා කිරීම
    if ($result_st->num_rows === 1) {
        $student = $result_st->fetch_assoc();

        // A. Status Active (1) ද කියා බැලීම
        if ($student['status'] == 1) {

            // B. Password (NIC) නිවැරදිදැයි බැලීම
            if ($password == $student['nic']) {

                // Successful login
                $_SESSION['student_id'] = $student['reg_number'];
                $_SESSION['full_name'] = $student['full_name'];
                $_SESSION['profile_pic'] = $student['photo'];

                header("Location: ../student_portal");
                exit();

            } else {
                // Password වැරදි නම්
                $_SESSION['error'] = 'Invalid username or password';
                header("Location: ../log/login.php");
                exit();
            }

        } else {
            // Status එක Inactive (0) නම්
            $_SESSION['error'] = 'Your account is deactivated. Please contact the office.';
            header("Location: ../log/login.php");
            exit();
        }

    } else {
        // User කෙනෙක් සොයාගත නොහැකි නම් (Username වැරදි නම්)
        $_SESSION['error'] = 'Invalid username or password';
        header("Location: ../log/login.php");
        exit();
    }

} else {
    // POST Request එකක් නොවේ නම්
    header("Location: ../log/login.php");
    exit();
}
?>