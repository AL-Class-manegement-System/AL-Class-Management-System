<?php
include('../includes/student_header.php');

// ==========================================
// 1. ශිෂ්‍යයා Enroll වී ඇති විෂයන් ලබා ගැනීම
// ==========================================
$db_student_id = $student['student_id'];
$enrolled_subjects = [];

// enrollments table එක හරහා classes table එකට සම්බන්ධ වී විෂය නාම ලබා ගැනීම
// Note: If 'classes' table subject names are different from 'study_materials' subject names, 
// you need to map them here, similar to the logic in my_classes.php.
$sql_subjects = "
    SELECT DISTINCT c.subject
    FROM enrollments e
    JOIN classes c ON e.class_id = c.class_id
    WHERE e.student_id = ?
";
$stmt_subjects = $conn->prepare($sql_subjects);
$stmt_subjects->bind_param("i", $db_student_id);
$stmt_subjects->execute();
$result_subjects = $stmt_subjects->get_result();

while ($row = $result_subjects->fetch_assoc()) {
    $enrolled_subjects[] = $row['subject'];
}
$stmt_subjects->close();

// Stream name එකද අමතර විෂයන් (ICT වැනි) සඳහා යොදන්න
$stream_map = [
    'Maths'    => 'Physical Science',
    'Bio'      => 'Bio Science',
    'Tech'     => 'Technology',
    'Art'      => 'Arts',
    'Commerce' => 'Commerce'
];
$stream_subject_name = isset($stream_map[$student['stream']]) ? $stream_map[$student['stream']] : $student['stream'];
$enrolled_subjects[] = $stream_subject_name;
$enrolled_subjects = array_unique($enrolled_subjects); // Duplicates ඉවත් කිරීම

// ==========================================
// 2. විෂය නාමයන් පාදක කරගෙන Study Materials ලබා ගැනීම
// ==========================================
$categorized_materials = [];

if (!empty($enrolled_subjects)) {
    // සකස් කළ විෂය නාම ලැයිස්තුව, SQL query එකට ගැලපෙන ලෙස සකස් කිරීම
    // Note: This query assumes 'study_materials' table exists with columns: material_title, material_type, file_path, subject_name, upload_date, status.
    $placeholders = implode(',', array_fill(0, count($enrolled_subjects), '?'));
    
    $sql_materials = "SELECT * FROM study_materials WHERE subject_name IN ($placeholders) AND status = 1 ORDER BY subject_name, upload_date DESC";
    
    // Dynamic binding
    $stmt_materials = $conn->prepare($sql_materials);
    $types = str_repeat('s', count($enrolled_subjects));
    
    // PHP >= 5.6 සඳහා: $stmt_materials->bind_param($types, ...$enrolled_subjects); 
    // මෙහිදී bind_param() සඳහා array_map(function($a) { return &$a; }, $enrolled_subjects) වැනි සකස් කිරීමක් අවශ්‍ය වේ.
    // සරලව, $enrolled_subjects array එකේ references අවශ්‍යයි.
    // (Actual production code needs proper dynamic binding or a simpler query method if binding fails dynamically.)
    
    // For demonstration, assuming $enrolled_subjects is small and contains only strings:
    // This is a simplified/potentially unsafe way for older PHP versions, but shows the concept:
    $temp_params = [];
    foreach ($enrolled_subjects as &$param) {
        $temp_params[] = &$param;
    }
    call_user_func_array([$stmt_materials, 'bind_param'], array_merge([$types], $temp_params));

    if ($stmt_materials->execute()) {
        $materials_result = $stmt_materials->get_result();
        if ($materials_result->num_rows > 0) {
            while ($material = $materials_result->fetch_assoc()) {
                $categorized_materials[$material['subject_name']][] = $material;
            }
        }
    }
    $stmt_materials->close();
}
?>

