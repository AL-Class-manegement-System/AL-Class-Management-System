<?php 
session_start();
include 'includes/auth.php';
include 'db_con.php'; 

// ==========================================
// 1. DATA FETCHING LOGIC
// ==========================================

// A. Total Students (Active Only)
$student_sql = "SELECT COUNT(*) as total FROM students WHERE status = 1";
$student_res = $conn->query($student_sql);
$student_count = $student_res->fetch_assoc()['total'];

// B. Monthly Income (Calculated from payments table based on paid_date)
$current_month = date('Y-m'); // Ex: 2025-12
$income_sql = "SELECT SUM(amount) as total FROM payments WHERE DATE_FORMAT(paid_date, '%Y-%m') = '$current_month'";
$income_res = $conn->query($income_sql);
$row_income = $income_res->fetch_assoc();
$monthly_income = $row_income['total'] ? $row_income['total'] : 0;

// C. Active Classes Count
$class_sql = "SELECT COUNT(*) as total FROM classes WHERE status = 1";
$class_res = $conn->query($class_sql);
$class_count = $class_res->fetch_assoc()['total'];

// D. Total Teachers
$teacher_sql = "SELECT COUNT(*) as total FROM teachers WHERE status = 1";
$teacher_res = $conn->query($teacher_sql);
$teacher_count = $teacher_res->fetch_assoc()['total'];

// E. Recent 5 Students
$new_students_sql = "SELECT * FROM students ORDER BY student_id DESC LIMIT 5";
$new_students_res = $conn->query($new_students_sql);

// F. Chart Data (Last 6 Months Income)
$chart_labels = [];
$chart_data = [];

for ($i = 5; $i >= 0; $i--) {
    $month_filter = date("Y-m", strtotime("-$i months")); // For DB Query
    $display_label = date("M", strtotime("-$i months"));  // For Chart Label
    
    $chart_sql = "SELECT SUM(amount) as total FROM payments WHERE DATE_FORMAT(paid_date, '%Y-%m') = '$month_filter'";
    $chart_res = $conn->query($chart_sql);
    $row = $chart_res->fetch_assoc();
    
    $chart_labels[] = $display_label;
    $chart_data[] = $row['total'] ? $row['total'] : 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Future Minds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style> 
        body { font-family: 'Poppins', sans-serif; } 
        .glass-effect { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <?php include('includes/sidebar.php'); ?>

    <div class="ml-64 flex flex-col min-h-screen transition-all duration-300">
        
        <header class="bg-white shadow-sm py-4 px-8 flex justify-between items-center sticky top-0 z-40 border-b border-gray-100">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Dashboard Overview</h2>
                <p class="text-xs text-gray-500">Welcome back, Admin!</p>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="relative cursor-pointer hover:bg-gray-50 p-2 rounded-full transition">
                    <i class="fas fa-bell text-gray-500 text-xl hover:text-indigo-600"></i>
                    <span class="absolute top-1 right-2 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                </div>
                <div class="flex items-center gap-3 cursor-pointer">
                    <div class="text-right hidden md:block">
                        <div class="text-sm font-bold text-gray-700"><?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin'; ?></div>
                        <div class="text-xs text-gray-500">Administrator</div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold shadow-md ring-2 ring-indigo-100">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-8 flex-1 overflow-y-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between hover:shadow-lg transition-all duration-300 group">
                    <div>
                        <div class="text-gray-500 text-sm font-medium mb-1">Total Students</div>
                        <div class="text-3xl font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">
                            <?php echo number_format($student_count); ?>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between hover:shadow-lg transition-all duration-300 group">
                    <div>
                        <div class="text-gray-500 text-sm font-medium mb-1">Monthly Income</div>
                        <div class="text-3xl font-bold text-gray-800 group-hover:text-green-600 transition-colors">
                            Rs. <?php echo number_format($monthly_income / 1000, 1); ?>k
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600 text-xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between hover:shadow-lg transition-all duration-300 group">
                    <div>
                        <div class="text-gray-500 text-sm font-medium mb-1">Active Classes</div>
                        <div class="text-3xl font-bold text-gray-800 group-hover:text-purple-600 transition-colors">
                            <?php echo $class_count; ?>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 text-xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-chalkboard"></i>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between hover:shadow-lg transition-all duration-300 group">
                    <div>
                        <div class="text-gray-500 text-sm font-medium mb-1">Total Teachers</div>
                        <div class="text-3xl font-bold text-gray-800 group-hover:text-pink-600 transition-colors">
                            <?php echo $teacher_count; ?>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-pink-50 flex items-center justify-center text-pink-600 text-xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Revenue Analytics</h3>
                        <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded">Last 6 Months</span>
                    </div>
                    <div class="relative h-72 w-full">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center justify-between">
                        <span>New Joiners</span>
                        <a href="student.php" class="text-xs text-indigo-600 hover:underline">View All</a>
                    </h3>
                    
                    <div class="space-y-4">
                        <?php 
                        if ($new_students_res->num_rows > 0) {
                            while($student = $new_students_res->fetch_assoc()) {
                                // Image handling
                                $photo_name = $student['photo'];
                                $photo_path = (!empty($photo_name) && file_exists("../assets/images/students/" . $photo_name)) 
                                    ? "../assets/images/students/" . $photo_name 
                                    : "../assets/images/user2.jpg";
                        ?>
                        <div class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg transition-colors cursor-pointer">
                            <img src="<?php echo $photo_path; ?>" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm">
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-sm text-gray-800 truncate"><?php echo htmlspecialchars($student['full_name']); ?></div>
                                <div class="text-xs text-gray-500 truncate"><?php echo htmlspecialchars($student['stream']); ?> | <?php echo $student['batch']; ?></div>
                            </div>
                            <span class="text-[10px] font-bold text-green-600 bg-green-100 px-2 py-0.5 rounded-full">New</span>
                        </div>
                        <?php 
                            }
                        } else {
                            echo "<div class='text-center py-8 text-gray-400 text-sm'>No new students found</div>";
                        }
                        ?>
                    </div>

                    <button onclick="window.location.href='../log/registration.php'" class="w-full mt-6 py-2.5 text-center text-sm text-white font-semibold bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-lg shadow-indigo-500/30 transition transform hover:-translate-y-0.5">
                        <i class="fas fa-plus mr-2"></i> Register New Student
                    </button>
                </div>

            </div>

        </main>
    </div>

    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        // PHP Arrays to JS
        const labels = <?php echo json_encode($chart_labels); ?>;
        const data = <?php echo json_encode($chart_data); ?>;

        // Gradient for chart
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
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
                        cornerRadius: 8,
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
                        ticks: { font: { size: 11, family: 'Poppins' }, color: '#9ca3af' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, family: 'Poppins' }, color: '#9ca3af' }
                    }
                }
            }
        });
    </script>
</body>
</html>