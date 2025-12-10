<?php
// admin/payments.php - All bugs resolved.

session_start();
include('includes/auth.php'); 
include('db_con.php');      // Database connection ($conn)

// Admin role check, if needed, add it here (based on the 'role' column in your users table)
// if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
//     header("Location: dashboard.php");
//     exit();
// }


// =======================
// ACTION: Mark as Paid (Secure Prepared Statement)
// =======================
if (isset($_POST['action']) && $_POST['action'] == 'mark_paid' && isset($_POST['payment_id'])) {
    
    // Prepared Statement for Update
    $update_stmt = $conn->prepare("UPDATE payments SET payment_status = 'paid', paid_date = NOW() WHERE payment_id = ?");
    
    if ($update_stmt) {
        $update_stmt->bind_param("i", $_POST['payment_id']); // 'i' is for integer type
        
        if ($update_stmt->execute()) {
            $_SESSION['message'] = "Payment successfully marked as 'paid'.";
        } else {
            $_SESSION['error'] = "Error marking payment: " . $update_stmt->error;
        }
        $update_stmt->close();
    } else {
        $_SESSION['error'] = "Database Prepare Error: " . $conn->error;
    }
    header("Location: payments.php");
    exit();
}
// =======================

// Fetch all payment data (Join on student_id [INT] and correct column names)
$query_payments = "SELECT
                    p.payment_id,
                    s.full_name AS student_name, 
                    s.reg_number AS student_reg_number, 
                    c.class_name AS class_name,
                    p.amount,
                    p.paid_date, 
                    p.payment_status 
                   FROM payments p
                   JOIN students s ON p.student_id = s.student_id 
                   JOIN classes c ON p.class_id = c.class_id
                   ORDER BY p.paid_date DESC";

$result_payments = $conn->query($query_payments);


function getStatusBadge($status) {
    switch ($status) {
        case 'paid':
            return '<span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>';
        case 'pending':
            return '<span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>';
        case 'failed':
            return '<span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">Failed</span>';
        default:
            return $status;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Management | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex">
        <?php include('includes/sidebar.php'); ?>
        <div class="ml-64 flex-1">
            <main class="p-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">💰 Payment Management</h1>
                <hr class="mb-6">

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <h2 class="text-xl font-bold text-gray-700 p-4 border-b border-gray-100">All Payment Records</h2>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Payment ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Student (Reg No)</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Class</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Month/Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if ($result_payments && $result_payments->num_rows > 0): ?>
                                    <?php while($row = $result_payments->fetch_assoc()): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['payment_id']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['student_name']); ?></div>
                                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($row['student_reg_number']); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['class_name']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php echo date('Y-m-d', strtotime($row['paid_date'])); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">Rs. <?php echo number_format($row['amount'], 2); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php echo getStatusBadge($row['payment_status']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <?php if ($row['payment_status'] == 'pending'): ?>
                                                    <form method="POST" style="display:inline;">
                                                        <input type="hidden" name="payment_id" value="<?php echo $row['payment_id']; ?>">
                                                        <input type="hidden" name="action" value="mark_paid">
                                                        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 transition">✔ Mark as Paid</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-gray-400 text-xs">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="8" class="text-center py-8 text-gray-500">No payment records found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>
</body>
</html>