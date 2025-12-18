<?php
// student_portal/pages/get_payhere_hash.php
error_reporting(0); // Error errors පෙන්නන එක නවත්වන්න, නැත්නම් JSON වැඩ කරන්නේ නෑ
ini_set('display_errors', 0);

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
    $class_query = $conn->query("SELECT * FROM classes WHERE class_id = $class_id");
    $student_query = $conn->query("SELECT * FROM students WHERE student_id = $student_id");

    $class = $class_query->fetch_assoc();
    $student = $student_query->fetch_assoc();

    if ($class && $student) {
        // ඔබේ Sandbox Credentials නිවැරදිද බලන්න
        $merchant_id = "1231876";  
        $merchant_secret = "MTU3NDk1MTUxMjMyODExNzgyODcxOTkyOTQ3NTUzMzE0OTUwMDczNw=="; 
        
        $currency = "LKR";
        // දශම ස්ථාන 2කට අනිවාර්යයෙන්ම හැදෙන්න ඕනේ
        $amount = number_format($class['fee'], 2, '.', ''); 

        $order_id = "ORD_" . $student_id . "_" . $class_id . "_" . time();
        $month = date('F');
        $year = date('Y');

        // Payment එක 'pending' ලෙස ඇතුළත් කිරීම
        $stmt = $conn->prepare("INSERT INTO payments (student_id, class_id, month, year, amount, payment_status, method, transaction_id, payment_type, paid_date) VALUES (?, ?, ?, ?, ?, 'pending', 'Online', ?, 'Full', NOW())");
        
        // $amount string එකක් නිසා 'd' වෙනුවට 's' පාවිච්චි කිරීම වඩා හොඳයි
        $stmt->bind_param("iissss", $student_id, $class_id, $month, $year, $amount, $order_id);

        if ($stmt->execute()) {
            // Hash එක සෑදීම
            $hash_str = $merchant_id . $order_id . $amount . $currency . strtoupper(md5($merchant_secret));
            $hash = strtoupper(md5($hash_str));

            // URL සැකසීම (Host කරනකොට domain එක දාන්න)
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
                "phone" => !empty($student['student_phone']) ? $student['student_phone'] : "0777123456", // Phone නැත්නම් Error එන එක වළක්වන්න
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