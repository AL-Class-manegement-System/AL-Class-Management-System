<?php
include('../includes/student_header.php');

// ==========================================
// DB වෙතින් සජීවී පන්තියක URL එක ලබා ගැනීම (Hardcode ඉවත් කිරීම)
// ==========================================
$db_student_id = $student['student_id'];
$live_url = "#"; // Default fallback URL
$class_details = null;

// ශිෂ්‍යයා enroll වී ඇති පන්ති අතුරින් active live class එකක් සොයා ගැනීම (Prepared Statement)
$sql_live = "
    SELECT c.subject, c.live_url, c.teacher_name
    FROM enrollments e
    JOIN classes c ON e.class_id = c.class_id
    WHERE e.student_id = ? AND c.is_live = 1 
    LIMIT 1 
";

// $stmt_live = $conn->prepare($sql_live);
// if ($stmt_live) {
//     $stmt_live->bind_param("i", $db_student_id);
//     $stmt_live->execute();
//     $result_live = $stmt_live->get_result();
    
//     if ($result_live->num_rows > 0) {
//         $class_details = $result_live->fetch_assoc();
//         // URL එකට autoplay and mute පරාමිතීන් එකතු කිරීම
//         $live_url = htmlspecialchars($class_details['live_url']) . "?autoplay=1&mute=1";
//     }
//     $stmt_live->close();
// }

// Live Status/Title
$class_title = $class_details ? htmlspecialchars($class_details['subject']) . ' by ' . htmlspecialchars($class_details['teacher_name']) : 'No Live Class Currently Available';
$info_message = $class_details ? 'You are now watching the live session for ' . htmlspecialchars($class_details['subject']) . '.' : 'Please check the Time Table for your next scheduled class.';
$from_color = $class_details ? 'from-green-600' : 'from-yellow-600';
$to_color = $class_details ? 'to-teal-600' : 'to-orange-600';
?>

<div class="flex-1 flex flex-col h-screen overflow-y-auto">

    <main class="p-8 pt-2">

        <div
            class="bg-gradient-to-r <?php echo $from_color; ?> <?php echo $to_color; ?> rounded-2xl p-8 text-white mb-8 shadow-lg relative overflow-hidden opacity-80">
            <div class="relative z-10">
                <h1 class="text-3xl font-bold mb-2"><?php echo $class_title; ?> 🎥</h1>
                <p class="opacity-90"><?php echo $info_message; ?></p>
            </div>  
            <i class="fas fa-video absolute -bottom-4 -right-4 text-9xl text-white opacity-10 transform rotate-12"></i>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-4">Current Live Class</h2>
            
            <?php if ($class_details && $class_details['live_url']): ?>
            <div class="aspect-w-16 aspect-h-9">
                <iframe
                    src="<?php echo $live_url; ?>"
                    title="<?php echo $class_title; ?>"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    class="w-full h-full rounded-lg">
                </iframe>
            </div>
            <?php else: ?>
            <div class="flex flex-col items-center justify-center py-20 text-center bg-gray-50 rounded-lg border border-dashed border-gray-200">
                <i class="fas fa-satellite-dish text-5xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-500">No Live Stream</h3>
                <p class="text-sm text-gray-400 mt-1">There are no ongoing live sessions for your enrolled classes right now.</p>
                <a href="time_table.php" class="mt-4 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                    View Time Table
                </a>
            </div>
            <?php endif; ?>
            
            </div>
    </main>
</div>