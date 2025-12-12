<?php
// student_portal/pages/submit_exam.php
include('../includes/student_header.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>window.location.href='exam_center.php';</script>";
    exit();
}

$exam_id = intval($_POST['exam_id']);
$student_id = $_SESSION['student_id'];
$score = 0;
$total_questions = 0;

// නිවැරදි පිළිතුරු ලබා ගැනීම
$q_sql = "SELECT question_id, correct_option FROM exam_questions WHERE exam_id = $exam_id";
$result = $conn->query($q_sql);

while ($row = $result->fetch_assoc()) {
    $total_questions++;
    $qid = $row['question_id'];
    $correct_opt = $row['correct_option'];

    // සිසුවාගේ පිළිතුර
    if (isset($_POST['q_' . $qid])) {
        $student_ans = intval($_POST['q_' . $qid]);
        if ($student_ans == $correct_opt) {
            $score++;
        }
    }
}

// ප්‍රතිඵල Database එකට Save කිරීම
$save_sql = "INSERT INTO exam_results (exam_id, student_id, score, total_questions) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($save_sql);
$stmt->bind_param("iiii", $exam_id, $student_id, $score, $total_questions);
$stmt->execute();

// ප්‍රතිශතය ගණනය
$percentage = ($total_questions > 0) ? round(($score / $total_questions) * 100, 2) : 0;
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

        <div class="mt-8">
            <a href="exam_center.php" class="bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-900 transition w-full block">
                Back to Exam Center
            </a>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>