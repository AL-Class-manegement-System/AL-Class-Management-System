<?php
// student_portal/pages/get_payhere_hash.php
session_start();
include('../includes/connection.php');

header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

if (isset($_POST['class_id'])) {
    $class_id = intval($_POST['class_id']);
    $student_id = $_SESSION['student_id'];

    // 1. Get Class Details
    $class_sql = "SELECT * FROM classes WHERE class_id = ?";
    $stmt = $conn->prepare($class_sql);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $class_result = $stmt->get_result();
    $class = $class_result->fetch_assoc();

    // 2. Get Student Details
    $student_sql = "SELECT * FROM students WHERE student_id = ?";
    $stmt2 = $conn->prepare($student_sql);
    $stmt2->bind_param("i", $student_id);
    $stmt2->execute();
    $student_result = $stmt2->get_result();
    $student = $student_result->fetch_assoc();

    if ($class && $student) {
        // --- PAYHERE CREDENTIALS (SANDBOX) ---
        $merchant_id = "1225260";
        $merchant_secret = "NzY0Nzg1Mjc0MTgzMTY4Mjg5MjE4ODcyNjMyMzYzMjA1MTcwOTY4";
        $currency = "LKR";

        $amount = number_format($class['fee'], 2, '.', '');

        // Unique Order ID: ORD_StudentID_ClassID_Timestamp
        $order_id = "ORD_" . $student_id . "_" . $class_id . "_" . time();

        // Generate Hash
        $hash = strtoupper(
            md5(
                $merchant_id .
                $order_id .
                $amount .
                $currency .
                strtoupper(md5($merchant_secret))
            )
        );

        // Payment Data Array
        $payment_data = [
            "sandbox" => true,
            "merchant_id" => $merchant_id,
            "return_url" => "http://localhost/AL-Class-Management-System/student_portal/pages/payment_success.php",
            "cancel_url" => "http://localhost/AL-Class-Management-System/student_portal/pages/enroll_class.php?class_id=" . $class_id,
            "notify_url" => "http://localhost/AL-Class-Management-System/student_portal/pages/payment_notify.php",
            "order_id" => $order_id,
            "items" => $class['subject'] . " - " . $class['class_name'],
            "amount" => $amount,
            "currency" => $currency,
            "hash" => $hash,
            "first_name" => $student['first_name'],
            "last_name" => $student['last_name'],
            "email" => $student['email'],
            "phone" => $student['phone'],
            "address" => "No.1, Galle Road",
            "city" => "Colombo",
            "country" => "Sri Lanka"
        ];

        echo json_encode($payment_data);
    } else {
        echo json_encode(['error' => 'Class or Student not found']);
    }
}
?>