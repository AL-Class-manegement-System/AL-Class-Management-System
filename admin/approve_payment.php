<?php
// admin/approve_payment.php
session_start();
include('db_con.php');

if (!isset($_SESSION['admin_id'])) { // admin session variable එක ඔබේ පද්ධතියට ගැලපෙන පරිදි වෙනස් කරගන්න
    // header("Location: login.php"); // Uncomment if needed
}

if (isset($_GET['pay_id'])) {
    $payment_id = intval($_GET['pay_id']);

    // 1. Payment විස්තර ලබා ගැනීම
    $sql = "SELECT * FROM payments WHERE payment_id = $payment_id";
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
        $pay_data = $res->fetch_assoc();
        $student_id = $pay_data['student_id'];
        $class_id = $pay_data['class_id'];
        $slip_image = $pay_data['slip_image'];

        // 2. Payment Status එක 'paid' ලෙස වෙනස් කිරීම
        $conn->query("UPDATE payments SET payment_status = 'paid' WHERE payment_id = $payment_id");

        // 3. Enroll කිරීම (Enrollments Table එකට ඇතුළත් කිරීම)
        $check_enroll = $conn->query("SELECT * FROM enrollments WHERE student_id = $student_id AND class_id = $class_id");

        if ($check_enroll->num_rows == 0) {
            // අලුත් Enrollment එකක්
            $stmt = $conn->prepare("INSERT INTO enrollments (student_id, class_id, status, joined_date, slip_image, payment_method) VALUES (?, ?, 1, NOW(), ?, 'Slip')");
            $stmt->bind_param("iis", $student_id, $class_id, $slip_image);
            $stmt->execute();
        } else {
            // දැනටමත් තිබේ නම් Active කිරීම
            $conn->query("UPDATE enrollments SET status = 1 WHERE student_id = $student_id AND class_id = $class_id");
        }

        echo "<script>alert('Payment Approved & Student Enrolled Successfully!'); window.location.href='payments.php';</script>";

    } else {
        echo "<script>alert('Invalid Payment ID'); window.location.href='payments.php';</script>";
    }
}
?>