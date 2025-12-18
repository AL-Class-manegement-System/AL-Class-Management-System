<?php
// admin/payments.php
session_start();
include('includes/auth.php');
include('db_con.php');

// --- 1. Approve / Reject Logic ---
if (isset($_GET['approve_id'])) {
    $pay_id = intval($_GET['approve_id']);
    // Update payment status to 'paid'
    $stmt = $conn->prepare("UPDATE payments SET payment_status = 'paid' WHERE payment_id = ?");
    $stmt->bind_param("i", $pay_id);
    if($stmt->execute()){
        header("Location: payments.php?msg=Payment Approved Successfully");
    }
    exit();
}

if (isset($_GET['reject_id'])) {
    $pay_id = intval($_GET['reject_id']);
    // Update payment status to 'failed'
    $stmt = $conn->prepare("UPDATE payments SET payment_status = 'failed' WHERE payment_id = ?");
    $stmt->bind_param("i", $pay_id);
    if($stmt->execute()){
        header("Location: payments.php?msg=Payment Rejected");
    }
    exit();
}

// --- 2. Filter Logic ---
$where_clauses = [];
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $status = $conn->real_escape_string($_GET['status']);
    $where_clauses[] = "p.payment_status = '$status'";
}
if (isset($_GET['month']) && !empty($_GET['month'])) {
    $month = $conn->real_escape_string($_GET['month']);
    $where_clauses[] = "p.month = '$month'";
}
if (isset($_GET['year']) && !empty($_GET['year'])) {
    $year = intval($_GET['year']);
    $where_clauses[] = "p.year = $year";
}
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where_clauses[] = "(s.full_name LIKE '%$search%' OR s.reg_number LIKE '%$search%')";
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(' AND ', $where_clauses);
}

// --- 3. Fetch Data ---
$sql = "SELECT p.*, s.full_name, s.reg_number, c.class_name 
        FROM payments p 
        LEFT JOIN students s ON p.student_id = s.student_id 
        LEFT JOIN classes c ON p.class_id = c.class_id 
        $where_sql
        ORDER BY p.paid_date DESC";

$result = $conn->query($sql);

// Get Distinct Years for Filter
$years_res = $conn->query("SELECT DISTINCT year FROM payments ORDER BY year DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <?php include('includes/sidebar.php'); ?>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden ml-64">
            
            <header class="bg-white border-b h-20 flex items-center justify-between px-8">
                <h1 class="text-2xl font-bold text-gray-800">💰 Payment History</h1>
                <a href="add_manual_payment.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-plus"></i> Add Payment
                </a>
            </header>

            <main class="flex-1 overflow-y-auto p-8">
                
                <?php if(isset($_GET['msg'])): ?>
                    <div id="alert" class="bg-green-100 text-green-700 p-3 rounded mb-4 border border-green-200">
                        <?php echo htmlspecialchars($_GET['msg']); ?>
                    </div>
                    <script>setTimeout(() => document.getElementById('alert').style.display = 'none', 3000);</script>
                <?php endif; ?>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                        
                        <div>
                            <label class="text-xs font-bold text-gray-500">Search Student</label>
                            <input type="text" name="search" value="<?php echo $_GET['search'] ?? ''; ?>" placeholder="Name or Reg No" class="w-full border p-2 rounded text-sm">
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500">Month</label>
                            <select name="month" class="w-full border p-2 rounded text-sm">
                                <option value="">All Months</option>
                                <?php 
                                $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                                foreach($months as $m) {
                                    $sel = (isset($_GET['month']) && $_GET['month'] == $m) ? 'selected' : '';
                                    echo "<option value='$m' $sel>$m</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500">Year</label>
                            <select name="year" class="w-full border p-2 rounded text-sm">
                                <option value="">All Years</option>
                                <?php while($y = $years_res->fetch_assoc()): ?>
                                    <option value="<?php echo $y['year']; ?>" <?php echo (isset($_GET['year']) && $_GET['year'] == $y['year']) ? 'selected' : ''; ?>>
                                        <?php echo $y['year']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500">Status</label>
                            <select name="status" class="w-full border p-2 rounded text-sm">
                                <option value="">All Status</option>
                                <option value="paid" <?php echo (isset($_GET['status']) && $_GET['status'] == 'paid') ? 'selected' : ''; ?>>Paid</option>
                                <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="failed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'failed') ? 'selected' : ''; ?>>Failed</option>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-900 w-full">Filter</button>
                            <a href="payments.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Class/Month</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Method</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['full_name']); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo htmlspecialchars($row['reg_number']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($row['class_name']); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo $row['month'] . ' ' . $row['year']; ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold">Rs. <?php echo number_format($row['amount'], 2); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo $row['payment_type']; ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm"><?php echo $row['method']; ?></div>
                                        <?php if(!empty($row['slip_image'])): ?>
                                            <a href="../uploads/slips/<?php echo $row['slip_image']; ?>" target="_blank" class="text-xs text-indigo-600 underline">View Slip</a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php 
                                            $st = strtolower($row['payment_status']);
                                            $color = ($st == 'paid') ? 'bg-green-100 text-green-800' : (($st == 'pending') ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                        ?>
                                        <span class="px-2 py-1 rounded-full text-xs font-bold <?php echo $color; ?>"><?php echo ucfirst($st); ?></span>
                                    </td>
                                    <td class="px-6 py-4 flex gap-2">
                                        <?php if($st == 'pending'): ?>
                                            <a href="?approve_id=<?php echo $row['payment_id']; ?>" onclick="return confirm('Approve?')" class="text-green-600 hover:text-green-800" title="Approve"><i class="fas fa-check-circle text-xl"></i></a>
                                            <a href="?reject_id=<?php echo $row['payment_id']; ?>" onclick="return confirm('Reject?')" class="text-red-600 hover:text-red-800" title="Reject"><i class="fas fa-times-circle text-xl"></i></a>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs italic">Completed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
</body>
</html>