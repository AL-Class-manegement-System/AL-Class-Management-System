<?php
// admin/accounting.php - All bugs resolved and improved with error handling.

session_start();
include('includes/auth.php'); 
include('db_con.php');      // Database connection ($conn)

// Admin role check (as per your requirement)
// if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
//     header("Location: dashboard.php");
//     exit();
// }

$current_month = date('Y-m');
$report_month = isset($_GET['month']) ? htmlspecialchars($_GET['month']) : $current_month;

// Initialize results
$total_income = 0.00;
$total_salaries = 0.00;
$detailed_salary_result = null;
$error = null;

// ==========================================
// 1. Calculate Total Income (Prepared Statement)
// ==========================================
$income_query = "SELECT SUM(amount) AS total_income FROM payments WHERE DATE_FORMAT(paid_date, '%Y-%m') = ? AND payment_status = 'paid'";
$income_stmt = $conn->prepare($income_query);

if ($income_stmt) {
    $income_stmt->bind_param("s", $report_month);
    $income_stmt->execute();
    $income_result = $income_stmt->get_result();
    $total_income = $income_result->fetch_assoc()['total_income'] ?? 0.00;
    $income_stmt->close();
} else {
    $error = "Income Query Preparation Failed: " . $conn->error;
}


// ==========================================
// 2. Calculate Total Salaries (Prepared Statement)
// ==========================================
$salary_query = "SELECT SUM(s.amount) AS total_salaries_paid
                 FROM salaries s
                 WHERE s.payment_month = ? AND s.status = 'paid'";
$salary_stmt = $conn->prepare($salary_query);

if ($salary_stmt) {
    $salary_stmt->bind_param("s", $report_month);
    $salary_stmt->execute();
    $salary_result = $salary_stmt->get_result();
    $total_salaries = $salary_result->fetch_assoc()['total_salaries_paid'] ?? 0.00;
    $salary_stmt->close();
} else {
    $error = "Salary Summary Query Preparation Failed: " . $conn->error;
}

$net_profit = $total_income - $total_salaries;

// ==========================================
// 3. Detailed Salary Report (Prepared Statement) - FIX applied here
// ==========================================
$detailed_salary_query = "SELECT
                            t.full_name AS teacher_name, 
                            s.amount,
                            s.payment_date,
                            s.status
                          FROM salaries s
                          JOIN teachers t ON s.teacher_id = t.teacher_id 
                          WHERE s.payment_month = ?
                          ORDER BY s.payment_date DESC";
$detailed_salary_stmt = $conn->prepare($detailed_salary_query);

if ($detailed_salary_stmt) { // FIX: Check if prepare succeeded
    $detailed_salary_stmt->bind_param("s", $report_month);
    $detailed_salary_stmt->execute();
    $detailed_salary_result = $detailed_salary_stmt->get_result();
    $detailed_salary_stmt->close();
} else {
    $error = "Detailed Salary Report Preparation Failed: " . $conn->error;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Accounting Reports | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex">
        <?php include('includes/sidebar.php'); ?>
        <div class="ml-64 flex-1">
            <main class="p-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">📊 Accounting Reports</h1>
                <hr class="mb-6">

                <?php if ($error): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                        <p class="font-bold">Database Error</p>
                        <p class="text-sm"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>

                <form method="GET" class="flex flex-wrap gap-4 items-end mb-6 bg-white p-4 rounded-xl shadow-sm">
                    <label for="month" class="text-sm font-semibold text-gray-700">Select Report Month:</label>
                    <input type="month" id="month" name="month" value="<?php echo htmlspecialchars($report_month); ?>" required class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">Show Report</button>
                </form>

                <h2 class="text-2xl font-bold text-gray-700 mb-6">Financial Overview for <?php echo date('F Y', strtotime($report_month . '-01')); ?></h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="p-6 rounded-xl text-white shadow-lg income-box bg-green-500">
                        <h3 class="text-sm font-semibold uppercase opacity-90">Total Income (Student Fees)</h3>
                        <p class="text-3xl font-extrabold mt-1">Rs. <?php echo number_format($total_income, 2); ?></p>
                    </div>
                    <div class="p-6 rounded-xl text-white shadow-lg expense-box bg-red-500">
                        <h3 class="text-sm font-semibold uppercase opacity-90">Total Expense (Teacher Salaries)</h3>
                        <p class="text-3xl font-extrabold mt-1">Rs. <?php echo number_format($total_salaries, 2); ?></p>
                    </div>
                    <div class="p-6 rounded-xl text-white shadow-lg profit-box <?php echo $net_profit >= 0 ? 'bg-indigo-600' : 'bg-red-700'; ?>">
                        <h3 class="text-sm font-semibold uppercase opacity-90">Net Profit / Loss</h3>
                        <p class="text-3xl font-extrabold mt-1">Rs. <?php echo number_format($net_profit, 2); ?></p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <h3 class="text-xl font-bold text-gray-700 p-4 border-b border-gray-100">Teacher Salary Report</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Teacher Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Amount Paid</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Payment Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if ($detailed_salary_result && $detailed_salary_result->num_rows > 0): ?>
                                    <?php while($row = $detailed_salary_result->fetch_assoc()): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">Rs. <?php echo number_format($row['amount'], 2); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo htmlspecialchars($row['payment_date']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <?php echo $row['status'] == 'paid' ? '<span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>' : '<span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>'; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center py-6 text-gray-500">No salary records for this month.</td></tr>
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