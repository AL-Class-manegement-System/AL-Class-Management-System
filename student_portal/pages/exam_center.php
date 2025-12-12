<?php
// student_portal/pages/exam_center.php
include('../includes/student_header.php');

$student_stream = $student['stream']; // Student header එකෙන් එන $student array එකෙන් ගන්නවා.
// Stream Mapping (DB එකේ Stream නම සහ Student table එකේ Stream නම ගැලපීමට)
$stream_map = [
    'Maths' => 'Physical Science',
    'Bio' => 'Bio Science',
    'Tech' => 'Technology',
    'Art' => 'Arts',
    'Commerce' => 'Commerce'
];
$filter_stream = isset($stream_map[$student_stream]) ? $stream_map[$student_stream] : $student_stream;

?>

<div class="flex-1 flex flex-col h-screen overflow-y-auto bg-gray-50">
    <main class="p-8">
        <h1 class="text-3xl font-bold text-slate-800 mb-6">🎯 Exam Center</h1>
        <p class="text-slate-500 mb-8">Available exams for <b><?php echo htmlspecialchars($filter_stream); ?></b> stream.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            // 1. Stream එකට අදාළ සහ
            // 2. Status = 'Approved' වූ විභාග පමණක් තෝරන්න
            $sql = "SELECT e.*, t.full_name AS teacher_name 
                    FROM online_exams e 
                    JOIN teachers t ON e.teacher_id = t.teacher_id 
                    WHERE e.stream = ? AND e.approval_status = 'Approved' 
                    ORDER BY e.created_at DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $filter_stream);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
            ?>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-indigo-100 hover:shadow-lg transition">
                    <div class="flex justify-between items-start mb-4">
                        <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded">Approved</span>
                        <span class="text-gray-500 text-xs"><i class="far fa-clock"></i> <?php echo $row['duration']; ?> mins</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p class="text-sm text-gray-500 mb-4">Subject: <?php echo htmlspecialchars($row['subject']); ?></p>
                    <p class="text-xs text-gray-400 mb-4">By: <?php echo htmlspecialchars($row['teacher_name']); ?></p>
                    
                    <a href="do_exam.php?exam_id=<?php echo $row['exam_id']; ?>" class="block w-full text-center bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition">
                        Start Exam
                    </a>
                </div>
            <?php 
                }
            } else {
                echo "<div class='col-span-full text-center py-10 text-gray-500'>No approved exams available for your stream yet.</div>";
            }
            ?>
        </div>
    </main>
</div>
<?php include('../includes/footer.php'); ?>