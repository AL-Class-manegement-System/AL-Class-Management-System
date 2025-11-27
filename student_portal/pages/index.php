<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['reg_number'])) {
    header("Location: ../../log/login.php"); // Login නැත්නම් එළියට යවන්න
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex h-screen overflow-hidden">

        <aside class="w-64 bg-blue-900 text-white hidden md:flex flex-col">
            <div class="h-16 flex items-center justify-center font-bold text-xl border-b border-blue-800">
                LMS Portal
            </div>
            <nav class="flex-1 px-2 py-4 space-y-2">
                <a href="#" class="flex items-center px-4 py-3 bg-blue-800 rounded-md transition duration-200">
                    <i class="fas fa-home w-6"></i> Dashboard
                </a>
                <a href="#" class="flex items-center px-4 py-3 hover:bg-blue-700 rounded-md transition duration-200">
                    <i class="fas fa-book w-6"></i> My Courses
                </a>
                <a href="#" class="flex items-center px-4 py-3 hover:bg-blue-700 rounded-md transition duration-200">
                    <i class="fas fa-chart-bar w-6"></i> Results
                </a>
                <a href="#" class="flex items-center px-4 py-3 hover:bg-blue-700 rounded-md transition duration-200">
                    <i class="fas fa-calendar-check w-6"></i> Attendance
                </a>
                <a href="#" class="flex items-center px-4 py-3 hover:bg-blue-700 rounded-md transition duration-200">
                    <i class="fas fa-user w-6"></i> Profile
                </a>
            </nav>
            <div class="p-4 border-t border-blue-800">
                <a href="logout.php" class="flex items-center px-4 py-2 hover:bg-red-600 rounded-md transition duration-200">
                    <i class="fas fa-sign-out-alt w-6"></i> Logout
                </a>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            
            <header class="h-16 bg-white shadow flex items-center justify-between px-6">
                <div class="text-gray-500 text-sm">
                    Welcome back, <span class="font-bold text-gray-800"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($_SESSION['reg_number']); ?></div>
                        <div class="text-xs text-gray-500">Student</div>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['full_name']); ?>&background=random" alt="Profile" class="h-10 w-10 rounded-full">
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                
                <h3 class="text-2xl font-semibold text-gray-700 mb-6">Overview</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                                <i class="fas fa-book-open fa-lg"></i>
                            </div>
                            <div class="ml-4">
                                <p class="mb-2 text-sm font-medium text-gray-600">Enrolled Courses</p>
                                <p class="text-lg font-semibold text-gray-700">04</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-500">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                            <div class="ml-4">
                                <p class="mb-2 text-sm font-medium text-gray-600">Attendance</p>
                                <p class="text-lg font-semibold text-gray-700">85%</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-yellow-500">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                                <i class="fas fa-bell fa-lg"></i>
                            </div>
                            <div class="ml-4">
                                <p class="mb-2 text-sm font-medium text-gray-600">Notifications</p>
                                <p class="text-lg font-semibold text-gray-700">03</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-purple-500">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                                <i class="fas fa-graduation-cap fa-lg"></i>
                            </div>
                            <div class="ml-4">
                                <p class="mb-2 text-sm font-medium text-gray-600">Upcoming Exam</p>
                                <p class="text-lg font-semibold text-gray-700">Physics</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h4 class="text-lg font-semibold text-gray-700 mb-4">Recent Announcements</h4>
                        <ul class="space-y-4">
                            <li class="border-b pb-3">
                                <p class="text-sm font-medium text-gray-800">Chemistry Class Rescheduled</p>
                                <p class="text-xs text-gray-500">Yesterday at 4:00 PM</p>
                            </li>
                            <li class="border-b pb-3">
                                <p class="text-sm font-medium text-gray-800">Exam Timetable Released</p>
                                <p class="text-xs text-gray-500">2 days ago</p>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h4 class="text-lg font-semibold text-gray-700 mb-4">Quick Links</h4>
                        <div class="grid grid-cols-2 gap-4">
                             <button class="p-4 bg-gray-50 rounded text-center hover:bg-blue-50 transition">
                                <i class="fas fa-download text-blue-500 mb-2"></i>
                                <div class="text-sm font-medium">Download Materials</div>
                             </button>
                             <button class="p-4 bg-gray-50 rounded text-center hover:bg-blue-50 transition">
                                <i class="fas fa-video text-red-500 mb-2"></i>
                                <div class="text-sm font-medium">Join Live Class</div>
                             </button>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
</body>
</html>