<?php
session_start();
require_once '../includes/connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. Data ලබා ගැනීම සහ පිරිසිදු කිරීම
    $username =  trim($_POST['username']) ;
    $password =  trim($_POST['password']) ;

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

    if ($result_st->num_rows === 1) {
        $student = $result_st->fetch_assoc();


        if ($password == $student['nic']) {
            // Successful login
            $_SESSION['student_id'] = $student['reg_number'];
            $_SESSION['full_name'] = $student['full_name'];
            header("Location: ../pages");
            exit();
        } else {
            // Invalid password
            $_SESSION['error'] = 'Invalid username or password';
            header("Location: ../log/login.php" );
            exit();
        }
    } else {
        // User not found
        $_SESSION['error'] = 'Invalid username or password';
        header("Location: ../log/login.php");
        exit();
    }

}
?>