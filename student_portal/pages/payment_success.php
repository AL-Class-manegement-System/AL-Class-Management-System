<?php
// student_portal/pages/payment_success.php
include('../../includes/connection.php');
session_start();

if (isset($_GET['order_id'])) {
    $parts = explode('_', $_GET['order_id']); // ORD_Student_Class_Time
    $student_id = intval($parts[1]);
    $class_id = intval($parts[2]);

    $class = $conn->query("SELECT fee FROM classes WHERE class_id = $class_id")->fetch_assoc();
    $amount = $class['fee'];
    $month = date('F');
    $year = date('Y');

    // Insert into PAYMENTS as 'paid' (Money received)
    // NOTE: Enrollments table is NOT updated here. Admin must click "Approve" to enroll.
    $stmt = $conn->prepare("INSERT INTO payments (student_id, class_id, month, year, amount, payment_status, method, paid_date) 
                            VALUES (?, ?, ?, ?, ?, 'paid', 'Online', NOW())");
    $stmt->bind_param("iissd", $student_id, $class_id, $month, $year, $amount);

    if ($stmt->execute()) {
        echo "<script>alert('Payment Successful! Please wait for Admin to approve your enrollment.'); window.location.href='my_classes.php';</script>";
    }
}
?>