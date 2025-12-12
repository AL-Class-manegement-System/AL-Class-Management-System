<?php
// student_portal/pages/exam_results.php
include('../includes/student_header.php');

// ==========================================
// 1. Fetch Exam Results (Prepared Statement)
// ==========================================
$student_db_id = $student['student_id'];

// Exam Results + Exam Details + Teacher Name එකතු කර දත්ත ගැනීම
$sql = "
    SELECT 
        r.score,
        r.total_questions,
        r.attempted_at,
        e.title AS exam_title,
        e.subject,
        t.full_name AS teacher_name
    FROM exam_results r
    JOIN online_exams e ON r.exam_id = e.exam_id
    JOIN teachers t ON e.teacher_id = t.teacher_id
    WHERE r.student_id = ?
    ORDER BY r.attempted_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_db_id);
$stmt->execute();
$result = $stmt->get_result();

// සංඛ්‍යාලේඛන සඳහා විචල්‍යයන්
$total_exams = 0;
$total_marks_percentage = 0;
$passed_exams = 0;
$results_data = [];

while ($row = $result->fetch_assoc()) {
    // ප්‍රතිශතය ගණනය
    $percentage = ($row['total_questions'] > 0) ? round(($row['score'] / $row['total_questions']) * 100, 1) : 0;
    $row['percentage'] = $percentage;
    
    // Pass ද Fail ද කියා බැලීම (උදා: 40% ට වැඩි නම් Pass)
    $row['status'] = ($percentage >= 40) ? 'Pass' : 'Fail';
    
    if ($percentage >= 40) {
        $passed_exams++;
    }
    
    $total_marks_percentage += $percentage;
    $total_exams++;
    $results_data[] = $row;
}

$average_score = ($total_exams > 0) ? round($total_marks_percentage / $total_exams, 1) : 0;
$stmt->close();
?>

<div class="flex-1 flex flex-col h-screen overflow-y-auto bg-gray-50">
    <main class="p-4 md:p-8">
        
        <h1 class="text-2xl md:text-3xl font-bold text-slate-800 mb-6 flex items-center gap-3">
            📊 My Exam Results
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-indigo-100 flex items-center gap-4">
                <div class="p-4 bg-indigo-50 text-indigo-600 rounded-full text-xl">
                    <i class="fas fa-file-signature"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium uppercase">Total Exams</p>
                    <h3 class="text-2xl font-bold text-slate-800"><?php echo $total_exams; ?></h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-green-100 flex items-center gap-4">
                <div class="p-4 bg-green-50 text-green-600 rounded-full text-xl">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium uppercase">Average Score</p>
                    <h3 class="text-2xl font-bold text-slate-800"><?php echo $average_score; ?>%</h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-orange-100 flex items-center gap-4">
                <div class="p-4 bg-orange-50 text-orange-600 rounded-full text-xl">
                    <i class="fas fa-trophy"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium uppercase">Passed Exams</p>
                    <h3 class="text-2xl font-bold text-slate-800"><?php echo $passed_exams; ?> <span class="text-sm text-slate-400 font-normal">/ <?php echo $total_exams; ?></span></h3>
                </div>
            </div>
        </div>

        <?php if (empty($results_data)): ?>
            <div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-3xl border border-dashed border-gray-200">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-300 text-3xl">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-400">No Exams Attempted Yet</h3>
                <p class="text-sm text-gray-400 mt-1">Go to the <a href="exam_center.php" class="text-indigo-500 hover:underline">Exam Center</a> to take your first exam.</p>
            </div>
        <?php else: ?>
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                                <th class="p-4 font-semibold">Exam Title</th>
                                <th class="p-4 font-semibold">Subject & Teacher</th>
                                <th class="p-4 font-semibold">Date Taken</th>
                                <th class="p-4 font-semibold text-center">Score</th>
                                <th class="p-4 font-semibold text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php foreach ($results_data as $row): 
                                // Color logic based on percentage
                                $score_color = ($row['percentage'] >= 75) ? 'text-green-600' : (($row['percentage'] >= 40) ? 'text-blue-600' : 'text-red-600');
                                $status_bg = ($row['status'] == 'Pass') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
                            ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-4">
                                    <p class="font-bold text-slate-800"><?php echo htmlspecialchars($row['exam_title']); ?></p>
                                </td>
                                <td class="p-4">
                                    <p class="text-slate-700 font-medium"><?php echo htmlspecialchars($row['subject']); ?></p>
                                    <p class="text-xs text-slate-400"><?php echo htmlspecialchars($row['teacher_name']); ?></p>
                                </td>
                                <td class="p-4 text-slate-500">
                                    <?php echo date('M d, Y', strtotime($row['attempted_at'])); ?>
                                    <br>
                                    <span class="text-xs"><?php echo date('h:i A', strtotime($row['attempted_at'])); ?></span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="text-lg font-bold <?php echo $score_color; ?>">
                                        <?php echo $row['score']; ?> / <?php echo $row['total_questions']; ?>
                                    </span>
                                    <p class="text-xs text-slate-400 font-medium"><?php echo $row['percentage']; ?>%</p>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $status_bg; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endif; ?>

    </main>
</div>

<?php include('../includes/footer.php'); ?>