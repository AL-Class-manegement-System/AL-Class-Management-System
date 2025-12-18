<?php
// admin/approve_payment.php
session_start();
include('db_con.php'); // ඔබේ database connection file එක

// Admin login වී ඇත්දැයි පරීක්ෂා කිරීම (අවශ්‍ය නම් comment ඉවත් කරන්න)
// if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

if (isset($_GET['pay_id'])) {
    $payment_id = intval($_GET['pay_id']);

    // 1. Payment විස්තර ලබා ගැනීම
    $sql = "SELECT * FROM payments WHERE payment_id = $payment_id";
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
        $pay_data = $res->fetch_assoc();
        $student_id = $pay_data['student_id'];
        $class_id = $pay_data['class_id'];
        
        // 2. Payment Status එක 'paid' ලෙස update කිරීම
        $update_pay = $conn->query("UPDATE payments SET payment_status = 'paid' WHERE payment_id = $payment_id");

        if ($update_pay) {
            // 3. ශිෂ්‍යයාව පන්තියට Enroll කිරීම (Enrollments Table එකට දැමීම)
            // මුලින්ම ශිෂ්‍යයා දැනටමත් enroll වී ඇත්දැයි බලන්න
            $check_enroll = $conn->query("SELECT * FROM enrollments WHERE student_id = $student_id AND class_id = $class_id");

            if ($check_enroll->num_rows == 0) {
                // Enrollment එක නොමැති නම් අලුතින් ඇතුළත් කරන්න (INSERT)
                // සටහන: ඔබේ enrollments table එකේ 'slip_image' වැනි columns නැත්නම් ඒවා මෙම query එකෙන් ඉවත් කරන්න.
                // ආරක්ෂිතව මූලික විස්තර පමණක් ඇතුළත් කරමු:
                $stmt = $conn->prepare("INSERT INTO enrollments (student_id, class_id, status, joined_date) VALUES (?, ?, 1, NOW())");
                $stmt->bind_param("ii", $student_id, $class_id);
                
                if ($stmt->execute()) {
                    echo "<script>alert('Payment Approved & Student Enrolled Successfully!'); window.location.href='payments.php';</script>";
                } else {
                    echo "<script>alert('Payment marked as Paid, BUT Enrollment Failed! Error: " . $conn->error . "'); window.location.href='payments.php';</script>";
                }
            } else {
                // දැනටමත් enroll වී ඇත්නම් status එක 1 කරන්න (Active)
                $conn->query("UPDATE enrollments SET status = 1 WHERE student_id = $student_id AND class_id = $class_id");
                echo "<script>alert('Payment Approved & Student Re-Activated!'); window.location.href='payments.php';</script>";
            }

        } else {
            echo "<script>alert('Failed to update payment status.'); window.location.href='payments.php';</script>";
        }

    } else {
        echo "<script>alert('Invalid Payment ID'); window.location.href='payments.php';</script>";
    }
} else {
    header("Location: payments.php");
}
?>