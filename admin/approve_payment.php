<?php
// admin/approve_payment.php
session_start();
include('db_con.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['pay_id'])) {
    $payment_id = intval($_GET['pay_id']);

    // 1. Get Payment Details
    $sql = "SELECT * FROM payments WHERE payment_id = $payment_id";
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
        $pay_data = $res->fetch_assoc();
        $student_id = $pay_data['student_id'];
        $class_id = $pay_data['class_id'];

        // 2. Update Payment Status to 'paid' (if it was pending)
        $conn->query("UPDATE payments SET payment_status = 'paid' WHERE payment_id = $payment_id");

        // 3. ENROLL STUDENT
        // Check duplicates first
        $chk = $conn->query("SELECT * FROM enrollments WHERE student_id = $student_id AND class_id = $class_id");

        if ($chk->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO enrollments (student_id, class_id, status, joined_date) VALUES (?, ?, 1, NOW())");
            $stmt->bind_param("ii", $student_id, $class_id);

            if ($stmt->execute()) {
                echo "<script>alert('Student Approved & Enrolled Successfully!'); window.location.href='payments.php';</script>";
            } else {
                echo "<script>alert('Error enrolling student.'); window.location.href='payments.php';</script>";
            }
        } else {
            echo "<script>alert('Student is already enrolled.'); window.location.href='payments.php';</script>";
        }

    } else {
        echo "<script>alert('Invalid Payment ID'); window.location.href='payments.php';</script>";
    }
}
?>