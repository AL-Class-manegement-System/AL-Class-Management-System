<?php
// teacher_portal/pages/exam_results.php
// Updated: With PDF & CSV Export Features

include('../include/head.php');
require_once '../../includes/connection.php';

// 1. Security Check
if (!isset($_SESSION['teacher_id']) || !isset($_SESSION['teacher_db_id'])) {
    header("Location: ../../log/login.php");
    exit();
}

$teacher_db_id = $_SESSION['teacher_db_id'];
$selected_exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
$results = [];
$exam_details = null;

// 2. Fetch Exams created by this teacher (For the Dropdown)
$exams_list = [];
$sql_exams = "SELECT exam_id, title, subject FROM online_exams WHERE teacher_id = ? ORDER BY created_at DESC";
$stmt_exams = $conn->prepare($sql_exams);
$stmt_exams->bind_param("i", $teacher_db_id);
$stmt_exams->execute();
$res_exams = $stmt_exams->get_result();
while ($row = $res_exams->fetch_assoc()) {
    $exams_list[] = $row;
}
$stmt_exams->close();

// 3. Fetch Results if an exam is selected
if ($selected_exam_id > 0) {
    // A. Verify exam ownership
    $check_sql = "SELECT title, subject, created_at FROM online_exams WHERE exam_id = ? AND teacher_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $selected_exam_id, $teacher_db_id);
    $check_stmt->execute();
    $exam_res = $check_stmt->get_result();
    
    if ($exam_res->num_rows > 0) {
        $exam_details = $exam_res->fetch_assoc();
        
        // B. Get Student Results
        $sql_results = "
            SELECT 
                r.score, 
                r.total_questions, 
                r.attempted_at,
                s.full_name, 
                s.reg_number,
                s.student_phone
            FROM exam_results r
            JOIN students s ON r.student_id = s.student_id
            WHERE r.exam_id = ?
            ORDER BY r.score DESC"; 
            
        $stmt_res = $conn->prepare($sql_results);
        $stmt_res->bind_param("i", $selected_exam_id);
        $stmt_res->execute();
        $result_set = $stmt_res->get_result();
        
        while ($row = $result_set->fetch_assoc()) {
            $results[] = $row;
        }
        $stmt_res->close();
    } else {
        echo "<script>alert('Invalid Exam or Access Denied'); window.location.href='exam_results.php';</script>";
    }
    $check_stmt->close();
}
?>

<?php include("../include/sidebar.php"); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<div class="p-4 sm:ml-64 pb-20">
    
    <div class="flex items-center justify-between p-4 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm">
        <h2 class="text-xl font-bold text-gray-800">Exam Results & Reports</h2>
        <div class="text-sm text-gray-500">Analyze student performance</div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/2">
                <label for="exam_id" class="block mb-2 text-sm font-medium text-gray-900">Select Exam to View Results</label>
                <select id="exam_id" name="exam_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    <option value="">-- Choose an Exam --</option>
                    <?php foreach ($exams_list as $ex): ?>
                        <option value="<?php echo $ex['exam_id']; ?>" <?php if($selected_exam_id == $ex['exam_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($ex['title']) . " (" . htmlspecialchars($ex['subject']) . ")"; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                View Results
            </button>
        </form>
    </div>

    <?php if ($selected_exam_id > 0 && $exam_details): ?>
        
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900" id="examTitle"><?php echo htmlspecialchars($exam_details['title']); ?></h3>
                    <p class="text-sm text-gray-500">Subject: <?php echo htmlspecialchars($exam_details['subject']); ?> | Date Created: <?php echo date('Y-m-d', strtotime($exam_details['created_at'])); ?></p>
                </div>
                
                <div class="flex gap-2">
                    <button onclick="exportToCSV()" class="flex items-center text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2">
                        <i class="ph ph-microsoft-excel-logo text-lg mr-2"></i> Export CSV
                    </button>
                    <button onclick="exportToPDF()" class="flex items-center text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2">
                        <i class="ph ph-file-pdf text-lg mr-2"></i> Export PDF
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="resultsTable" class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-3">Rank</th>
                            <th scope="col" class="px-6 py-3">Student Name</th>
                            <th scope="col" class="px-6 py-3">Reg Number</th>
                            <th scope="col" class="px-6 py-3">Score</th>
                            <th scope="col" class="px-6 py-3">Percentage</th>
                            <th scope="col" class="px-6 py-3">Attempted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($results) > 0): ?>
                            <?php 
                            $rank = 1;
                            foreach ($results as $row): 
                                $percentage = ($row['total_questions'] > 0) ? round(($row['score'] / $row['total_questions']) * 100, 1) : 0;
                                
                                // Color code
                                $score_color = 'text-gray-900';
                                if($percentage >= 75) $score_color = 'text-green-600 font-bold';
                                elseif($percentage < 40) $score_color = 'text-red-600 font-bold';
                            ?>
                                <tr class="bg-white border-b hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        <?php echo $rank; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-base font-semibold text-gray-900"><?php echo htmlspecialchars($row['full_name']); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo htmlspecialchars($row['student_phone']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-gray-600">
                                        <?php echo htmlspecialchars($row['reg_number']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-base <?php echo $score_color; ?>">
                                            <?php echo $row['score']; ?> / <?php echo $row['total_questions']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php echo $percentage; ?>%
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        <?php echo date('Y-m-d H:i', strtotime($row['attempted_at'])); ?>
                                    </td>
                                </tr>
                            <?php 
                                $rank++;
                            endforeach; 
                            ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="ph ph-user-list text-4xl mb-2 text-gray-300"></i>
                                        <p>No students have attempted this exam yet.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php elseif($selected_exam_id > 0): ?>
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
            <span class="font-medium">Error!</span> Exam details could not be loaded.
        </div>
    <?php endif; ?>

</div>

<script>
    // 1. Export to PDF Function
    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        // Exam Title from PHP
        const title = "Exam Results: " + document.getElementById('examTitle').innerText;
        
        // Add Title to PDF
        doc.setFontSize(16);
        doc.text(title, 14, 20);
        doc.setFontSize(10);
        doc.text("Generated on: " + new Date().toLocaleDateString(), 14, 28);

        // Generate Table
        doc.autoTable({
            html: '#resultsTable',
            startY: 35,
            theme: 'grid',
            headStyles: { fillColor: [41, 128, 185] }, // Blue Header
            styles: { fontSize: 9 },
        });

        // Save PDF
        doc.save('exam_results.pdf');
    }

    // 2. Export to CSV Function
    function exportToCSV() {
        // Select the table
        var table = document.getElementById("resultsTable");
        
        // Convert table to worksheet
        var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet JS"});
        
        // Save File
        XLSX.writeFile(wb, "exam_results.xlsx");
    }
</script>

<?php include("../include/footer.php"); ?>