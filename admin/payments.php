<?php
// admin/payments.php
session_start();
include('includes/auth.php');
include('db_con.php');

// Fetch payments with details
$sql = "SELECT p.*, s.full_name, s.reg_number, c.class_name 
        FROM payments p 
        JOIN students s ON p.student_id = s.student_id 
        JOIN classes c ON p.class_id = c.class_id 
        ORDER BY p.paid_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment Approvals</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-100 font-sans">
    <div class="flex">
        <?php include('includes/sidebar.php'); ?>
        <div class="ml-64 flex-1 p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">💳 Payment Approvals</h1>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Class</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Slip</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-sm"><?php echo htmlspecialchars($row['full_name']); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo $row['reg_number']; ?></div>
                                </td>
                                <td class="px-6 py-4 text-sm"><?php echo htmlspecialchars($row['class_name']); ?></td>
                                <td class="px-6 py-4 text-sm">
                                    <?php if ($row['method'] == 'Online'): ?>
                                        <span class="text-blue-600 font-bold"><i class="fas fa-globe"></i> Online</span>
                                    <?php else: ?>
                                        <span class="text-purple-600 font-bold"><i class="fas fa-file-invoice"></i> Slip</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if (!empty($row['slip_image'])): ?>
                                        <a href="../uploads/slips/<?php echo $row['slip_image']; ?>" target="_blank"
                                            class="text-indigo-600 underline text-xs">View Slip</a>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    $status = $row['payment_status'];
                                    $color = ($status == 'paid') ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
                                    echo "<span class='px-2 py-1 rounded-full text-xs font-bold $color'>" . ucfirst($status) . "</span>";
                                    ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    // Normally we check enrollments table here, but for simplicity, we let the approve script handle duplication checks.
                                    ?>
                                    <a href="approve_payment.php?pay_id=<?php echo $row['payment_id']; ?>"
                                        class="bg-green-600 text-white px-3 py-1.5 rounded text-xs hover:bg-green-700 transition shadow-sm"
                                        onclick="return confirm('Approve this payment and Enroll the student?');">
                                        <i class="fas fa-check-circle"></i> Approve & Enroll
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>