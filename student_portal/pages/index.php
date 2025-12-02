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

if ($student->num_rows === 1) {
    $student = $student->fetch_assoc();

    $photo_url = $student['photo'];
    $image_path = "../../assets/images/students/" . $photo_url;
    if (!empty($$photo_url) && file_exists($image_path)) {
        // ෆොටෝ එක තියෙනවා නම් ඒක ගන්න
        $profile_pic = $image_path;
    } else {
        // ෆොටෝ එකක් නැත්නම්, නමේ අකුරු වලින් හැදෙන පින්තූරයක් (Default) ගන්න
        $profile_pic = "https://ui-avatars.com/api/?name=" . urlencode($student['full_name']) . "&background=6366f1&color=fff";
    }

} else {
    // Handle case where student is not found
    header("Location: ../log/login.php");
    exit();
}

include('../includes/student_header.php');

?>


<div class="flex-1 flex flex-col h-screen overflow-y-auto">

    <main class="p-8">

        <div
            class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white mb-8 shadow-lg relative overflow-hidden opacity-80">
            <div class="relative z-10">
                <h1 class="text-3xl font-bold mb-2">Welcome back,
                    <?php echo htmlspecialchars($student['full_name']) ?>! 👋
                </h1>
                <p class="opacity-90">You have <span class="font-bold text-yellow-300">2 assignments</span>
                    due
                    this week. Keep up the good work!</p>
            </div>
            <i class="fas fa-rocket absolute -bottom-4 -right-4 text-9xl text-white opacity-10 transform rotate-12"></i>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 ">
            <div
                class=" relative overflow-hidden bg-gradient-to-br from-amber-300 via-yellow-500 to-amber-600 rounded-2xl p-6 text-white shadow-xl shadow-yellow-500/30 transform  transition-all duration-300 border border-yellow-300/50 p-3">

                <div class="gold-shine-effect"></div>

                <div class="relative z-10 flex justify-between  items-center justify-center">
                    <div>
                        <p class="text-yellow-50 font-semibold text-sm uppercase tracking-wider">Your id
                        </p>
                        <h3 class="text-4xl font-extrabold mt-1 text-white drop-shadow-md">
                            <?php echo htmlspecialchars($student['reg_number']) ?>
                        </h3>
                        <p class="text-xs text-yellow-100 mt-1">Active Subjects</p>
                    </div>

                </div>


            </div>

        </div>



        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">


            <button
                class="bg-white p-6 rounded-2xl shadow-sm  hover:shadow-md group relative p-4 rounded-2xl backdrop-blur-xl border-2 border-indigo-800/30 bg-gradient-to-br from-white-500/40 via-black-500/60 to-black/80 shadow-2xl hover:shadow-indigo-500/30 hover:shadow-2xl hover:scale-[1.02] hover:-translate-y-1 active:scale-95 transition-all duration-500 ease-out cursor-pointer hover:border-indigo-400/60 overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-100 text-blue-600 rounded-lg"><i class="fas fa-book-open"></i>
                    </div>
                    <span class="text-xs font-bold text-red-500 bg-red-100 px-2 py-1 rounded">
                        🔴 Live</span>
                </div>
                
                <h3 class="text-2xl font-bold text-slate-800">Live Class</h3>
                <p class="text-blue-500 text-sm">join now</p>
                
            </button>

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
                    <div class="p-3 bg-orange-100 text-orange-600 rounded-lg"><i class="fas fa-clock"></i>
                    </div>
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

                    <div class="flex items-center p-4 bg-slate-50 rounded-xl border-l-4 border-slate-300 opacity-75">
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
</div>
</body>

</html>