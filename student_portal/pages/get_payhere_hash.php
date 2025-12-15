<?php
// student_portal/pages/get_payhere_hash.php
session_start();
include('../../includes/connection.php');

header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

if (isset($_POST['class_id'])) {
    $class_id = intval($_POST['class_id']);
    $student_id = $_SESSION['student_id'];

    $class = $conn->query("SELECT * FROM classes WHERE class_id = $class_id")->fetch_assoc();
    $student = $conn->query("SELECT * FROM students WHERE student_id = $student_id")->fetch_assoc();

    if ($class && $student) {
        $merchant_id = "1225260"; // Replace with YOUR ID
        $merchant_secret = "NzY0Nzg1Mjc0MTgzMTY4Mjg5MjE4ODcyNjMyMzYzMjA1MTcwOTY4"; // Replace with YOUR Secret
        $currency = "LKR";
        $amount = number_format($class['fee'], 2, '.', '');
        $order_id = "ORD_" . $student_id . "_" . $class_id . "_" . time();

        $hash = strtoupper(md5($merchant_id . $order_id . $amount . $currency . strtoupper(md5($merchant_secret))));

        echo json_encode([
            "sandbox" => true,
            "merchant_id" => $merchant_id,
            "return_url" => "http://localhost/AL-Class-Management-System/student_portal/pages/payment_success.php",
            "cancel_url" => "http://localhost/AL-Class-Management-System/student_portal/pages/enroll_class.php?class_id=" . $class_id,
            "notify_url" => "http://yourdomain.com/notify.php",
            "order_id" => $order_id,
            "items" => $class['subject'],
            "amount" => $amount,
            "currency" => $currency,
            "hash" => $hash,
            "first_name" => $student['full_name'],
            "last_name" => "",
            "email" => $student['email'],
            "phone" => $student['student_phone'],
            "address" => "Colombo",
            "city" => "Colombo",
            "country" => "Sri Lanka"
        ]);
    } else {
        echo json_encode(['error' => 'Class/Student not found']);
    }
}
?>