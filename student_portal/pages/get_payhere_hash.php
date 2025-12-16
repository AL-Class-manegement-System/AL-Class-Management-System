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

    // පන්තියේ සහ ශිෂ්‍යයාගේ විස්තර ලබා ගැනීම
    $class = $conn->query("SELECT * FROM classes WHERE class_id = $class_id")->fetch_assoc();
    $student = $conn->query("SELECT * FROM students WHERE student_id = $student_id")->fetch_assoc();

    if ($class && $student) {
        $merchant_id = "1231876";  // ඔබේ Sandbox Merchant ID එක මෙතැනට දමන්න
        $merchant_secret = "MTU3NDk1MTUxMjMyODExNzgyODcxOTkyOTQ3NTUzMzE0OTUwMDczNw=="; // ඔබේ Sandbox Secret එක

        $currency = "LKR";
        $amount = number_format($class['fee'], 2, '.', '');

        // Order ID එක සුවිශේෂී ලෙස සකස් කිරීම
        $order_id = "ORD_" . $student_id . "_" . $class_id . "_" . time();
        $month = date('F');
        $year = date('Y');

        // 1. Payment එක ආරම්භ කිරීමට පෙර 'Pending' ලෙස Database එකට ඇතුළත් කිරීම
        $stmt = $conn->prepare("INSERT INTO payments (student_id, class_id, month, year, amount, payment_status, method, transaction_id, payment_type, paid_date) VALUES (?, ?, ?, ?, ?, 'pending', 'Online', ?, 'Full', NOW())");
        $stmt->bind_param("iissds", $student_id, $class_id, $month, $year, $amount, $order_id);

        if ($stmt->execute()) {
            // Hash එක සෑදීම
            $hash_str = $merchant_id . $order_id . $amount . $currency . strtoupper(md5($merchant_secret));
            $hash = strtoupper(md5($hash_str));

            // URL සැකසීම (ඔබේ localhost path එක වෙනස් නම් මෙතැන වෙනස් කරන්න)
            $base_url = "http://localhost/AL-Class-Management-System-main/student_portal/pages";

            echo json_encode([
                "sandbox" => true,
                "merchant_id" => $merchant_id,
                "return_url" => $base_url . "/payment_success.php?order_id=" . $order_id, // සාර්ථක වූ පසු යන තැන
                "cancel_url" => $base_url . "/enroll_class.php?class_id=" . $class_id,
                "notify_url" => $base_url . "/notify.php", // Optional
                "order_id" => $order_id,
                "items" => $class['subject'],
                "amount" => $amount,
                "currency" => $currency,
                "hash" => $hash,
                "first_name" => $student['full_name'],
                "last_name" => "Student",
                "email" => $student['email'],
                "phone" => $student['student_phone'],
                "address" => "Colombo",
                "city" => "Colombo",
                "country" => "Sri Lanka"
            ]);
        } else {
            echo json_encode(['error' => 'Database Insert Failed: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['error' => 'Class or Student not found']);
    }
}
?>