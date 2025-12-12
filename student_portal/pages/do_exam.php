<?php
// student_portal/pages/do_exam.php
include('../includes/student_header.php');

// 1. Exam ID එක URL එකෙන් ලබා ගැනීම
if (!isset($_GET['exam_id'])) {
    echo "<script>window.location.href='exam_center.php';</script>";
    exit();
}

$exam_id = intval($_GET['exam_id']);

// වැදගත්: student_header.php හරහා $student array එක ලැබී ඇති නිසා
// අපි DB ID එක (Integer) භාවිතා කරමු. (Session එකේ ඇත්තේ Reg Number විය හැක)
$student_db_id = $student['student_id']; 

// 2. විභාග විස්තර ලබා ගැනීම
$exam_sql = "SELECT * FROM online_exams WHERE exam_id = ?";
$stmt = $conn->prepare($exam_sql);
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$exam_res = $stmt->get_result();
$exam = $exam_res->fetch_assoc();

if (!$exam) {
    echo "<div class='p-10 text-red-500 font-bold text-center'>Invalid Exam ID!</div>";
    include('../includes/footer.php');
    exit();
}

// 3. දැනටමත් කර ඇත්නම් නැවත කිරීමට නොදීම
// (කලින් දෝෂය ආ තැන දැන් Prepared Statement මගින් නිවැරදි කර ඇත)
$check_sql = "SELECT * FROM exam_results WHERE student_id = ? AND exam_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $student_db_id, $exam_id);
$check_stmt->execute();
$check_res = $check_stmt->get_result();

if ($check_res->num_rows > 0) {
    echo "<div class='flex items-center justify-center h-screen bg-gray-50'>
            <div class='text-center bg-white p-10 rounded-2xl shadow-xl'>
                <div class='mb-4 text-yellow-500 text-6xl'><i class='fas fa-exclamation-circle'></i></div>
                <h2 class='text-2xl font-bold text-gray-800 mb-2'>Already Attempted!</h2>
                <p class='text-gray-500 mb-6'>You have already completed this exam.</p>
                <a href='exam_center.php' class='bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition'>
                    Back to Exam Center
                </a>
            </div>
          </div>";
    include('../includes/footer.php');
    exit();
}

// 4. ප්‍රශ්න ලබා ගැනීම
$q_sql = "SELECT * FROM exam_questions WHERE exam_id = ? ORDER BY RAND()"; 
$q_stmt = $conn->prepare($q_sql);
$q_stmt->bind_param("i", $exam_id);
$q_stmt->execute();
$questions = $q_stmt->get_result();
?>

<div class="flex-1 p-4 md:p-8 bg-gray-50 min-h-screen">
    
    <div class="bg-white p-4 rounded-xl shadow-sm flex flex-col md:flex-row justify-between items-center sticky top-0 z-40 border-b-4 border-indigo-500 mb-6">
        <div class="mb-2 md:mb-0 text-center md:text-left">
            <h1 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($exam['title']); ?></h1>
            <p class="text-sm text-gray-500"><i class="fas fa-book mr-1"></i> Subject: <?php echo htmlspecialchars($exam['subject']); ?></p>
        </div>
        <div class="text-right flex items-center gap-3 bg-red-50 px-4 py-2 rounded-lg border border-red-100">
            <p class="text-xs text-red-500 uppercase font-bold hidden md:block">Time Remaining</p>
            <div id="timer" class="text-2xl font-mono font-bold text-red-600">00:00</div>
        </div>
    </div>

    <form action="submit_exam.php" method="POST" id="examForm" class="max-w-4xl mx-auto pb-20">
        <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
        
        <?php 
        if ($questions->num_rows > 0) {
            $i = 1;
            while ($q = $questions->fetch_assoc()): 
        ?>
        <div class="bg-white p-6 rounded-xl shadow-sm mb-6 border border-gray-100 hover:shadow-md transition">
            <p class="font-bold text-lg text-gray-800 mb-4 flex">
                <span class="bg-indigo-100 text-indigo-700 w-8 h-8 flex items-center justify-center rounded-full mr-3 text-sm flex-shrink-0 font-bold"><?php echo $i; ?></span>
                <?php echo htmlspecialchars($q['question_text']); ?>
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 ml-0 md:ml-11">
                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-indigo-50 hover:border-indigo-200 transition group">
                    <input type="radio" name="q_<?php echo $q['question_id']; ?>" value="1" class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                    <span class="ml-3 text-gray-700 group-hover:text-indigo-700"><?php echo htmlspecialchars($q['option_1']); ?></span>
                </label>

                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-indigo-50 hover:border-indigo-200 transition group">
                    <input type="radio" name="q_<?php echo $q['question_id']; ?>" value="2" class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                    <span class="ml-3 text-gray-700 group-hover:text-indigo-700"><?php echo htmlspecialchars($q['option_2']); ?></span>
                </label>

                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-indigo-50 hover:border-indigo-200 transition group">
                    <input type="radio" name="q_<?php echo $q['question_id']; ?>" value="3" class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                    <span class="ml-3 text-gray-700 group-hover:text-indigo-700"><?php echo htmlspecialchars($q['option_3']); ?></span>
                </label>

                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-indigo-50 hover:border-indigo-200 transition group">
                    <input type="radio" name="q_<?php echo $q['question_id']; ?>" value="4" class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                    <span class="ml-3 text-gray-700 group-hover:text-indigo-700"><?php echo htmlspecialchars($q['option_4']); ?></span>
                </label>
                
                <?php if(!empty($q['option_5']) && $q['option_5'] != '-'): ?>
                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-indigo-50 hover:border-indigo-200 transition group col-span-1 md:col-span-2">
                    <input type="radio" name="q_<?php echo $q['question_id']; ?>" value="5" class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                    <span class="ml-3 text-gray-700 group-hover:text-indigo-700"><?php echo htmlspecialchars($q['option_5']); ?></span>
                </label>
                <?php endif; ?>
            </div>
        </div>
        <?php 
            $i++; 
            endwhile; 
        } else {
            echo "<div class='text-center py-10 text-gray-500'>No questions found for this exam.</div>";
        }
        ?>

        <div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 p-4 md:pl-72 flex justify-end items-center z-30">
            <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-lg font-bold text-lg hover:bg-green-700 transition shadow-lg flex items-center transform hover:scale-105">
                <i class="fas fa-paper-plane mr-2"></i> Submit Answers
            </button>
        </div>
    </form>
</div>

<script>
    // Timer Logic
    let duration = <?php echo intval($exam['duration']) * 60; ?>; 
    let display = document.querySelector('#timer');
    
    if (duration > 0) {
        let timer = setInterval(function () {
            let minutes = parseInt(duration / 60, 10);
            let seconds = parseInt(duration % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = minutes + ":" + seconds;

            if (--duration < 0) {
                clearInterval(timer);
                alert("Time is up! Submitting automatically.");
                document.getElementById("examForm").submit();
            }
        }, 1000);
    } else {
        display.textContent = "Unlimited";
    }
</script>

<?php include('../includes/footer.php'); ?>