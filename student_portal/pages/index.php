<?php

include('../includes/student_header.php');

// ==========================================
// 1. Database Query Logic for Dashboard Data
// ==========================================
$db_student_id = $student['student_id'];
date_default_timezone_set('Asia/Colombo'); // වේලා කලාපය සකසන්න

// --- Helper functions for Dashboard ---

$current_day = date('l'); // e.g., 'Monday'
$today_classes = [];
$next_class = null;

// a) Next Class and Today's Schedule (Prepared Statement - දෝෂය නිවැරදි කර ඇත)
// අද දිනට අදාළ ශිෂ්‍යයා Enroll වී ඇති සියලුම පන්ති වේලාව අනුව වර්ග කර ලබා ගැනීම
$sql_today_classes = "
    SELECT 
        c.subject, 
        c.time, 
        c.hall_number,
        c.teacher_name
    FROM enrollments e
    JOIN classes c ON e.class_id = c.class_id
    WHERE e.student_id = ? AND c.day = ?
    ORDER BY c.time ASC
";
$stmt_today = $conn->prepare($sql_today_classes);

// Fatal error fix: prepare සාර්ථක දැයි පරීක්ෂා කිරීම
if ($stmt_today) { 
    $stmt_today->bind_param("is", $db_student_id, $current_day);
    if ($stmt_today->execute()) { 
        $today_result = $stmt_today->get_result();

        while ($row = $today_result->fetch_assoc()) {
            $start_timestamp = strtotime($row['time']);
            $end_timestamp = $start_timestamp + (90 * 60); // පන්ති කාලය මිනිත්තු 90ක් ලෙස උපකල්පනය කරයි.
        
            $status = 'Upcoming';
            if (time() >= $start_timestamp && time() < $end_timestamp) {
                $status = 'Active';
            } elseif (time() >= $end_timestamp) {
                $status = 'Finished';
            } elseif ($start_timestamp - time() <= (30 * 60)) {
                $status = 'Starting Soon';
            }
            
            $row['status'] = $status;
            $row['time_display'] = date('h:i', $start_timestamp);
            $row['ampm'] = date('A', $start_timestamp);
            $today_classes[] = $row;
            
            // Next Class සොයා ගැනීම
            if (!$next_class && $status !== 'Finished') {
                $next_class = $row;
                $diff_seconds = $start_timestamp - time();
                if ($diff_seconds > 0) {
                    $minutes_left = ceil($diff_seconds / 60);
                    $next_class['time_left_msg'] = "Starts in " . $minutes_left . " mins";
                } elseif ($status == 'Active') {
                    $next_class['time_left_msg'] = "Ongoing Now";
                } else {
                    $next_class['time_left_msg'] = "Starting Soon";
                }
            }
        }
    }
    $stmt_today->close();
}

// b) Attendance Data (Prepared Statement)
$attendance_percentage = 'N/A';
$attendance_sql = "SELECT AVG(status) * 100 AS percentage FROM attendance WHERE student_id = ?";
$stmt_attendance = $conn->prepare($attendance_sql);

if ($stmt_attendance) { // Check if prepare succeeded
    $stmt_attendance->bind_param("i", $db_student_id);
    if ($stmt_attendance->execute()) {
        $att_result = $stmt_attendance->get_result();
        if ($att_result->num_rows > 0) {
            $row = $att_result->fetch_assoc();
            if ($row['percentage'] !== null) {
                $attendance_percentage = number_format($row['percentage'], 0) . '%';
            }
        }
    }
    $stmt_attendance->close();
}

// දත්ත අනුව වර්ණ සහ තත්වය සැකසීම
$attendance_status_text = 'Check your classes';
$attendance_color = 'text-gray-500 bg-gray-100';
if ($attendance_percentage !== 'N/A') {
    $percent = (int)str_replace('%', '', $attendance_percentage);
    if ($percent >= 80) {
        $attendance_status_text = 'Excellent';
        $attendance_color = 'text-green-500 bg-green-100';
    } elseif ($percent >= 60) {
        $attendance_status_text = 'Good';
        $attendance_color = 'text-orange-500 bg-orange-100';
    } else {
        $attendance_status_text = 'Needs Improvement';
        $attendance_color = 'text-red-500 bg-red-100';
    }
}


// c) Notice Board Data (Prepared Statement)
$notices = [];
$sql_notices = "SELECT title, created_at FROM notices WHERE status = 1 ORDER BY created_at DESC LIMIT 3";
$stmt_notices = $conn->prepare($sql_notices);

if ($stmt_notices) { // Check if prepare succeeded
    if ($stmt_notices->execute()) {
        $notices_result = $stmt_notices->get_result();

        while ($row = $notices_result->fetch_assoc()) {
            // 'time ago' ගණනය කිරීම
            $time_ago = time() - strtotime($row['created_at']);
            if ($time_ago < 60) $time_display = $time_ago . " seconds ago";
            elseif ($time_ago < 3600) $time_display = floor($time_ago / 60) . " minutes ago";
            elseif ($time_ago < 86400) $time_display = floor($time_ago / 3600) . " hours ago";
            else $time_display = date('M d, Y', strtotime($row['created_at'])); 

            $row['time_display'] = $time_display;
            $notices[] = $row;
        }
    }
    $stmt_notices->close();
}
?>

