<?php
include('../includes/student_header.php'); 

// ==========================================
// 1. ශිෂ්‍යයා Enroll වී ඇති විෂයන් ලබා ගැනීම (Prepared Statement)
// ==========================================
$db_student_id = $student['student_id']; 
$enrolled_subjects = []; 

// Enrollments සහ Classes වගු භාවිතා කර ශිෂ්‍යයාගේ විෂය නාම ලබා ගනී
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
    // **වැඩිදියුණු කිරීම:** විෂය නාමයන් නිවැරදිව ගැලපීම සඳහා trim කර ගබඩා කිරීම.
    $enrolled_subjects[] = trim($row['subject']); 
}
$stmt_subjects->close(); 

// Stream name එකද අමතර විෂයන් සඳහා යොදයි (ඔබගේ DB Schema මත පදනම්ව)
$stream_map = [ 
    'Maths'    => 'Physical Science', 
    'Bio'      => 'Bio Science', 
    'Tech'     => 'Technology', 
    'Art'      => 'Arts', 
    'Commerce' => 'Commerce' 
];
$stream_subject_name = isset($stream_map[$student['stream']]) ? $stream_map[$student['stream']] : $student['stream']; 
$enrolled_subjects[] = trim($stream_subject_name); 
$enrolled_subjects = array_unique($enrolled_subjects); 

if (empty($enrolled_subjects)) {
    $categorized_materials = [];
} else {
    // ==========================================
    // 2. විෂය නාමයන් පාදක කරගෙන Study Materials ලබා ගැනීම (Dynamic Prepared Statement)
    // ==========================================
    $categorized_materials = []; 

    $placeholders = implode(',', array_fill(0, count($enrolled_subjects), '?')); 
    
    // Status = 1 (Active) materials පමණක් පෙන්වයි.
    $sql_materials = "SELECT * FROM study_materials WHERE subject_name IN ($placeholders) AND status = 1 ORDER BY subject_name, upload_date DESC"; 
    
    $stmt_materials = $conn->prepare($sql_materials); 
    
    // **වැඩිදියුණු කළ Binding:** dynamic parameters සඳහා නිවැරදි references සකස් කිරීම.
    $types = str_repeat('s', count($enrolled_subjects)); 
    $bind_params = array($types);
    for ($i = 0; $i < count($enrolled_subjects); $i++) {
        $bind_params[] = &$enrolled_subjects[$i];
    }

    // call_user_func_array භාවිතයෙන් bind_param ක්‍රියාත්මක කරයි.
    call_user_func_array(array($stmt_materials, 'bind_param'), $bind_params);

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
                <p class="text-sm text-gray-400 mt-1">Please ensure you are enrolled in classes or check back later for available materials. (Enrolled Subjects checked: <?php echo implode(', ', $enrolled_subjects); ?>)</p>
            </div>
        <?php else: ?>
        
        <div class="flex flex-col md:flex-row gap-6">

            <div class="w-full md:w-64 bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex-shrink-0 sticky top-24 h-fit">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Your Subjects</p>
                <nav class="space-y-1" id="subject-tabs">
                    <?php 
                    $is_first = true; 
                    foreach (array_keys($categorized_materials) as $subject): 
                        $tab_id = str_replace([' ', '/', '&'], '_', $subject); 
                    ?>
                    <button data-target="<?php echo htmlspecialchars($tab_id); ?>"
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
                <div id="<?php echo htmlspecialchars($content_id); ?>" class="tab-content <?php echo $is_first ? '' : 'hidden'; ?>">
                    <h2 class="text-2xl font-bold text-slate-700 mb-4"><?php echo htmlspecialchars($subject); ?> Materials</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($materials as $material): 
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
                                Uploaded: <?php echo date('M d, Y', strtotime($material['upload_date'] ?? '2000-01-01')); ?> 
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

                tabContents.forEach(content => content.classList.add('hidden'));
                tabButtons.forEach(btn => {
                    btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-md');
                    btn.classList.add('text-slate-600', 'hover:bg-slate-100');
                });

                const targetContent = document.getElementById(targetId);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }
                
                button.classList.add('bg-indigo-600', 'text-white', 'shadow-md');
                button.classList.remove('text-slate-600', 'hover:bg-slate-100');
            });
        });
        
        if (tabButtons.length > 0) {
            tabButtons[0].click(); 
        }
    });
</script>

<?php include('../includes/footer.php'); ?>