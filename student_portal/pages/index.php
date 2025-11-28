<?php
session_start();

require_once '../../includes/connection.php';


$student_id = $_SESSION['student_id'];
$full_name = $_SESSION['full_name'];

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$sql = "SELECT * FROM students WHERE reg_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();

$student = $stmt->get_result();

if($student->num_rows === 1) {
    $student = $student->fetch_assoc();
} else {
    // Handle case where student is not found
    header("Location: ../log/login.php");
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

<body class="bg-gray-200 font-sans">

    <div class="flex h-screen overflow-hidden">

        <aside class="w-64 bg-slate-900 text-white hidden md:flex flex-col shadow-xl z-20">
            <div class="h-20 flex items-center px-8 border-b border-slate-700">
                <i class="fas fa-graduation-cap text-3xl text-primary mr-3"></i>
                <span class="text-xl font-bold tracking-wide">MySchool</span>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 ">
                <a href="#"
                    class="flex items-center px-4 py-3 bg-primary text-white rounded-xl transition-transform transform hover:scale-105 shadow-lg shadow-indigo-500/30">
                    <i class="fas fa-th-large w-6"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="#"
                    class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all">
                    <i class="fas fa-book w-6"></i>
                    <span class="font-medium">My Courses</span>
                </a>

                <a href="#"
                    class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all">
                    <i class="fas fa-clipboard-list w-6"></i>
                    <span class="font-medium">Exam Results</span>
                </a>

                <a href="#"
                    class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all">
                    <i class="fas fa-calendar-alt w-6"></i>
                    <span class="font-medium">Time Table</span>
                </a>
            </nav>

            <div class="p-4 border-t border-slate-700">
                <a href="../../lib/logout.php"
                    class="flex items-center px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-xl transition-all">
                    <i class="fas fa-sign-out-alt w-6"></i>
                    <span class="font-medium">Logout</span>
                </a>
            </div>
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-y-auto">

            <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 sticky top-0 z-10">
                <div class="md:hidden">
                    <button class="text-slate-500 hover:text-primary"><i class="fas fa-bars text-2xl"></i></button>
                </div>

                <h2 class="text-2xl font-bold text-slate-800 hidden md:block">Dashboard</h2>

                <div class="flex items-center gap-4">
                    <button
                        class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:text-primary transition relative">
                        <i class="fas fa-bell"></i>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <div class="flex items-center gap-3 pl-4 border-l">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-slate-700"><?php echo htmlspecialchars($student['full_name']); ?></p>
                            <p class="text-xs text-slate-500"><?php echo htmlspecialchars($student_id); ?></p>
                        </div>
                        <img src="<?php echo urlencode($student['photo']); ?>&background=6366f1&color=fff"
                            class="w-10 h-10 rounded-full border-2 border-slate-200">
                    </div>
                </div>
            </header>

            <main class="p-8">

                <div
                    class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white mb-8 shadow-lg relative overflow-hidden opacity-80">
                    <div class="relative z-10">
                        <h1 class="text-3xl font-bold mb-2">Welcome back, <?php echo htmlspecialchars($student['full_name']) ?>! 👋
                        </h1>
                        <p class="opacity-90">You have <span class="font-bold text-yellow-300">2 assignments</span> due
                            this week. Keep up the good work!</p>
                    </div>
                    <i
                        class="fas fa-rocket absolute -bottom-4 -right-4 text-9xl text-white opacity-10 transform rotate-12"></i>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-blue-100 text-blue-600 rounded-lg"><i class="fas fa-book-open"></i></div>
                            <span class="text-xs font-bold text-green-500 bg-green-100 px-2 py-1 rounded">+2 new</span>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-800">06</h3>
                        <p class="text-slate-500 text-sm">Enrolled Subjects</p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-green-100 text-green-600 rounded-lg"><i class="fas fa-user-check"></i>
                            </div>
                            <span class="text-xs font-bold text-green-500 bg-green-100 px-2 py-1 rounded">Good</span>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-800">85%</h3>
                        <p class="text-slate-500 text-sm">Attendance</p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-purple-100 text-purple-600 rounded-lg"><i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-800">3.8</h3>
                        <p class="text-slate-500 text-sm">Current GPA</p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-orange-100 text-orange-600 rounded-lg"><i class="fas fa-clock"></i></div>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-800">Next: Math</h3>
                        <p class="text-slate-500 text-sm">Starts in 30 mins</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                        <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-calendar-day text-primary"></i> Today's Schedule
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-center p-4 bg-slate-50 rounded-xl border-l-4 border-primary">
                                <div class="w-16 text-center">
                                    <p class="text-sm font-bold text-slate-800">08:00</p>
                                    <p class="text-xs text-slate-500">AM</p>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-bold text-slate-800">Combined Mathematics</h4>
                                    <p class="text-sm text-slate-500">Dr. Perera • Hall A</p>
                                </div>
                                <span
                                    class="ml-auto px-3 py-1 bg-green-100 text-green-600 text-xs font-bold rounded-full">Active</span>
                            </div>

                            <div
                                class="flex items-center p-4 bg-slate-50 rounded-xl border-l-4 border-slate-300 opacity-75">
                                <div class="w-16 text-center">
                                    <p class="text-sm font-bold text-slate-800">10:30</p>
                                    <p class="text-xs text-slate-500">AM</p>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-bold text-slate-800">Physics</h4>
                                    <p class="text-sm text-slate-500">Mr. Silva • Lab 02</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                        <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-bullhorn text-orange-500"></i> Notice Board
                        </h3>
                        <ul class="space-y-4">
                            <li class="pb-3 border-b border-slate-100">
                                <p class="text-sm font-semibold text-slate-700">Exam Timetable Released</p>
                                <p class="text-xs text-slate-400 mt-1">2 hours ago</p>
                            </li>
                            <li class="pb-3 border-b border-slate-100">
                                <p class="text-sm font-semibold text-slate-700">School Closed on Friday</p>
                                <p class="text-xs text-slate-400 mt-1">Yesterday</p>
                            </li>
                            <li>
                                <button
                                    class="w-full py-2 text-sm text-primary font-semibold hover:bg-slate-50 rounded-lg transition">View
                                    All Notices</button>
                            </li>
                        </ul>
                    </div>

                </div>

            </main>
        </div>
    </div>
</body>

</html>