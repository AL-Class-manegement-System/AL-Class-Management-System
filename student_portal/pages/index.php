<?php
session_start();

// Connection එක නිවැරදිව ලබා ගැනීම (Path එක වෙනස් විය හැක, ඔබේ ෆෝල්ඩර ව්‍යුහය අනුව බලන්න)
require_once '../../includes/connection.php';

// Session Check
if (!isset($_SESSION['student_id'])) {
    header("Location: ../../log/login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// ශිෂ්‍ය තොරතුරු ලබා ගැනීම
$sql = "SELECT * FROM students WHERE reg_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result_student = $stmt->get_result();

if ($result_student->num_rows === 1) {
    $student = $result_student->fetch_assoc();

    // Profile Picture Logic
    $photo_url = $student['photo'];
    $image_path = "../../assets/images/students/" . $photo_url;
    
    if (!empty($photo_url) && file_exists($image_path)) {
        $profile_pic = $image_path;
    } else {
        $profile_pic = "https://ui-avatars.com/api/?name=" . urlencode($student['full_name']) . "&background=6366f1&color=fff";
    }

} else {
    header("Location: ../../log/login.php");
    exit();
}

include('../includes/student_header.php');
?>

<div class="flex-1 flex flex-col h-screen overflow-y-auto">

    <main class="p-4 md:p-8">

        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white mb-8 shadow-lg relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-2xl md:text-3xl font-bold mb-2">Welcome back,
                    <?php echo htmlspecialchars($student['full_name']) ?>! 👋
                </h1>
                <p class="opacity-90">You have <span class="font-bold text-yellow-300">2 assignments</span> due this week. Keep up the good work!</p>
            </div>
            <i class="fas fa-rocket absolute -bottom-4 -right-4 text-9xl text-white opacity-10 transform rotate-12"></i>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            <div class="md:col-span-2 relative overflow-hidden bg-gradient-to-br from-amber-300 via-yellow-500 to-amber-600 rounded-2xl p-6 text-white shadow-xl shadow-yellow-500/30 transform transition-all duration-300 border border-yellow-300/50">
                
                <div class="gold-shine-effect"></div>

                <div class="relative z-10 flex flex-col sm:flex-row justify-between items-center gap-6">
                    
                    <div class="text-center sm:text-left">
                        <p class="text-yellow-50 font-semibold text-sm uppercase tracking-wider">Student ID</p>
                        <h3 class="text-3xl sm:text-4xl font-extrabold mt-1 text-white drop-shadow-md tracking-widest">
                            <?php echo htmlspecialchars($student['reg_number']) ?>
                        </h3>
                        <p class="text-sm text-yellow-100 mt-2 font-medium">
                            <?php echo htmlspecialchars($student['full_name']) ?>
                        </p>
                        <span class="inline-block mt-3 px-3 py-1 bg-white/20 rounded-full text-xs backdrop-blur-sm border border-white/30">
                            Active Student
                        </span>
                    </div>

                    <div class="flex flex-col items-center gap-3 bg-white/20 p-4 rounded-xl backdrop-blur-md border border-white/30 shadow-inner">
                        <div id="qrcode" class="bg-white p-2 rounded-lg shadow-sm"></div>
                        <div class="bg-white px-2 py-1 rounded-lg shadow-sm mt-1">
                            <svg id="barcode" class="w-full h-8"></svg>
                        </div>
                    </div>

                </div>
            </div>

            <button class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-md group relative overflow-hidden border-2 border-transparent hover:border-indigo-400/60 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 to-white opacity-50"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 text-blue-600 rounded-lg group-hover:scale-110 transition-transform">
                            <i class="fas fa-video"></i>
                        </div>
                        <span class="text-xs font-bold text-red-500 bg-red-100 px-2 py-1 rounded animate-pulse">
                            🔴 Live
                        </span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">Live Class</h3>
                    <p class="text-indigo-600 text-sm font-medium mt-1 flex items-center gap-1">
                        Join Now <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </p>
                </div>
            </button>

            <div class="flex flex-col gap-6">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition flex-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-500 text-xs uppercase font-bold">Attendance</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1">85%</h3>
                        </div>
                        <div class="p-3 bg-green-100 text-green-600 rounded-lg">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition flex-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-500 text-xs uppercase font-bold">Next Class</p>
                            <h3 class="text-xl font-bold text-slate-800 mt-1">Mathematics</h3>
                            <p class="text-xs text-orange-500 font-medium">Starts in 30 mins</p>
                        </div>
                        <div class="p-3 bg-orange-100 text-orange-600 rounded-lg">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="font-bold text-lg text-slate-800 mb-6 flex items-center gap-2">
                    <i class="fas fa-calendar-day text-primary"></i> Today's Schedule
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center p-4 bg-slate-50 rounded-xl border-l-4 border-primary hover:bg-indigo-50 transition-colors cursor-pointer">
                        <div class="w-16 text-center border-r border-slate-200 pr-4">
                            <p class="text-sm font-bold text-slate-800">08:00</p>
                            <p class="text-xs text-slate-500">AM</p>
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="font-bold text-slate-800">Combined Mathematics</h4>
                            <p class="text-sm text-slate-500 flex items-center gap-2">
                                <i class="fas fa-user-tie text-xs"></i> Dr. Perera • <i class="fas fa-map-marker-alt text-xs"></i> Hall A
                            </p>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-600 text-xs font-bold rounded-full">Active</span>
                    </div>

                    <div class="flex items-center p-4 bg-white rounded-xl border border-slate-100 opacity-75">
                        <div class="w-16 text-center border-r border-slate-200 pr-4">
                            <p class="text-sm font-bold text-slate-800">10:30</p>
                            <p class="text-xs text-slate-500">AM</p>
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="font-bold text-slate-800">Physics</h4>
                            <p class="text-sm text-slate-500">Mr. Silva • Lab 02</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="font-bold text-lg text-slate-800 mb-6 flex items-center gap-2">
                    <i class="fas fa-bullhorn text-orange-500"></i> Notice Board
                </h3>
                <ul class="space-y-4">
                    <li class="pb-3 border-b border-slate-100 hover:bg-orange-50/50 p-2 rounded-lg transition">
                        <p class="text-sm font-semibold text-slate-700">Exam Timetable Released</p>
                        <p class="text-xs text-slate-400 mt-1"><i class="far fa-clock"></i> 2 hours ago</p>
                    </li>
                    <li class="pb-3 border-b border-slate-100 hover:bg-orange-50/50 p-2 rounded-lg transition">
                        <p class="text-sm font-semibold text-slate-700">School Closed on Friday</p>
                        <p class="text-xs text-slate-400 mt-1"><i class="far fa-clock"></i> Yesterday</p>
                    </li>
                    <li class="pt-2">
                        <button class="w-full py-2.5 text-sm text-primary font-semibold bg-indigo-50 hover:bg-indigo-100 rounded-xl transition flex items-center justify-center gap-2">
                            View All Notices <i class="fas fa-arrow-right"></i>
                        </button>
                    </li>
                </ul>
            </div>

        </div>

    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        // PHP Variable to JS
        var studentID = "<?php echo $student['reg_number']; ?>";

        // Check if ID exists before generating
        if(studentID) {
            // 1. Generate QR Code
            try {
                var qrcodeContainer = document.getElementById("qrcode");
                qrcodeContainer.innerHTML = ""; // Clear any previous code
                new QRCode(qrcodeContainer, {
                    text: studentID,
                    width: 70,
                    height: 70,
                    colorDark : "#000000",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.H
                });
            } catch (e) {
                console.error("QR Code Error:", e);
            }

            // 2. Generate Barcode
            try {
                JsBarcode("#barcode", studentID, {
                    format: "CODE128",
                    lineColor: "#000",
                    width: 1.5,
                    height: 30,
                    displayValue: false, // Hide the number below barcode (cleaner look)
                    background: "#ffffff",
                    margin: 0
                });
            } catch (e) {
                console.error("Barcode Error:", e);
            }
        }
    });
</script>

</body>
</html>