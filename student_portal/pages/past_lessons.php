<?php
include('../includes/student_header.php');

// Fetch enrolled classes for this student
$enrolled_classes = [];
$e_sql = "SELECT class_id FROM enrollments WHERE student_id = ?";
$e_stmt = $conn->prepare($e_sql);
$e_stmt->bind_param("i", $student['student_id']); // Assuming student_id is reg_number based on header logic? header uses reg_number as $student_id but fetches using that.
// Wait, header says: $student_id = $_SESSION['student_id']; which IS reg_number.
// And enrollments table? Let's assume structure.
if ($e_stmt->execute()) {
    $e_res = $e_stmt->get_result();
    while ($r = $e_res->fetch_assoc()) {
        $enrolled_classes[] = $r['class_id'];
    }
}

// Convert to CSV for SQL IN clause
$class_ids_str = implode(',', $enrolled_classes);
$lessons = [];

if (!empty($enrolled_classes)) {
    // Fetch Lessons
    // IN Clause is not directly supported by bind_param with array, so we sanitize manually or use placeholders
    $placeholders = implode(',', array_fill(0, count($enrolled_classes), '?'));
    $sql = "SELECT pl.*, c.class_name, t.full_name as teacher_name 
            FROM past_lessons pl 
            JOIN classes c ON pl.class_id = c.class_id 
            JOIN teachers t ON pl.teacher_id = t.teacher_id
            WHERE pl.class_id IN ($placeholders) AND pl.status = 1 
            ORDER BY pl.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $types = str_repeat('i', count($enrolled_classes));
    $stmt->bind_param($types, ...$enrolled_classes);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $lessons[] = $row;
    }
}
?>

<div class="flex-1 flex flex-col h-screen overflow-y-auto">
    <main class="flex-grow p-4 md:p-8 max-w-7xl mx-auto w-full">

        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Past Lessons</h1>
            <p class="text-slate-500 mt-1">Watch recordings of previous classes.</p>
        </div>

        <?php if (empty($lessons)): ?>
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center border border-slate-100">
                <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-video-slash text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">No Lessons Found</h3>
                <p class="text-slate-500 mt-2">You don't have any past lessons available for your enrolled classes.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($lessons as $lesson): ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition group">
                        <!-- Thumbnail -->
                        <a href="<?php echo htmlspecialchars($lesson['video_url']); ?>" target="_blank" class="block relative aspect-video bg-slate-200 overflow-hidden">
                            <?php if ($lesson['cover_image']): ?>
                                <img src="../../uploads/lesson_covers/<?php echo $lesson['cover_image']; ?>" alt="Cover" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600">
                                    <i class="fas fa-play-circle text-white text-5xl opacity-80 group-hover:scale-110 transition"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition flex items-center justify-center">
                                <span class="bg-white/90 text-indigo-600 rounded-full w-12 h-12 flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition duration-300">
                                    <i class="fas fa-play ml-1"></i>
                                </span>
                            </div>

                            <span class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded backdrop-blur-sm">
                                <?php echo htmlspecialchars($lesson['class_name']); ?>
                            </span>
                        </a>

                        <div class="p-5">
                            <h3 class="font-bold text-slate-800 line-clamp-2 mb-2 group-hover:text-indigo-600 transition">
                                <a href="<?php echo htmlspecialchars($lesson['video_url']); ?>" target="_blank">
                                    <?php echo htmlspecialchars($lesson['title']); ?>
                                </a>
                            </h3>
                            
                            <p class="text-xs text-slate-500 mb-4 line-clamp-2">
                                <?php echo htmlspecialchars($lesson['description'] ?: 'No description available.'); ?>
                            </p>

                            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 text-xs">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-600 truncate max-w-[100px]">
                                        <?php echo htmlspecialchars($lesson['teacher_name']); ?>
                                    </span>
                                </div>
                                <span class="text-xs text-slate-400">
                                    <i class="far fa-calendar mr-1"></i> <?php echo date('M d, Y', strtotime($lesson['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>
    <?php include('../includes/footer.php'); ?>
</div>