<div class="flex-1 flex flex-col h-screen overflow-y-auto">
    <main class="p-4 md:p-8">
        
        <h1 class="text-3xl font-bold text-slate-800 mb-8">Study Packs & Downloads 📚</h1>

        <?php if (empty($categorized_materials)): ?>
            <div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-3xl border border-dashed border-gray-200">
                <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mb-4 text-indigo-600 text-3xl">
                    <i class="fas fa-box-open"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-500">No Study Materials Found</h3>
                <p class="text-sm text-gray-400 mt-1">Please enroll in a class first or check back later for available materials.</p>
            </div>
        <?php else: ?>
        
        <div class="flex flex-col md:flex-row gap-6">

            <div class="w-full md:w-64 bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex-shrink-0 sticky top-24 h-fit">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Your Subjects</p>
                <nav class="space-y-1" id="subject-tabs">
                    <?php 
                    $is_first = true;
                    foreach (array_keys($categorized_materials) as $subject): 
                        // Spaces remove/replace to create a safe HTML ID
                        $tab_id = str_replace([' ', '/', '&'], '_', $subject);
                    ?>
                    <button data-target="<?php echo $tab_id; ?>"
                        class="tab-button w-full text-left px-3 py-2 rounded-xl text-sm font-medium transition-colors 
                        <?php echo $is_first ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'; ?>">
                        <i class="fas fa-book-open mr-2"></i> <?php echo htmlspecialchars($subject); ?>
                    </button>
                    <?php 
                        $is_first = false;
                    endforeach; 
                    ?>
                </nav>
            </div>

            <div class="flex-1">
                <?php 
                $is_first = true;
                foreach ($categorized_materials as $subject => $materials): 
                    $content_id = str_replace([' ', '/', '&'], '_', $subject);
                ?>
                <div id="<?php echo $content_id; ?>" class="tab-content <?php echo $is_first ? '' : 'hidden'; ?>">
                    <h2 class="text-2xl font-bold text-slate-700 mb-4"><?php echo htmlspecialchars($subject); ?> Materials</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($materials as $material): 
                            // Set file path, assuming it's relative to the project root or accessible via this path
                            $file_url = (isset($material['file_path']) && !empty($material['file_path'])) ? '../../' . htmlspecialchars($material['file_path']) : '#';
                        ?>
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition">
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full uppercase tracking-wider mb-2 inline-block 
                                <?php 
                                    $type = $material['material_type'] ?? 'General';
                                    if($type == 'Notes') echo 'bg-blue-100 text-blue-700';
                                    elseif($type == 'Past Paper') echo 'bg-green-100 text-green-700';
                                    elseif($type == 'Reading') echo 'bg-orange-100 text-orange-700';
                                    else echo 'bg-purple-100 text-purple-700';
                                ?>">
                                <?php echo htmlspecialchars($type); ?>
                            </span>
                            <h3 class="text-lg font-bold text-slate-800 line-clamp-2 mt-1">
                                <?php echo htmlspecialchars($material['material_title']); ?>
                            </h3>
                            <p class="text-xs text-slate-500 mt-2">
                                Uploaded: <?php echo date('M d, Y', strtotime($material['upload_date'] ?? 'now')); ?>
                            </p>
                            
                            <a href="<?php echo $file_url; ?>" 
                                target="_blank"
                                class="mt-4 w-full flex items-center justify-center gap-2 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php 
                    $is_first = false;
                endforeach; 
                ?>
            </div>
            
        </div>
        <?php endif; ?>
    </main>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');

                // 1. Hide all content and reset button styles
                tabContents.forEach(content => content.classList.add('hidden'));
                tabButtons.forEach(btn => {
                    btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-md');
                    btn.classList.add('text-slate-600', 'hover:bg-slate-100');
                });

                // 2. Show the target content and set active button style
                document.getElementById(targetId).classList.remove('hidden');
                button.classList.add('bg-indigo-600', 'text-white', 'shadow-md');
                button.classList.remove('text-slate-600', 'hover:bg-slate-100');
            });
        });
    });
</script>

<?php include('../includes/footer.php'); ?>