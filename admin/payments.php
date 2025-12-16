<?php
// admin/payments.php
session_start();
include('includes/auth.php');
include('db_con.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Payments - Future Minds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">

    <?php include('includes/sidebar.php'); ?>

    <div class="ml-64 flex flex-col min-h-screen">

        <header class="bg-white shadow-sm py-4 px-8 flex justify-between items-center sticky top-0 z-40">
            <h2 class="text-2xl font-bold text-gray-800">Payments Management</h2>
            <a href="add_manual_payment.php"
                class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                <i class="fas fa-plus-circle mr-2"></i> Add Manual Payment
            </a>
        </header>

        <main class="p-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Class</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Method</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Slip/Ref</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            // Join tables to get Student Name and Class Name
                            $sql = "SELECT p.*, s.full_name, s.reg_number, c.class_name 
                                    FROM payments p 
                                    JOIN students s ON p.student_id = s.student_id 
                                    JOIN classes c ON p.class_id = c.class_id 
                                    ORDER BY p.payment_id DESC";
                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0):
                                while ($row = $result->fetch_assoc()):
                                    ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-sm text-gray-800">
                                                <?php echo htmlspecialchars($row['full_name']); ?>
                                            </div>
                                            <div class="text-xs text-gray-500"><?php echo $row['reg_number']; ?></div>
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            <?php echo htmlspecialchars($row['class_name']); ?>
                                        </td>

                                        <td class="px-6 py-4 text-sm">
                                            <?php echo ($row['method'] == 'Online') ?
                                                '<span class="text-blue-600 font-bold"><i class="fas fa-globe"></i> Online</span>' :
                                                '<span class="text-purple-600 font-bold"><i class="fas fa-money-bill-wave"></i> ' . $row['method'] . '</span>'; ?>
                                        </td>

                                        <td class="px-6 py-4">
                                            <?php if (!empty($row['slip_image'])): ?>
                                                <a href="../uploads/slips/<?php echo $row['slip_image']; ?>" target="_blank"
                                                    class="bg-gray-200 text-gray-700 px-3 py-1 rounded text-xs hover:bg-gray-300 transition">
                                                    <i class="fas fa-eye"></i> View Slip
                                                </a>
                                            <?php else: ?>
                                                <span class="text-gray-400 text-xs text-center block">-</span>
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
                                            <?php if ($row['payment_status'] == 'pending'): ?>
                                                <a href="approve_payment.php?pay_id=<?php echo $row['payment_id']; ?>"
                                                    class="bg-green-600 text-white px-3 py-1.5 rounded text-xs hover:bg-green-700 transition shadow-sm"
                                                    onclick="return confirm('Approve this payment and enroll student?');">
                                                    <i class="fas fa-check"></i> Approve
                                                </a>
                                            <?php else: ?>
                                                <span class="text-green-600 text-xs font-bold"><i class="fas fa-check-circle"></i>
                                                    Approved</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-6 text-gray-500">No payment records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>

</html>