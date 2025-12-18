<?php
// student_portal/pages/study_packs.php
session_start();
include('../includes/student_header.php');
include('../../includes/connection.php'); // Connection එක ෂුවර් කරගන්න

$student_id = $_SESSION['student_id'];

// ==========================================
// 1. ශිෂ්‍යයා Enroll වී ඇති Active Classes සොයා ගැනීම
// ==========================================
$enrolled_class_ids = [];

// enrollments table එකෙන් active (status=1) පන්ති පමණක් ගනී
$sql_enroll = "SELECT class_id FROM enrollments WHERE student_id = ? AND status = 1";
$stmt = $conn->prepare($sql_enroll);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result_enroll = $stmt->get_result();

while ($row = $result_enroll->fetch_assoc()) {
    $enrolled_class_ids[] = $row['class_id'];
}
$stmt->close();

$categorized_materials = [];

// Enroll වී ඇති පන්ති තිබේ නම් පමණක් Materials සොයයි
if (!empty($enrolled_class_ids)) {
    
    // Class IDs කොමා මගින් වෙන් කර String එකක් සාදයි (SQL IN (...) සඳහා)
    $class_ids_str = implode(',', array_map('intval', $enrolled_class_ids));
    
    // study_materials සහ classes tables join කර දත්ත ගනී
    // Admin එකෙන් upload කළේ 'class_id' එකට නිසා, අපි 'class_id' එකෙන් මැච් කරනවා
    $sql_materials = "
        SELECT sm.*, c.subject, c.class_name 
        FROM study_materials sm
        JOIN classes c ON sm.class_id = c.class_id
        WHERE sm.class_id IN ($class_ids_str) AND sm.status = 1
        ORDER BY sm.uploaded_on DESC
    ";
    
    $result_materials = $conn->query($sql_materials);
    
    if ($result_materials && $result_materials->num_rows > 0) {
        while ($row = $result_materials->fetch_assoc()) {
            // පන්තියේ නම සහ විෂය අනුව කාණ්ඩ කරයි
            // උදා: Combined Maths - 2025 Revision
            $group_name = $row['subject'] . " - " . $row['class_name'];
            $categorized_materials[$group_name][] = $row;
        }
    }
}
?>

