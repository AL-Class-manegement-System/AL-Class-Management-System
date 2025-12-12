<?php
// admin/approve_payment.php
session_start();
require_once '../includes/connection.php';

// 1. Admin Login Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['pay_id'])) {
    $payment_id = intval($_GET['pay_id']);

    // 2. ගෙවීම් විස්තර ලබා ගැනීම
    $sql_pay = "SELECT p.*, c.class_name, c.teacher_name, s.parent_phone, s.reg_number 
                FROM payments p
                JOIN classes c ON p.class_id = c.class_id
                JOIN students s ON p.student_id = s.student_id
                WHERE p.payment_id = ?";
    $stmt = $conn->prepare($sql_pay);
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment = $result->fetch_assoc();

    if ($payment) {
        $student_id = $payment['student_id'];
        $class_id = $payment['class_id'];
        $class_name = $payment['class_name'];
        $parent_phone = $payment['parent_phone'];
        $student_reg = $payment['reg_number'];

        // 3. Payment Status එක 'paid' ලෙස වෙනස් කිරීම
        $update_sql = "UPDATE payments SET payment_status = 'paid' WHERE payment_id = ?";
        $stmt_up = $conn->prepare($update_sql);
        $stmt_up->bind_param("i", $payment_id);
        
        if ($stmt_up->execute()) {
            
            // 4. Enrollments Table එකට දත්ත ඇතුලත් කිරීම (Enroll කිරීම)
            // කලින් Enroll වී ඇත්දැයි පරීක්ෂා කිරීම
            $check_enr = "SELECT * FROM enrollments WHERE student_id = ? AND class_id = ?";
            $stmt_chk = $conn->prepare($check_enr);
            $stmt_chk->bind_param("ii", $student_id, $class_id);
            $stmt_chk->execute();
            
            if ($stmt_chk->get_result()->num_rows == 0) {
                // Enroll වී නැත්නම් අලුතින් ඇතුලත් කරන්න (Joined Date එක අද ලෙස වැටේ)
                $enroll_sql = "INSERT INTO enrollments (student_id, class_id, joined_date) VALUES (?, ?, NOW())";
                $stmt_enr = $conn->prepare($enroll_sql);
                $stmt_enr->bind_param("ii", $student_id, $class_id);
                $stmt_enr->execute();
                
                // 5. SMS යැවීම (Student Joined Message)
                if (!empty($parent_phone)) {
                    $msg = "Success! Payment approved for $class_name. You are now enrolled.\nID: $student_reg\n- Future Minds";
                    sendSMS($parent_phone, $msg);
                }
            }

            echo "<script>alert('Payment Approved & Student Enrolled Successfully!'); window.location.href='payments.php';</script>";
        } else {
            echo "<script>alert('Error updating payment.'); window.location.href='payments.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid Payment ID.'); window.location.href='payments.php';</script>";
    }
} else {
    header("Location: payments.php");
}

// SMS Function
function sendSMS($to, $message) {
    // ඔබේ SMS Gateway Code එක මෙතැනට දමන්න (Notify.lk / Twilio)
    // උදා: 
    // $api_key = "YOUR_KEY";
    // $url = "https://sms-provider.com/send?to=$to&msg=" . urlencode($message);
    // file_get_contents($url);
}
?>