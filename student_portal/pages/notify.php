<?php
// student_portal/pages/notify.php
include('../../includes/connection.php');

$merchant_id = $_POST['merchant_id'];
$order_id = $_POST['order_id'];
$payhere_amount = $_POST['payhere_amount'];
$payhere_currency = $_POST['payhere_currency'];
$status_code = $_POST['status_code'];
$md5sig = $_POST['md5sig'];

// මෙතන අර කලින් දාපු Secret එකම දාන්න
$merchant_secret = "MTU3NDk1MTUxMjMyODExNzgyODcxOTkyOTQ3NTUzMzE0OTUwMDczNw==";

$local_md5sig = strtoupper(md5($merchant_id . $order_id . $payhere_amount . $payhere_currency . $status_code . strtoupper(md5($merchant_secret))));

if (($local_md5sig === $md5sig) && ($status_code == 2)) {

    // 1. Payment Status එක Paid කිරීම
    $stmt = $conn->prepare("UPDATE payments SET payment_status = 'paid' WHERE transaction_id = ?");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();

    // 2. Class Enroll කිරීම
    $result = $conn->query("SELECT * FROM payments WHERE transaction_id = '$order_id'");
    if ($result->num_rows > 0) {
        $payment = $result->fetch_assoc();
        $student_id = $payment['student_id'];
        $class_id = $payment['class_id'];

        // දැනටමත් Enroll වී ඇත්දැයි බැලීම
        $check_enroll = $conn->query("SELECT * FROM enrollments WHERE student_id = $student_id AND class_id = $class_id");

        if ($check_enroll->num_rows == 0) {
            $enroll_sql = "INSERT INTO enrollments (student_id, class_id, status, joined_date, payment_method) VALUES (?, ?, 1, NOW(), 'Online')";
            $stmt2 = $conn->prepare($enroll_sql);
            $stmt2->bind_param("ii", $student_id, $class_id);
            $stmt2->execute();
        } else {
            $conn->query("UPDATE enrollments SET status = 1 WHERE student_id = $student_id AND class_id = $class_id");
        }
    }
}
?>