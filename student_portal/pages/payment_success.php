<?php
// student_portal/pages/payment_success.php
session_start();
include('../../includes/connection.php');
include('../includes/student_header.php');

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // 1. Payment Status එක 'paid' ලෙස යාවත්කාලීන කිරීම (Prepared Statement)
    $update_sql = "UPDATE payments SET payment_status = 'paid' WHERE transaction_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("s", $order_id);

    if ($stmt->execute()) {
        // 2. අදාළ Payment විස්තර ලබා ගැනීම
        $get_pay_sql = "SELECT * FROM payments WHERE transaction_id = ?";
        $pay_stmt = $conn->prepare($get_pay_sql);
        $pay_stmt->bind_param("s", $order_id);
        $pay_stmt->execute();
        $payment_result = $pay_stmt->get_result();

        if ($payment_result->num_rows > 0) {
            $payment_data = $payment_result->fetch_assoc();
            $student_id = $payment_data['student_id'];
            $class_id = $payment_data['class_id'];
            $amount = $payment_data['amount'];
            $paid_date = $payment_data['paid_date'];

            // 3. පන්තියේ විස්තර ලබා ගැනීම (Receipt එකට පෙන්වීමට)
            $class_sql = "SELECT * FROM classes WHERE class_id = ?";
            $class_stmt = $conn->prepare($class_sql);
            $class_stmt->bind_param("i", $class_id);
            $class_stmt->execute();
            $class_data = $class_stmt->get_result()->fetch_assoc();

            // 4. Enroll වී ඇත්දැයි බැලීම සහ Enroll කිරීම
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
?>

            <div class="flex-1 flex flex-col items-center justify-center min-h-screen bg-gray-100 p-4 print:bg-white print:p-0">
                
                <div class="bg-white p-8 rounded-3xl shadow-xl w-full max-w-md border border-gray-200 relative print:shadow-none print:border-none print:w-full">
                    
                    <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 print:hidden">
                        <div class="bg-green-500 rounded-full p-4 shadow-lg border-4 border-white">
                            <i class="fas fa-check text-3xl text-white"></i>
                        </div>
                    </div>

                    <div class="text-center mt-8 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Payment Successful!</h2>
                        <p class="text-gray-500 text-sm">Thank you for your enrollment.</p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 border-dashed mb-6 print:border-gray-800 print:bg-white">
                        <div class="text-center mb-4 border-b border-gray-200 pb-4 border-dashed">
                            <h3 class="text-lg font-bold text-gray-800">Future Minds Institute</h3>
                            <p class="text-xs text-gray-500">Official Payment Receipt</p>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Receipt No:</span>
                                <span class="font-mono font-bold text-gray-800"><?php echo htmlspecialchars($order_id); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Date:</span>
                                <span class="font-medium text-gray-800"><?php echo date('Y-M-d h:i A', strtotime($paid_date)); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Student ID:</span>
                                <span class="font-medium text-gray-800"><?php echo htmlspecialchars($student['reg_number']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Student Name:</span>
                                <span class="font-medium text-gray-800"><?php echo htmlspecialchars($student['full_name']); ?></span>
                            </div>
                            
                            <div class="border-t border-gray-200 border-dashed my-2"></div>

                            <div class="flex justify-between">
                                <span class="text-gray-500">Class:</span>
                                <span class="font-bold text-gray-800 text-right"><?php echo htmlspecialchars($class_data['subject']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Teacher:</span>
                                <span class="font-medium text-gray-800 text-right"><?php echo htmlspecialchars($class_data['teacher_name']); ?></span>
                            </div>

                            <div class="border-t border-gray-200 border-dashed my-2"></div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-800 font-bold text-lg">Total Paid</span>
                                <span class="text-green-600 font-bold text-xl">LKR <?php echo number_format($amount, 2); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 print:hidden">
                        <button onclick="window.print()" class="w-full py-3 bg-gray-800 text-white rounded-xl font-bold hover:bg-gray-900 transition flex items-center justify-center gap-2">
                            <i class="fas fa-print"></i> Print Receipt
                        </button>
                        
                        <a href="my_classes.php" class="w-full py-3 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-xl font-bold hover:bg-indigo-100 transition text-center">
                            Go to My Classes
                        </a>
                    </div>

                    <div class="hidden print:block text-center mt-8 text-xs text-gray-400">
                        <p>This is a computer generated receipt.</p>
                        <p>Generated on <?php echo date('Y-m-d H:i:s'); ?></p>
                    </div>

                </div>
            </div>

<?php
        } else {
            echo "<div class='text-center p-10 text-red-500 font-bold text-xl mt-10'>Error: Payment record not found.</div>";
        }
    } else {
        echo "<div class='text-center p-10 text-red-500 font-bold text-xl mt-10'>Error: Could not update payment status.</div>";
    }
} else {
    echo "<div class='text-center p-10 text-red-500 font-bold text-xl mt-10'>Invalid Access.</div>";
}

include('../includes/footer.php');
?>