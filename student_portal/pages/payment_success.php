<?php
// student_portal/pages/payment_success.php
session_start();
include('../../includes/connection.php');
include('../includes/student_header.php');

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // 1. Payment Status එක 'paid' ලෙස යාවත්කාලීන කිරීම
    $update_sql = "UPDATE payments SET payment_status = 'paid' WHERE transaction_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("s", $order_id);

    if ($stmt->execute()) {
        // 2. අදාළ Payment විස්තර ලබා ගැනීම (Class ID සහ Student ID)
        $get_pay = $conn->query("SELECT * FROM payments WHERE transaction_id = '$order_id'");
        if ($get_pay->num_rows > 0) {
            $payment_data = $get_pay->fetch_assoc();
            $student_id = $payment_data['student_id'];
            $class_id = $payment_data['class_id'];

            // 3. Enroll වී ඇත්දැයි බැලීම
            $check_enroll = $conn->query("SELECT * FROM enrollments WHERE student_id = $student_id AND class_id = $class_id");

            if ($check_enroll->num_rows == 0) {
                // Enroll වී නැත්නම් Enroll කිරීම
                $enroll_sql = "INSERT INTO enrollments (student_id, class_id, status, joined_date, payment_method) VALUES (?, ?, 1, NOW(), 'Online')";
                $stmt2 = $conn->prepare($enroll_sql);
                $stmt2->bind_param("ii", $student_id, $class_id);
                $stmt2->execute();
            } else {
                // දැනටමත් Enroll වී ඇත්නම් Status එක Active (1) කිරීම
                $conn->query("UPDATE enrollments SET status = 1 WHERE student_id = $student_id AND class_id = $class_id");
            }

            // සාර්ථක පණිවිඩය
            echo "
            <div class='flex items-center justify-center h-screen bg-green-50'>
                <div class='bg-white p-10 rounded-3xl shadow-xl text-center'>
                    <div class='text-green-500 text-6xl mb-4'><i class='fas fa-check-circle'></i></div>
                    <h1 class='text-3xl font-bold text-gray-800 mb-2'>Payment Successful!</h1>
                    <p class='text-gray-600 mb-6'>You have been successfully enrolled in the class.</p>
                    <a href='my_classes.php' class='bg-green-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-green-700 transition'>Go to My Classes</a>
                </div>
            </div>";
        } else {
            echo "<div class='text-center p-10 text-red-500'>Error: Payment record not found.</div>";
        }
    } else {
        echo "<div class='text-center p-10 text-red-500'>Error: Could not update payment status.</div>";
    }
} else {
    echo "<div class='text-center p-10 text-red-500'>Invalid Access.</div>";
}

include('../includes/footer.php');
?>