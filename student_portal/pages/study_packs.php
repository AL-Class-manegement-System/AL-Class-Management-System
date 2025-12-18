<?php
// student_portal/pages/study_packs.php
// Ensures only 'Active' enrolled classes AND 'Approved' materials (status=1) are shown.

session_start();
include('../includes/student_header.php');
include('../../includes/connection.php');

if (!isset($_SESSION['student_id'])) {
    echo "<script>window.location.href='../../log/login.php';</script>";
    exit();
}

$student_id = $_SESSION['student_id'];

// ==========================================
// 1. ශිෂ්‍යයා Active (Paid) වී ඇති පන්ති තෝරා ගැනීම
// ==========================================
$enrolled_class_ids = [];

// enrollment status = 1 (Active) Only
$enroll_sql = "SELECT class_id FROM enrollments WHERE student_id = ? AND status = 1";
$enroll_stmt = $conn->prepare($enroll_sql);
$enroll_stmt->bind_param("i", $student_id);
$enroll_stmt->execute();
$enroll_result = $enroll_stmt->get_result();

while ($row = $enroll_result->fetch_assoc()) {
    $enrolled_class_ids[] = $row['class_id'];
}
$enroll_stmt->close();

$categorized_materials = [];

if (!empty($enrolled_class_ids)) {

    $ids_placeholder = implode(',', array_map('intval', $enrolled_class_ids));

    // ==========================================
    // 2. Approved (Status = 1) Study Materials පමණක් පෙන්වීම
    // ==========================================
    $sql_materials = "
        SELECT 
            sm.material_id,
            sm.title AS material_title,
            sm.file_path,
            sm.uploaded_on AS upload_date,
            c.subject,
            c.class_name
        FROM study_materials sm
        JOIN classes c ON sm.class_id = c.class_id
        WHERE sm.class_id IN ($ids_placeholder) 
        AND sm.status = 1  -- <-- වැදගත්ම කොටස: Approved ඒවා පමණයි
        ORDER BY sm.uploaded_on DESC
    ";

    $materials_result = $conn->query($sql_materials);

    if ($materials_result && $materials_result->num_rows > 0) {
        while ($material = $materials_result->fetch_assoc()) {
            $group_name = $material['subject'] . " (" . $material['class_name'] . ")";
            $categorized_materials[$group_name][] = $material;
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
                <h3 class="text-xl font-bold text-slate-700">No Approved Materials Found</h3>
                <p class="text-sm text-slate-500 mt-2 max-w-md">
                    ඔබේ පන්ති සඳහා තවමත් අනුමත වූ Study Materials ඇතුළත් කර නොමැත. කරුණාකර ඔබ පන්තියට <b>Active</b> වී ඇති බව තහවුරු කරගන්න.
                </p>
                <a href="my_classes.php" class="mt-6 px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                    Check My Classes
                </a>
            </div>
        <?php else: ?>

            <div class="flex flex-col lg:flex-row gap-8">
                <div class="w-full lg:w-72 flex-shrink-0">
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 sticky top-4">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Subjects</p>
                        <nav class="space-y-2" id="subject-tabs">
                            <?php $is_first = true; foreach (array_keys($categorized_materials) as $index => $subject): $tab_id = "tab_" . $index; ?>
                                <button data-target="<?php echo $tab_id; ?>"
                                    class="tab-button w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center gap-3
                            <?php echo $is_first ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600'; ?>">
                                    <i class="fas fa-folder<?php echo $is_first ? '-open' : ''; ?>"></i>
                                    <span class="truncate"><?php echo htmlspecialchars($subject); ?></span>
                                </button>
                            <?php $is_first = false; endforeach; ?>
                        </nav>
                    </div>
                </div>

                <div class="flex-1 min-w-0">
                    <?php $is_first = true; foreach ($categorized_materials as $subject => $materials): 
                        $current_index = array_search($subject, array_keys($categorized_materials));
                        $content_id = "tab_" . $current_index;
                    ?>
                        <div id="<?php echo $content_id; ?>" class="tab-content <?php echo $is_first ? '' : 'hidden'; ?> animate-fade-in">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                                    <i class="fas fa-layer-group text-indigo-500"></i> <?php echo htmlspecialchars($subject); ?>
                                </h2>
                                <span class="text-xs font-medium bg-slate-200 text-slate-600 px-3 py-1 rounded-full"><?php echo count($materials); ?> Files</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                                <?php foreach ($materials as $material):
                                    // Path Correction
                                    $file_path_db = $material['file_path'];
                                    $clean_path = str_replace('../', '', $file_path_db);
                                    $file_url = '../../' . $clean_path; // Relative to student_portal/pages/

                                    $ext = strtolower(pathinfo($clean_path, PATHINFO_EXTENSION));
                                    $icon = 'fa-file-alt'; $icon_color = 'text-slate-500'; $bg_color = 'bg-slate-100';
                                    if ($ext == 'pdf') { $icon = 'fa-file-pdf'; $icon_color = 'text-red-500'; $bg_color = 'bg-red-50'; }
                                    elseif (in_array($ext, ['doc', 'docx'])) { $icon = 'fa-file-word'; $icon_color = 'text-blue-500'; $bg_color = 'bg-blue-50'; }
                                    elseif (in_array($ext, ['zip', 'rar'])) { $icon = 'fa-file-archive'; $icon_color = 'text-yellow-600'; $bg_color = 'bg-yellow-50'; }
                                ?>
                                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md hover:border-indigo-200 transition-all group">
                                        <div class="flex items-start justify-between">
                                            <div class="w-12 h-12 <?php echo $bg_color; ?> rounded-xl flex items-center justify-center flex-shrink-0">
                                                <i class="fas <?php echo $icon; ?> <?php echo $icon_color; ?> text-2xl"></i>
                                            </div>
                                            <div class="text-right"><span class="text-[10px] font-bold text-slate-400 uppercase"><?php echo strtoupper($ext); ?></span></div>
                                        </div>
                                        <h3 class="text-base font-bold text-slate-800 mt-4 mb-1 line-clamp-2"><?php echo htmlspecialchars($material['material_title']); ?></h3>
                                        <p class="text-xs text-slate-400 mb-4"><i class="far fa-clock"></i> <?php echo date('M d, Y', strtotime($material['upload_date'])); ?></p>
                                        <a href="<?php echo $file_url; ?>" download target="_blank" class="w-full flex items-center justify-center gap-2 py-2.5 bg-slate-50 text-slate-700 text-sm font-bold rounded-xl hover:bg-indigo-600 hover:text-white transition-all">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php $is_first = false; endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');
                tabContents.forEach(content => content.classList.add('hidden'));
                
                // Reset Styles
                tabButtons.forEach(btn => {
                    btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-md', 'shadow-indigo-200');
                    btn.classList.add('text-slate-600', 'hover:bg-slate-50');
                    btn.querySelector('i').classList.replace('fa-folder-open', 'fa-folder');
                });

                // Activate Target
                document.getElementById(targetId).classList.remove('hidden');
                
                // Activate Button Style
                button.classList.add('bg-indigo-600', 'text-white', 'shadow-md', 'shadow-indigo-200');
                button.classList.remove('text-slate-600', 'hover:bg-slate-50');
                button.querySelector('i').classList.replace('fa-folder', 'fa-folder-open');
            });
        });
    });
</script>

<?php include('../includes/footer.php'); ?>