<?php
// student_portal/pages/get_payhere_hash.php
error_reporting(E_ALL);
ini_set('display_errors', 0);

session_start();
include('../../includes/connection.php');

header('Content-Type: application/json');

// 1. Session Check
if (!isset($_SESSION['student_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

if (isset($_POST['class_id'])) {
    $class_id = intval($_POST['class_id']);
    // Session එකේ ඇත්තේ Reg Number එකයි (STxxxx)
    $student_reg_no = $_SESSION['student_id']; 

    if (!$conn) {
        echo json_encode(['error' => 'Database connection failed.']);
        exit;
    }

    // 2. දත්ත ලබා ගැනීම
    $class_query = $conn->query("SELECT * FROM classes WHERE class_id = $class_id");
    $student_query = $conn->query("SELECT * FROM students WHERE reg_number = '$student_reg_no'");

    if (!$class_query || !$student_query) {
        echo json_encode(['error' => 'Database Query Failed']);
        exit;
    }

    $class = $class_query->fetch_assoc();
    $student = $student_query->fetch_assoc();

    if ($class && $student) {
        
        $student_db_id = intval($student['student_id']);

        // ==========================================
        // PAYHERE SANDBOX CREDENTIALS (TESTING ONLY)
        // ==========================================
        // මෙය වෙනස් කරන්න එපා. මෙය PayHere Testing සදහා පොදු එකකි.
        $merchant_id = "1231876"; 
        $merchant_secret = "NTI0NDUwNTk1MTAzMzQ0ODY1NDQ3ODAxOTQxOTI0MDQwNDUxODU="; 
        
        $currency = "LKR";
        $amount = number_format($class['fee'], 2, '.', ''); 
        $order_id = "ORD_" . $student_db_id . "_" . $class_id . "_" . time();
        
        // Hash Generation
        $hash_str = $merchant_id . $order_id . $amount . $currency . strtoupper(md5($merchant_secret));
        $hash = strtoupper(md5($hash_str));

        // Insert Pending Payment
        $month = date('F');
        $year = date('Y');
        
        $stmt = $conn->prepare("INSERT INTO payments (student_id, class_id, month, year, amount, payment_status, method, transaction_id, payment_type, paid_date) VALUES (?, ?, ?, ?, ?, 'pending', 'Online', ?, 'Full', NOW())");
        $stmt->bind_param("iissss", $student_db_id, $class_id, $month, $year, $amount, $order_id);

        if ($stmt->execute()) {
            // Domain Configuration
            $base_url = "http://localhost/AL-Class-Management-System-main/student_portal/pages";

            echo json_encode([
                "sandbox" => true,
                "merchant_id" => $merchant_id,
                "return_url" => $base_url . "/payment_success.php?order_id=" . $order_id,
                "cancel_url" => $base_url . "/enroll_class.php?class_id=" . $class_id,
                "notify_url" => $base_url . "/notify.php", 
                "order_id" => $order_id,
                "items" => $class['subject'],
                "amount" => $amount,
                "currency" => $currency,
                "hash" => $hash,
                "first_name" => $student['full_name'],
                "last_name" => "Student",
                "email" => $student['email'],
                "phone" => !empty($student['student_phone']) ? $student['student_phone'] : "0777123456",
                "address" => "Colombo",
                "city" => "Colombo",
                "country" => "Sri Lanka"
            ]);
        } else {
            echo json_encode(['error' => 'Insert Failed: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['error' => 'Class or Student Data Not Found.']);
    }
} else {
    echo json_encode(['error' => 'Invalid Request']);
}
?>