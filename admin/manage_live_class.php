<?php
session_start();
include('includes/auth.php'); 
include('db_con.php');

$message = null;
$error = null;

// ==========================================
// ACTION: Set Live Class (Secure Prepared Statement)
// ==========================================
if (isset($_POST['set_live'])) {
    $class_id = intval($_POST['class_id']);
    $live_url = trim($_POST['live_url']);
    $subject = trim($_POST['subject']); // To get the subject name for success message

    if (!empty($class_id) && !empty($live_url)) {
        
        // 1. Deactivate all existing live classes (Set is_live = 0)
        $deactivate_sql = "UPDATE classes SET is_live = 0 WHERE is_live = 1";
        if (!$conn->query($deactivate_sql)) {
            $error = "Error deactivating old class: " . $conn->error;
        }

        if (!$error) {
            // 2. Activate the selected class and set its URL
            $activate_sql = "UPDATE classes SET is_live = 1, live_url = ? WHERE class_id = ?";
            $activate_stmt = $conn->prepare($activate_sql);
            
            if ($activate_stmt) {
                // Live URL එකට අනවශ්‍ය පරාමිතීන් තිබේ නම් ඒවා ඉවත් කිරීම
                $clean_live_url = preg_replace('/(\?.*)?$/', '', $live_url); 
                
                $activate_stmt->bind_param("si", $clean_live_url, $class_id);
                
                if ($activate_stmt->execute()) {
                    $message = "Successfully set **" . htmlspecialchars($subject) . "** as the live class!";
                } else {
                    $error = "Error setting new live class: " . $activate_stmt->error;
                }
                $activate_stmt->close();
            } else {
                $error = "Database Prepare Error (Activate): " . $conn->error;
            }
        }
    } else {
        $error = "Please select a class and provide a valid Live URL.";
    }
}

// ==========================================
// ACTION: Stop Live Class (Secure Prepared Statement)
// ==========================================
if (isset($_POST['stop_live'])) {
    $deactivate_sql = "UPDATE classes SET is_live = 0 WHERE is_live = 1";
    if ($conn->query($deactivate_sql)) {
        $message = "Live session successfully terminated.";
    } else {
        $error = "Error stopping live session: " . $conn->error;
    }
}


// ==========================================
// DATA FETCHING: Active Classes and Current Live Status
// ==========================================

// Fetch all active classes to populate the dropdown
$classes = [];
$class_sql = "SELECT class_id, class_name, subject, teacher_name, stream FROM classes WHERE status = 1 ORDER BY subject ASC";
$class_res = $conn->query($class_sql);
if ($class_res) {
    while ($row = $class_res->fetch_assoc()) {
        $classes[] = $row;
    }
}

// Check for the currently live class
$current_live_class = null;
$live_sql = "SELECT class_id, class_name, subject, teacher_name, live_url FROM classes WHERE is_live = 1 LIMIT 1";
$live_res = $conn->query($live_sql);
if ($live_res && $live_res->num_rows > 0) {
    $current_live_class = $live_res->fetch_assoc();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Live Class Management | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex">
        <?php include('includes/sidebar.php'); ?>
        <div class="ml-64 flex-1">
            <main class="p-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">🎥 Live Class Management</h1>
                <hr class="mb-6">

                <?php if ($message): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                        <p class="font-bold">Error</p>
                        <p class="text-sm"><?php echo $error; ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($current_live_class): ?>
                
                <div class="bg-indigo-600 text-white rounded-xl shadow-lg p-6 mb-8 border border-indigo-700">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-video text-3xl animate-pulse"></i>
                            <div>
                                <h2 class="text-2xl font-bold">LIVE NOW: <?php echo htmlspecialchars($current_live_class['subject']); ?></h2>
                                <p class="text-indigo-200">Teacher: <?php echo htmlspecialchars($current_live_class['teacher_name']); ?> (<?php echo htmlspecialchars($current_live_class['class_name']); ?>)</p>
                            </div>
                        </div>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to stop the live session?');">
                            <button type="submit" name="stop_live" class="bg-red-500 text-white px-5 py-2 rounded-lg font-bold hover:bg-red-600 transition shadow-md">
                                <i class="fas fa-stop-circle mr-2"></i> STOP LIVE
                            </button>
                        </form>
                    </div>
                    <p class="mt-4 text-sm bg-indigo-500 p-2 rounded">
                        <i class="fas fa-link mr-2"></i> URL: <?php echo htmlspecialchars($current_live_class['live_url']); ?>
                    </p>
                </div>

                <?php else: ?>

                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8">
                    <h2 class="text-xl font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-play-circle text-green-600"></i> Start New Live Session
                    </h2>
                    <form method="POST" action="">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            
                            <div>
                                <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">Select Class to Go Live</label>
                                <select id="class_id" name="class_id" required 
                                        class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
                                        onchange="document.getElementById('subject_name_hidden').value=this.options[this.selectedIndex].text.split(' | ')[0]">
                                    <option value="" disabled selected>-- Select an Active Class --</option>
                                    <?php foreach ($classes as $class): ?>
                                    <option value="<?php echo $class['class_id']; ?>">
                                        <?php echo htmlspecialchars($class['subject']) . " | " . htmlspecialchars($class['class_name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" id="subject_name_hidden" name="subject" value="">
                            </div>

                            <div>
                                <label for="live_url" class="block text-sm font-medium text-gray-700 mb-2">YouTube Embed URL</label>
                                <input type="text" id="live_url" name="live_url" placeholder="Ex: https://www.youtube.com/embed/dQw4w9WgXcQ" required
                                       class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                                <p class="text-xs text-gray-500 mt-1">Use a YouTube *Embed* URL (e.g. `https://www.youtube.com/embed/VIDEO_ID`) or similar.</p>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-100">
                            <button type="submit" name="set_live" class="px-6 py-3 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition transform hover:-translate-y-0.5">
                                <i class="fas fa-broadcast-tower mr-2"></i> Go Live Now
                            </button>
                        </div>
                    </form>
                </div>

                <?php endif; ?>
            </main>
        </div>
    </div>
</body>
</html>