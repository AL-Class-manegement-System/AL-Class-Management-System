<?php include('db_con.php'); ?>

<?php
// 1. Total Students Count
$student_sql = "SELECT COUNT(*) as total FROM students";
$student_res = $conn->query($student_sql);
$student_count = $student_res->fetch_assoc()['total'];


// 4. Total Teachers
$teacher_sql = "SELECT COUNT(*) as total FROM teachers WHERE status = 1";
$teacher_res = $conn->query($teacher_sql);
$teacher_count = $teacher_res->fetch_assoc()['total'];

// 5. Recent 5 Students (New Widget Data)
$new_students_sql = "SELECT full_name, stream, photo FROM students ORDER BY registered_date DESC LIMIT 5";
$new_students_res = $conn->query($new_students_sql);

// 6. Chart Data (Last 6 Months Income)
$chart_labels = [];
$chart_data = [];

for ($i = 5; $i >= 0; $i--) {
    $month = date("Y-m", strtotime("-$i months"));
    $month_name = date("M", strtotime("-$i months"));
    
    $chart_sql = "SELECT SUM(amount) as total FROM payments WHERE DATE_FORMAT(paid_date, '%Y-%m') = '$month'";
    $chart_res = $conn->query($chart_sql);
    $row = $chart_res->fetch_assoc();
    
    $chart_labels[] = $month_name;
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
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <?php include('includes/sidebar.php'); ?>

    <div class="ml-64 flex flex-col min-h-screen transition-all duration-300">
        
        <header class="bg-white shadow-sm py-4 px-8 flex justify-between items-center sticky top-0 z-40">
            <h2 class="text-2xl font-bold text-gray-800">Dashboard Overview</h2>
            
            <div class="flex items-center gap-6">
                <div class="relative cursor-pointer hover:bg-gray-50 p-2 rounded-full transition">
                    <i class="fas fa-bell text-gray-500 text-xl hover:text-indigo-600"></i>
                    <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 rounded-full text-[10px] text-white flex items-center justify-center border border-white">3</span>
                </div>
                <div class="flex items-center gap-3 cursor-pointer">
                    <div class="text-right hidden md:block">
                        <div class="text-sm font-bold text-gray-700">Admin User</div>
                        <div class="text-xs text-gray-500">Administrator</div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold border-2 border-indigo-200 shadow-sm">
                        A
                    </div>
                </div>
            </div>
        </header>

        <main class="p-8 flex-1 overflow-y-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition hover:-translate-y-1">
                    <div class="w-14 h-14 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 text-2xl">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm">Total Students</div>
                        <div class="text-2xl font-bold text-gray-800"><?php echo number_format($student_count); ?></div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition hover:-translate-y-1">
                    <div class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center text-green-600 text-2xl">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm">Monthly Income</div>
                        <div class="text-2xl font-bold text-gray-800">Rs. <?php echo number_format($monthly_income); ?></div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition hover:-translate-y-1">
                    <div class="w-14 h-14 rounded-full bg-purple-50 flex items-center justify-center text-purple-600 text-2xl">
                        <i class="fas fa-chalkboard"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm">Active Classes</div>
                        <div class="text-2xl font-bold text-gray-800"><?php echo $class_count; ?></div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition hover:-translate-y-1">
                    <div class="w-14 h-14 rounded-full bg-pink-50 flex items-center justify-center text-pink-600 text-2xl">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm">Total Teachers</div>
                        <div class="text-2xl font-bold text-gray-800"><?php echo $teacher_count; ?></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Revenue Analytics (Last 6 Months)</h3>
                    <canvas id="revenueChart" height="150"></canvas>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">New Joiners</h3>
                    <div class="space-y-4">
                        <?php 
                        if ($new_students_res->num_rows > 0) {
                            while($student = $new_students_res->fetch_assoc()) {
                                $photo = !empty($student['photo']) ? "../assests/images/students/".$student['photo'] : "../assests/images/user2.jpg";
                        ?>
                        <div class="flex items-center gap-3 pb-3 border-b border-gray-100 last:border-0 last:pb-0">
                            <img src="<?php echo $photo; ?>" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                            <div class="flex-1">
                                <div class="font-semibold text-sm"><?php echo htmlspecialchars($student['full_name']); ?></div>
                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($student['stream']); ?></div>
                            </div>
                            <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded">New</span>
                        </div>
                        <?php 
                            }
                        } else {
                            echo "<p class='text-gray-500 text-sm'>No new students yet.</p>";
                        }
                        ?>
                    </div>
                    <a href="student.php" class="block w-full mt-6 py-2 text-center text-sm text-indigo-600 font-semibold bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">View All Students</a>
                </div>

            </div>

        </main>
    </div>

    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        // Data from PHP
        const labels = <?php echo json_encode($chart_labels); ?>;
        const data = <?php echo json_encode($chart_data); ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Income (LKR)',
                    data: data,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4], color: '#f3f4f6' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
</body>
</html>