<div class="flex-1 flex flex-col h-screen overflow-y-auto bg-gray-50">
    <main class="p-4 md:p-8">
        
        <h1 class="text-3xl font-bold text-slate-800 mb-2">Study Packs & Downloads 📚</h1>
        <p class="text-slate-500 mb-8 text-sm">Download notes, tutes, and past papers for your enrolled classes.</p>

        <?php if (empty($categorized_materials)): ?>
            <div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-3xl border border-dashed border-gray-300 shadow-sm">
                <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mb-4 text-indigo-600 text-4xl">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-700">No Materials Found</h3>
                <p class="text-sm text-slate-500 mt-2 max-w-md">
                    ඔබට අදාල පන්තිවල Study Materials තවම ඇතුළත් කර නොමැත හෝ ඔබ පන්තියට Enroll වී නොමැත.
                </p>
                <a href="my_classes.php" class="mt-6 px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                    Check My Classes
                </a>
            </div>
        <?php else: ?>
        
        <div class="flex flex-col lg:flex-row gap-8">

            <div class="w-full lg:w-72 flex-shrink-0">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 sticky top-4">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Available Classes</p>
                    <nav class="space-y-2" id="subject-tabs">
                        <?php 
                        $is_first = true; 
                        $tab_index = 0;
                        foreach ($categorized_materials as $subject_name => $materials): 
                            $tab_id = "tab_" . $tab_index;
                        ?>
                        <button data-target="<?php echo $tab_id; ?>"
                            class="tab-button w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center gap-3
                            <?php echo $is_first ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600'; ?>">
                            <i class="fas fa-folder<?php echo $is_first ? '-open' : ''; ?>"></i> 
                            <span class="truncate"><?php echo htmlspecialchars($subject_name); ?></span>
                        </button>
                        <?php 
                            $is_first = false; 
                            $tab_index++;
                        endforeach; 
                        ?>
                    </nav>
                </div>
            </div>

            <div class="flex-1 min-w-0">
                <?php 
                $is_first = true; 
                $content_index = 0;
                foreach ($categorized_materials as $subject_name => $materials): 
                    $content_id = "tab_" . $content_index;
                ?>
                <div id="<?php echo $content_id; ?>" class="tab-content <?php echo $is_first ? '' : 'hidden'; ?> animate-fade-in">
                    
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-layer-group text-indigo-500"></i>
                            <?php echo htmlspecialchars($subject_name); ?>
                        </h2>
                        <span class="text-xs font-medium bg-slate-200 text-slate-600 px-3 py-1 rounded-full">
                            <?php echo count($materials); ?> Files
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        <?php foreach ($materials as $material): 
                            // File Path නිවැරදි කිරීම: DB එකේ තියෙන්නේ 'uploads/study_materials/filename.pdf'
                            // අපි ඉන්නේ 'student_portal/pages/' ඇතුලේ. ඒ නිසා '../../' දාන්න ඕනේ.
                            $file_path_db = $material['file_path'];
                            // සමහර විට DB එකේ '../uploads/' ලෙස තිබුනොත් එය නිවැරදි කරමු
                            $clean_path = str_replace('../', '', $file_path_db);
                            $file_url = '../../' . $clean_path; 
                            
                            // Extension එක ගැනීම
                            $ext = strtolower(pathinfo($clean_path, PATHINFO_EXTENSION));
                            
                            // Icons
                            $icon = 'fa-file-alt'; $icon_color = 'text-slate-500'; $bg_color = 'bg-slate-100';
                            if ($ext == 'pdf') { $icon = 'fa-file-pdf'; $icon_color = 'text-red-500'; $bg_color = 'bg-red-50'; }
                            elseif (in_array($ext, ['doc', 'docx'])) { $icon = 'fa-file-word'; $icon_color = 'text-blue-500'; $bg_color = 'bg-blue-50'; }
                            elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) { $icon = 'fa-file-image'; $icon_color = 'text-purple-500'; $bg_color = 'bg-purple-50'; }
                            elseif (in_array($ext, ['zip', 'rar'])) { $icon = 'fa-file-archive'; $icon_color = 'text-yellow-600'; $bg_color = 'bg-yellow-50'; }
                        ?>
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md hover:border-indigo-200 transition-all group">
                            <div class="flex items-start justify-between">
                                <div class="w-12 h-12 <?php echo $bg_color; ?> rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas <?php echo $icon; ?> <?php echo $icon_color; ?> text-2xl"></i>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">
                                        <?php echo strtoupper($ext); ?>
                                    </span>
                                </div>
                            </div>

                            <h3 class="text-base font-bold text-slate-800 mt-4 mb-1 line-clamp-2 group-hover:text-indigo-600 transition-colors" title="<?php echo htmlspecialchars($material['title']); ?>">
                                <?php echo htmlspecialchars($material['title']); ?>
                            </h3>
                            
                            <p class="text-xs text-slate-400 mb-4 flex items-center gap-1">
                                <i class="far fa-clock"></i> 
                                <?php echo date('M d, Y', strtotime($material['uploaded_on'])); ?>
                            </p>
                            
                            <a href="<?php echo $file_url; ?>" download target="_blank"
                                class="w-full flex items-center justify-center gap-2 py-2.5 bg-slate-50 text-slate-700 text-sm font-bold rounded-xl hover:bg-indigo-600 hover:text-white transition-all group-hover:shadow-lg group-hover:shadow-indigo-500/20">
                                <i class="fas fa-download"></i> Download File
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php 
                    $is_first = false; 
                    $content_index++;
                endforeach; 
                ?>
            </div>
            
        </div>
        <?php endif; ?>
    </main>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');

                // සියලු Content සැඟවීම
                tabContents.forEach(content => content.classList.add('hidden'));
                
                // බොත්තම් වල පාට වෙනස් කිරීම (Reset)
                tabButtons.forEach(btn => {
                    btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-md', 'shadow-indigo-200');
                    btn.classList.add('text-slate-600', 'hover:bg-slate-50');
                    const icon = btn.querySelector('i');
                    if(icon) { icon.classList.remove('fa-folder-open'); icon.classList.add('fa-folder'); }
                });

                // අදාල Content එක පෙන්වීම
                const targetContent = document.getElementById(targetId);
                if (targetContent) { targetContent.classList.remove('hidden'); }
                
                // Active බොත්තම පාට කිරීම
                button.classList.add('bg-indigo-600', 'text-white', 'shadow-md', 'shadow-indigo-200');
                button.classList.remove('text-slate-600', 'hover:bg-slate-50');
                
                // Icon එක මාරු කිරීම
                const activeIcon = button.querySelector('i');
                if(activeIcon) { activeIcon.classList.remove('fa-folder'); activeIcon.classList.add('fa-folder-open'); }
            });
        });
    });
</script>

<?php include('../includes/footer.php'); ?>