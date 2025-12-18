<?php
// admin/dashboard.php - Updated Dynamic Dashboard
session_start();
include('includes/auth.php');
include('db_con.php');

// ==========================================
// 1. DATA FETCHING FROM DATABASE
// ==========================================

// A. Total Students (Active Only)
// status = 1 ඇති සිසුන් පමණක් ගණනය කරයි.
$student_sql = "SELECT COUNT(*) as count FROM students WHERE status = 1";
$student_res = $conn->query($student_sql);
$count_students = $student_res->fetch_assoc()['count'];

// B. Total Teachers (Active Only)
$teacher_sql = "SELECT COUNT(*) as count FROM teachers WHERE status = 1";
$teacher_res = $conn->query($teacher_sql);
$count_teachers = $teacher_res->fetch_assoc()['count'];

// C. Active Classes
$class_sql = "SELECT COUNT(*) as count FROM classes WHERE status = 1";
$class_res = $conn->query($class_sql);
$count_classes = $class_res->fetch_assoc()['count'];

// D. Monthly Income (Current Month)
// මෙම මාසයට අදාළ ආදායම ගණනය කරයි.
$current_month = date('Y-m'); 
$income_stmt = $conn->prepare("SELECT SUM(amount) as total FROM payments WHERE DATE_FORMAT(paid_date, '%Y-%m') = ? AND payment_status = 'paid'");
$monthly_income = 0;

if ($income_stmt) {
    $income_stmt->bind_param("s", $current_month);
    $income_stmt->execute();
    $income_res = $income_stmt->get_result();
    $row_income = $income_res->fetch_assoc();
    $monthly_income = $row_income['total'] ? $row_income['total'] : 0;
    $income_stmt->close();
}

// E. Chart Data (Last 6 Months Income)
// පසුගිය මාස 6 සඳහා ආදායම් දත්ත ලබා ගනී.
$chart_labels = [];
$chart_data = [];

$chart_stmt = $conn->prepare("SELECT SUM(amount) as total FROM payments WHERE DATE_FORMAT(paid_date, '%Y-%m') = ? AND payment_status = 'paid'");

if ($chart_stmt) {
    for ($i = 5; $i >= 0; $i--) {
        $month_filter = date("Y-m", strtotime("-$i months")); // Database එකට
        $display_label = date("M", strtotime("-$i months"));  // Chart එකට
        
        $chart_stmt->bind_param("s", $month_filter);
        $chart_stmt->execute();
        $chart_res = $chart_stmt->get_result();
        $row = $chart_res->fetch_assoc();
        
        $chart_labels[] = $display_label;
        $chart_data[] = $row['total'] ? (float)$row['total'] : 0;
    }
    $chart_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Future Minds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>

<body class="bg-gray-100 text-gray-800 font-sans">

    <div class="flex h-screen overflow-hidden">

        <?php include('includes/sidebar.php'); ?>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden ml-64">

            <header class="bg-white border-b border-gray-200 h-20 flex items-center justify-between px-6 lg:px-10 sticky top-0 z-30">
                <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-bold text-gray-800">
                                <?php echo isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Admin'; ?>
                            </p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 border border-indigo-200">
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

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow group">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Total Active Students</p>
                                <h3 class="text-3xl font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">
                                    <?php echo number_format($count_students); ?>
                                </h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow group">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Income (<?php echo date('M'); ?>)</p>
                                <h3 class="text-3xl font-bold text-gray-800 group-hover:text-green-600 transition-colors">
                                    Rs. <?php echo number_format($monthly_income); ?>
                                </h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600 group-hover:scale-110 transition-transform">
                                <i class="fas fa-coins text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow group">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Active Classes</p>
                                <h3 class="text-3xl font-bold text-gray-800 group-hover:text-orange-600 transition-colors">
                                    <?php echo $count_classes; ?>
                                </h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 group-hover:scale-110 transition-transform">
                                <i class="fas fa-chalkboard text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow group">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Teachers</p>
                                <h3 class="text-3xl font-bold text-gray-800 group-hover:text-pink-600 transition-colors">
                                    <?php echo $count_teachers; ?>
                                </h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-pink-50 flex items-center justify-center text-pink-600 group-hover:scale-110 transition-transform">
                                <i class="fas fa-chalkboard-teacher text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                    
                    <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Revenue Analytics</h3>
                            <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded">Last 6 Months</span>
                        </div>
                        <div class="relative h-72 w-full">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800">Newest Students</h3>
                            <a href="student.php" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All</a>
                        </div>

                        <div class="overflow-y-auto flex-1 p-4 space-y-3">
                            <?php
                            $recent_sql = "SELECT * FROM students ORDER BY student_id DESC LIMIT 5";
                            $rec_res = $conn->query($recent_sql);

                            if ($rec_res && $rec_res->num_rows > 0) {
                                while ($row = $rec_res->fetch_assoc()) {
                                    // Profile Photo Handling
                                    $photo = (!empty($row['photo']) && file_exists("../assets/images/students/" . $row['photo'])) 
                                        ? "../assets/images/students/" . $row['photo'] 
                                        : "../assets/images/user2.jpg";
                                    ?>
                                    <div class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-xl transition-colors">
                                        <img src="<?php echo $photo; ?>" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-gray-800 text-sm truncate"><?php echo htmlspecialchars($row['full_name']); ?></p>
                                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($row['stream']); ?> | Batch <?php echo $row['batch']; ?></p>
                                        </div>
                                        <span class="px-2 py-1 text-[10px] font-bold bg-green-100 text-green-700 rounded-full">New</span>
                                    </div>
                                <?php
                                }
                            } else {
                                echo "<p class='text-center text-gray-500 text-sm py-4'>No recent students found.</p>";
                            }
                            ?>
                        </div>
                        
                        <div class="p-4 border-t border-gray-100 bg-gray-50">
                            <button onclick="window.location.href='../log/registration.php'" class="w-full py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition">
                                Add Student Manually
                            </button>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            
            // PHP Data to JavaScript
            const labels = <?php echo json_encode($chart_labels); ?>;
            const data = <?php echo json_encode($chart_data, JSON_NUMERIC_CHECK); ?>; 

            let gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.3)');
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Income (LKR)',
                        data: data,
                        borderColor: '#4f46e5',
                        backgroundColor: gradient,
                        borderWidth: 2,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4f46e5',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Rs. ' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [4, 4], color: '#f3f4f6' },
                            ticks: { font: { size: 11 }, color: '#9ca3af' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#9ca3af' }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>