<div class="flex-1 flex flex-col h-screen overflow-y-auto">

    <main class="p-4 pt-0 mt-0 md:p-8">

        <div
            class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white mb-8 shadow-lg relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-2xl md:text-3xl font-bold mb-2">     Welcome back,
                    <?php echo htmlspecialchars($student['full_name']) ?>! 👋
                </h1>
                
            </div>
            <i class="fas fa-rocket absolute -bottom-4 -right-4 text-9xl text-white opacity-10 transform rotate-12"></i>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            <div
                class=" opacity-80 md:col-span-2 relative overflow-hidden bg-gradient-to-br from-amber-300 via-yellow-500 to-amber-600 rounded-2xl p-6 text-white shadow-xl shadow-yellow-500/30 transform transition-all duration-300 border border-yellow-300/50">

                <div class="gold-shine-effect"></div>

                <div class="relative z-10 flex flex-col sm:flex-row justify-between items-center gap-6 opacity-100">

                    <div class="text-center sm:text-left">
                        <p class="text-yellow-50 font-semibold text-sm uppercase tracking-wider">Student ID</p>
                        <h3 class="text-3xl sm:text-4xl font-extrabold mt-1 text-white drop-shadow-md tracking-widest">
                            <?php echo htmlspecialchars($student['reg_number']) ?>
                        </h3>
                        <p class="text-sm text-yellow-100 mt-2 font-medium">
                            <?php echo htmlspecialchars($student['full_name']) ?>
                        </p>
                        <span
                            class="inline-block mt-3 px-3 py-1 bg-white/20 rounded-full text-xs backdrop-blur-sm border border-white/30">
                            Active Student
                        </span>
                    </div>

                    <div
                        class="flex flex-col items-center gap-3 bg-white/20 p-4 rounded-xl backdrop-blur-md border border-white/30 shadow-inner">
                        <div id="qrcode" class="bg-white p-2 rounded-lg shadow-sm"></div>
                        <div class="bg-white px-2 py-1 rounded-lg shadow-sm mt-1">
                            <svg id="barcode" class="w-full h-8"></svg>
                        </div>
                        
                        <button onclick="downloadQRCode()" 
                            class="mt-3 px-4 py-2 bg-white text-indigo-600 text-xs font-bold rounded-lg hover:bg-indigo-50 transition-colors shadow-md transform active:scale-95">
                            <i class="fas fa-download mr-1"></i> Download QR
                        </button>
                    </div>

                </div>
            </div>
            <a href="../pages/live_class.php">
                <div
                    class="bg-slate-900/90 p-6 rounded-2xl shadow-xl border border-indigo-700/50 hover:shadow-indigo-500/50 group relative overflow-hidden transition-all duration-300 ease-out cursor-pointer hover:scale-[1.02] active:scale-95">
                    <div class="relative z-10 flex flex-col h-full justify-between">
                        <div class="p-3 bg-indigo-500/10 text-white rounded-lg backdrop-blur-sm border border-indigo-500/20">
                            <i class="fas fa-book-open"></i>
                            <h3 class="text-2xl font-bold text-white mt-1">🔴Live Classes</h3>
                        </div>

                        <p class="text-indigo-400 text-sm font-medium mt-4 flex items-center gap-1">
                            Join Now <i
                                class="fas fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                        </p>
                    </div>
                    <div
                        class="absolute -top-10 -right-10 w-32 h-32 bg-indigo-400 rounded-full opacity-10 group-hover:opacity-20 transition-opacity transform group-hover:scale-110">
                    </div>

                </div>
            </a>

        </div>



        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-100 text-green-600 rounded-lg"><i class="fas fa-user-check"></i>
                    </div>
                    <span class="text-xs font-bold <?php echo $attendance_color; ?> px-2 py-1 rounded"><?php echo $attendance_status_text; ?></span>
                </div>
                <h3 class="text-2xl font-bold text-slate-800"><?php echo $attendance_percentage; ?></h3>
                <p class="text-slate-500 text-sm">Attendance</p>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition lg:col-span-2">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-xs uppercase font-bold">Next Class</p>
                        <h3 class="text-xl font-bold text-slate-800 mt-1">
                            <?php echo $next_class ? htmlspecialchars($next_class['subject']) : 'No Class Scheduled'; ?>
                        </h3>
                        <p class="text-xs <?php echo $next_class && $next_class['status'] == 'Active' ? 'text-green-500' : 'text-orange-500'; ?> font-medium">
                            <?php echo $next_class ? htmlspecialchars($next_class['time_left_msg']) : 'Check Time Table'; ?>
                        </p>
                    </div>
                    <div class="p-3 bg-orange-100 text-orange-600 rounded-lg">
                        <i class="fas fa-clock"></i>
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
                    
                    <?php if (!empty($today_classes)): ?>
                    <?php foreach ($today_classes as $class):
                        if ($class['status'] === 'Finished') continue; 
                        
                        $border_color = 'border-slate-200';
                        $bg_color = 'bg-white';
                        $text_color = 'text-slate-800';
                        $status_badge = 'bg-gray-100 text-gray-600';

                        if ($class['status'] === 'Active') {
                            $border_color = 'border-green-500';
                            $bg_color = 'bg-green-50/70';
                            $status_badge = 'bg-green-100 text-green-600';
                        } elseif ($class['status'] === 'Starting Soon') {
                            $border_color = 'border-orange-500';
                            $bg_color = 'bg-orange-50/70';
                            $status_badge = 'bg-orange-100 text-orange-600';
                        } elseif ($class['status'] === 'Upcoming') {
                            $border_color = 'border-primary';
                            $bg_color = 'bg-indigo-50/70';
                            $status_badge = 'bg-indigo-100 text-indigo-600';
                        }
                    ?>
                    <a href="live_class.php"
                        class="flex items-center p-4 <?php echo $bg_color; ?> rounded-xl border-l-4 <?php echo $border_color; ?> hover:bg-indigo-50 transition-colors cursor-pointer">
                        <div class="w-16 text-center border-r border-slate-200 pr-4">
                            <p class="text-sm font-bold <?php echo $text_color; ?>"><?php echo htmlspecialchars($class['time_display']); ?></p>
                            <p class="text-xs text-slate-500"><?php echo htmlspecialchars($class['ampm']); ?></p>
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="font-bold text-slate-800"><?php echo htmlspecialchars($class['subject']); ?></h4>
                            <p class="text-sm text-slate-500 flex items-center gap-2">
                                <i class="fas fa-user-tie text-xs"></i> <?php echo htmlspecialchars($class['teacher_name']); ?> • <i
                                    class="fas fa-map-marker-alt text-xs"></i> <?php echo htmlspecialchars($class['hall_number'] ?? 'Online'); ?>
                            </p>
                        </div>
                        <span class="px-3 py-1 <?php echo $status_badge; ?> text-xs font-bold rounded-full">
                            <?php echo htmlspecialchars($class['status']); ?>
                        </span>
                    </a>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="p-4 text-center text-slate-500">No classes scheduled for today.</div>
                    <?php endif; ?>

                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="font-bold text-lg text-slate-800 mb-6 flex items-center gap-2">
                    <i class="fas fa-bullhorn text-orange-500"></i> Notice Board
                </h3>
                <ul class="space-y-4">
                    <?php if (!empty($notices)): ?>
                        <?php foreach ($notices as $notice): ?>
                        <li class="pb-3 border-b border-slate-100 hover:bg-orange-50/50 p-2 rounded-lg transition">
                            <p class="text-sm font-semibold text-slate-700"><?php echo htmlspecialchars($notice['title']); ?></p>
                            <p class="text-xs text-slate-400 mt-1"><i class="far fa-clock"></i> <?php echo $notice['time_display']; ?></p>
                        </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="text-center text-slate-500 text-sm">No recent notices.</li>
                    <?php endif; ?>
                    
                    <li class="pt-2">
                        <button
                            class="w-full py-2.5 text-sm text-primary font-semibold bg-indigo-50 hover:bg-indigo-100 rounded-xl transition flex items-center justify-center gap-2">
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
    // QR Code Download Function - Global access සඳහා DOMContentLoaded Event එකෙන් පිටත තබනු ලැබේ.
    function downloadQRCode() {
        const qrcodeDiv = document.getElementById('qrcode');
        // qrcodejs මගින් div එක ඇතුළත canvas element එකක් නිර්මාණය කරයි.
        const canvas = qrcodeDiv.querySelector('canvas'); 
        
        if (!canvas) {
            alert('QR code not ready or not found!');
            return;
        }

        // Canvas එක PNG data URL එකකට පරිවර්තනය කිරීම
        const dataURL = canvas.toDataURL('image/png'); 
        
        // Download කිරීම සඳහා තාවකාලික a element එකක් නිර්මාණය කිරීම
        const a = document.createElement('a');
        a.href = dataURL;
        // ගොනු නාමය Student ID එක අනුව සකස් කිරීම
        a.download = '<?php echo $student['reg_number']; ?>_QR_Code.png';
        
        // Body එකට එකතු කර, click කර, ඉවත් කිරීම
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
    
    document.addEventListener("DOMContentLoaded", function () {
        // PHP Variable to JS
        var studentID = "<?php echo $student['reg_number']; ?>";

        // Check if ID exists before generating
        if (studentID) {
            // 1. Generate QR Code
            try {
                var qrcodeContainer = document.getElementById("qrcode");
                qrcodeContainer.innerHTML = ""; // Clear any previous code
                new QRCode(qrcodeContainer, {
                    text: studentID,
                    width: 70,
                    height: 70,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
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
<?php include("../includes/footer.php"); ?>