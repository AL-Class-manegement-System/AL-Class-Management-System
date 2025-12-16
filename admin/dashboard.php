<?php
// admin/dashboard.php
session_start();
include('includes/auth.php');
include('db_con.php');

// --- 1. GET COUNTS FROM DATABASE ---

// Total Students
$res_std = $conn->query("SELECT COUNT(*) as count FROM students");
$count_students = $res_std->fetch_assoc()['count'];

// Total Teachers
$res_tea = $conn->query("SELECT COUNT(*) as count FROM teachers WHERE status=1");
$count_teachers = $res_tea->fetch_assoc()['count'];

// Active Classes
$res_cls = $conn->query("SELECT COUNT(*) as count FROM classes WHERE status=1");
$count_classes = $res_cls->fetch_assoc()['count'];

// Total Income (Paid Payments)
$res_inc = $conn->query("SELECT SUM(amount) as total FROM payments WHERE payment_status='paid'");
$row_inc = $res_inc->fetch_assoc();
$total_income = $row_inc['total'] ? $row_inc['total'] : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Future Minds</title>
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

<body class="bg-gray-100 text-gray-800 font-sans">

    <div class="flex h-screen overflow-hidden">

        <?php include('includes/sidebar.php'); ?>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden ml-64">

            <header
                class="bg-white border-b border-gray-200 h-20 flex items-center justify-between px-6 lg:px-10 sticky top-0 z-30">
                <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-bold text-gray-800">Administrator</p>
                        </div>
                        <div
                            class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 border border-indigo-200">
                            <i class="fas fa-user-shield"></i>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 lg:p-10">

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Overview</h1>
                        <p class="text-gray-500 text-sm mt-1">Here is what's happening with your institute today.</p>
                    </div>
                    <a href="../log/registration.php" target="_blank"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium shadow-lg shadow-indigo-500/30 flex items-center gap-2 transition-transform active:scale-95">
                        <i class="fas fa-plus"></i> New Registration
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

                    <div
                        class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Total Students</p>
                                <h3 class="text-3xl font-bold text-gray-800">
                                    <?php echo number_format($count_students); ?></h3>
                            </div>
                            <div
                                class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Total Income</p>
                                <h3 class="text-3xl font-bold text-gray-800">Rs.
                                    <?php echo number_format($total_income); ?></h3>
                            </div>
                            <div
                                class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
                                <i class="fas fa-coins text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Active Classes</p>
                                <h3 class="text-3xl font-bold text-gray-800"><?php echo $count_classes; ?></h3>
                            </div>
                            <div
                                class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
                                <i class="fas fa-chalkboard text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Teachers</p>
                                <h3 class="text-3xl font-bold text-gray-800"><?php echo $count_teachers; ?></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-pink-50 flex items-center justify-center text-pink-600">
                                <i class="fas fa-chalkboard-teacher text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">Newest Students</h3>
                        <a href="student.php" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View
                            All</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="px-6 py-4">Student</th>
                                    <th class="px-6 py-4">Reg No</th>
                                    <th class="px-6 py-4">Stream</th>
                                    <th class="px-6 py-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                <?php
                                $recent_sql = "SELECT * FROM students ORDER BY student_id DESC LIMIT 5";
                                $rec_res = $conn->query($recent_sql);

                                if ($rec_res && $rec_res->num_rows > 0) {
                                    while ($row = $rec_res->fetch_assoc()) {
                                        $status_color = ($row['status'] == 1) ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700';
                                        $status_text = ($row['status'] == 1) ? 'Active' : 'Inactive';
                                        ?>
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <div>
                                                        <p class="font-semibold text-gray-800">
                                                            <?php echo htmlspecialchars($row['full_name']); ?></p>
                                                        <p class="text-xs text-gray-500">
                                                            <?php echo htmlspecialchars($row['student_phone']); ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-600"><?php echo $row['reg_number']; ?></td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                                    <?php echo htmlspecialchars($row['stream']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?php echo $status_color; ?>">
                                                    <?php echo $status_text; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center py-4 text-gray-500'>No students found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>

</body>

</html>