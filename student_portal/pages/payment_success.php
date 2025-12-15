<?php
// student_portal/pages/payment_success.php
include('../includes/connection.php');
session_start();

if (!isset($_GET['order_id'])) {
    die("Invalid Request");
}

$order_id = $_GET['order_id'];

// Parse Order ID (Format: ORD_StudentID_ClassID_Timestamp)
$parts = explode('_', $order_id);
if (count($parts) < 4) {
    die("Invalid Order Format");
}

$student_id = intval($parts[1]);
$class_id = intval($parts[2]);
$payment_date = date("Y-m-d H:i:s");

// 1. Check if already enrolled
$check_sql = "SELECT * FROM enrollments WHERE student_id = ? AND class_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $student_id, $class_id);
$check_stmt->execute();
$check_res = $check_stmt->get_result();

if ($check_res->num_rows == 0) {
    // 2. Enroll the student (Status 1 = Active/Paid)
    $insert_sql = "INSERT INTO enrollments (student_id, class_id, status, enrolled_at, payment_method) VALUES (?, ?, 1, ?, 'Online')";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iis", $student_id, $class_id, $payment_date);
    $stmt->execute();
}

// 3. Fetch Details for Receipt
$class_sql = "SELECT * FROM classes WHERE class_id = ?";
$cls_stmt = $conn->prepare($class_sql);
$cls_stmt->bind_param("i", $class_id);
$cls_stmt->execute();
$class = $cls_stmt->get_result()->fetch_assoc();

$st_sql = "SELECT * FROM students WHERE student_id = ?";
$st_stmt = $conn->prepare($st_sql);
$st_stmt->bind_param("i", $student_id);
$st_stmt->execute();
$student = $st_stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }

            .receipt-container {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="receipt-container bg-white w-full max-w-md p-8 rounded-3xl shadow-2xl relative overflow-hidden">

        <div class="absolute top-0 left-0 w-full h-2 bg-green-500"></div>
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-4xl text-green-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Payment Successful!</h1>
            <p class="text-gray-500">Transaction Completed</p>
        </div>

        <div class="border-t border-b border-gray-100 py-6 mb-6 space-y-4">
            <div class="flex justify-between">
                <span class="text-gray-500">Receipt No</span>
                <span class="font-mono font-bold text-gray-800">#<?php echo substr($order_id, -8); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Date</span>
                <span class="font-medium text-gray-800"><?php echo date("Y-M-d h:i A"); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Student Name</span>
                <span
                    class="font-medium text-gray-800 text-right"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Subject</span>
                <span
                    class="font-medium text-gray-800 text-right"><?php echo htmlspecialchars($class['subject']); ?></span>
            </div>
        </div>

        <div class="flex justify-between items-center mb-8">
            <span class="text-lg font-bold text-gray-800">Total Paid</span>
            <span class="text-2xl font-bold text-indigo-600">LKR <?php echo number_format($class['fee'], 2); ?></span>
        </div>

        <div class="space-y-3 no-print">
            <button onclick="window.print()"
                class="w-full py-3 bg-slate-800 text-white rounded-xl font-bold hover:bg-slate-900 transition flex items-center justify-center gap-2">
                <i class="fas fa-download"></i> Download Receipt
            </button>
            <a href="my_classes.php"
                class="block w-full py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition text-center">
                Back to My Classes
            </a>
        </div>

        <div class="mt-8 text-center text-xs text-gray-400">
            Future Minds Academy - Electronic Receipt
        </div>

    </div>

</body>

</html>