<?php
// student_portal/pages/submit_exam.php
// Fixed Version: Corrected 'attempted_at' column name & Added Error Handling

include('../includes/student_header.php');

// ==========================================
// 1. Request Method Verification
// ==========================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>window.location.href='exam_center.php';</script>";
    exit();
}

// දත්ත ලබා ගැනීම සහ පිරිසිදු කිරීම
$exam_id = isset($_POST['exam_id']) ? intval($_POST['exam_id']) : 0;
$student_db_id = $student['student_id']; // DB ID from Session
$student_reg_no = $student['reg_number']; // Registration Number for SMS

// Exam ID එක වලංගුදැයි බැලීම
if ($exam_id <= 0) {
    die("Invalid Exam ID.");
}

// ==========================================
// 2. SECURITY CHECK: One Attempt Only
// ==========================================
// සිසුවා මෙම විභාගය කලින් ලියා ඇත්දැයි පරීක්ෂා කිරීම
$check_sql = "SELECT * FROM exam_results WHERE student_id = ? AND exam_id = ?";
$check_stmt = $conn->prepare($check_sql);
if ($check_stmt) {
    $check_stmt->bind_param("ii", $student_db_id, $exam_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // දැනටමත් ලියා ඇත්නම් Error එකක් පෙන්වා Redirect කරයි
        echo "<script>alert('Error: You have already submitted this exam!'); window.location.href='exam_center.php';</script>";
        exit();
    }
    $check_stmt->close();
} else {
    die("Database Error (Check): " . $conn->error);
}

// ==========================================
// 3. Score Calculation Logic
// ==========================================
$score = 0;
$total_questions = 0;

// විභාගයට අදාළ ප්‍රශ්න සහ නිවැරදි පිළිතුරු ලබා ගැනීම
$q_sql = "SELECT question_id, correct_option FROM exam_questions WHERE exam_id = $exam_id";
$result = $conn->query($q_sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $total_questions++;
        $qid = $row['question_id'];
        $correct_opt = $row['correct_option'];

        // සිසුවා ලකුණු කළ පිළිතුර පරීක්ෂා කිරීම
        if (isset($_POST['q_' . $qid])) {
            $student_ans = intval($_POST['q_' . $qid]);
            if ($student_ans == $correct_opt) {
                $score++;
            }
        }
    }
}

// ප්‍රතිශතය ගණනය (Percentage)
$percentage = ($total_questions > 0) ? round(($score / $total_questions) * 100, 2) : 0;

// ==========================================
// 4. Save Results to Database (FIXED HERE)
// ==========================================
// Correction: Changed 'attempt_date' to 'attempted_at'
$save_sql = "INSERT INTO exam_results (exam_id, student_id, score, total_questions, attempted_at) VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($save_sql);

// ERROR HANDLING: Prepare එක fail වුනොත් දෝෂය පෙන්වීම
if ($stmt === false) {
    die("Database Insert Error: " . $conn->error);
}

$stmt->bind_param("iiii", $exam_id, $student_db_id, $score, $total_questions);

if ($stmt->execute()) {
    
    // ==========================================
    // 5. SMS Notification Logic
    // ==========================================
    
    // දෙමාපියන්ගේ දුරකථන අංකය ලබා ගැනීම (From Student Session Data)
    $parent_phone = $student['parent_phone']; 
    
    if (!empty($parent_phone)) {
        // SMS පණිවිඩය සකස් කිරීම
        $message = "Exam Completed.\nStudent: $student_reg_no\nExam ID: $exam_id\nScore: $score/$total_questions ($percentage%)\n- Future Minds";
        
        // SMS යැවීමේ Function එක ඇමතීම
        sendSMS($parent_phone, $message);
    }

} else {
    echo "Error saving results: " . $stmt->error;
    exit();
}
$stmt->close();

// ==========================================
// 6. SMS Sending Function
// ==========================================
function sendSMS($to, $message) {
    // ⚠️ වැදගත්: SMS Gateway විස්තර මෙතැනට දමන්න (Notify.lk / Twilio / ShoutOUT)
    
    /* // EXAMPLE CODE (Uncomment and fill details):
    $api_key = "YOUR_API_KEY";
    $user_id = "YOUR_USER_ID";
    $sender_id = "FutureMinds";
    
    $url = "https://sms-provider.com/api/send?user_id=$user_id&api_key=$api_key&sender_id=$sender_id&to=$to&message=" . urlencode($message);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    */
}
?>

<div class="flex-1 flex items-center justify-center bg-gray-50 h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-lg text-center max-w-lg w-full">
        
        <?php if ($percentage >= 50): ?>
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-trophy text-4xl text-green-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Excellent Job!</h1>
            <p class="text-gray-500">You passed the exam successfully.</p>
        <?php else: ?>
            <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-times-circle text-4xl text-red-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Keep Trying!</h1>
            <p class="text-gray-500">Don't give up, try harder next time.</p>
        <?php endif; ?>

        <div class="mt-8 grid grid-cols-2 gap-4 border-t border-b py-6">
            <div>
                <p class="text-sm text-gray-500 uppercase">Score</p>
                <p class="text-3xl font-bold text-indigo-600"><?php echo $score; ?> / <?php echo $total_questions; ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500 uppercase">Percentage</p>
                <p class="text-3xl font-bold <?php echo ($percentage>=50)?'text-green-500':'text-red-500'; ?>"><?php echo $percentage; ?>%</p>
            </div>
        </div>
        
        <?php if (!empty($parent_phone)): ?>
        <p class="text-xs text-gray-400 mt-2">
            <i class="fas fa-sms"></i> Result sent to parent (...<?php echo substr($parent_phone, -4); ?>)
        </p>
        <?php endif; ?>

        <div class="mt-8">
            <a href="exam_center.php" class="bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-900 transition w-full block">
                Back to Exam Center
            </a>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>