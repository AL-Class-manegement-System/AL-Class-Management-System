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
    </style>
</head>
<body class="bg-gray-100">

    <?php include('includes/sidebar.php'); ?>

    <div class="ml-64 flex flex-col min-h-screen transition-all duration-300">
        
        <header class="bg-white shadow-sm py-4 px-8 flex justify-between items-center sticky top-0 z-40">
            <h2 class="text-2xl font-bold text-gray-800">Dashboard Overview</h2>
            
            <div class="flex items-center gap-6">
                <div class="relative">
                    <i class="fas fa-bell text-gray-500 text-xl cursor-pointer hover:text-indigo-600"></i>
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-[10px] text-white flex items-center justify-center">3</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right hidden md:block">
                        <div class="text-sm font-bold text-gray-700">Admin User</div>
                        <div class="text-xs text-gray-500">Administrator</div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold border border-indigo-200">
                        A
                    </div>
                </div>
            </div>
        </header>

        <main class="p-8 flex-1 overflow-y-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                    <div class="w-14 h-14 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 text-2xl">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm">Total Students</div>
                        <div class="text-2xl font-bold text-gray-800">1,250</div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                    <div class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center text-green-600 text-2xl">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm">Monthly Income</div>
                        <div class="text-2xl font-bold text-gray-800">LKR 4.5M</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                    <div class="w-14 h-14 rounded-full bg-purple-50 flex items-center justify-center text-purple-600 text-2xl">
                        <i class="fas fa-chalkboard"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm">Active Classes</div>
                        <div class="text-2xl font-bold text-gray-800">24</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                    <div class="w-14 h-14 rounded-full bg-pink-50 flex items-center justify-center text-pink-600 text-2xl">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm">Total Teachers</div>
                        <div class="text-2xl font-bold text-gray-800">12</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Revenue Analytics (2025)</h3>
                    <canvas id="revenueChart" height="150"></canvas>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">New Students</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                            <img src="../assests/images/user2.jpg" class="w-10 h-10 rounded-full object-cover">
                            <div class="flex-1">
                                <div class="font-semibold text-sm">Kasun Perera</div>
                                <div class="text-xs text-gray-500">Physical Science - 2026</div>
                            </div>
                            <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded">New</span>
                        </div>
                        <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-sm">Amaya Silva</div>
                                <div class="text-xs text-gray-500">Bio Science - 2025</div>
                            </div>
                            <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded">New</span>
                        </div>
                         <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-sm">Nimali Fernando</div>
                                <div class="text-xs text-gray-500">Tech Stream - 2026</div>
                            </div>
                            <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded">New</span>
                        </div>
                    </div>
                    <button class="w-full mt-6 py-2 text-sm text-indigo-600 font-semibold bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">View All Students</button>
                </div>

            </div>

        </main>
    </div>

    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Income (LKR)',
                    data: [1200000, 1900000, 3000000, 2500000, 2000000, 4500000],
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
                }
            }
        });
    </script>
</body>
</html>