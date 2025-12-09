<?php
// 1. Session Start 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// back to login
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../../log/login.php");
    exit();
}

// 3. Path variable setup
$path = "../../"; 
$page_title = "Upload Study Materials"; 

// 4. Include Head and Connection
include("../include/head.php"); 
require_once $path . 'includes/connection.php'; 

// ==========================================
// TEACHER DATA SETUP
// ==========================================
$teacher_id = $_SESSION['teacher_id'] ?? null; 
$teacher_name = $_SESSION['full_name'] ?? 'Teacher';
$teacher_subject = $_SESSION['subject'] ?? 'General';

if (empty($teacher_id)) {
    header("Location: ../../log/login.php");
    exit();
}

// ==========================================
// FILE UPLOAD AND DB INSERT LOGIC
// ==========================================
$message = "";
$msg_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_material'])) {
    
    $material_title = trim($_POST['material_title']);
    $material_type = $_POST['material_type'];
    $subject_name = $teacher_subject;

    if (isset($_FILES['material_file']) && $_FILES['material_file']['error'] == 0) {
        
        $subject_folder = str_replace([' ', '/', '&'], '_', strtolower($subject_name));
        $target_dir = $path . "assets/study_materials/" . $subject_folder . "/"; 
        
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                $message = "Error: Could not create upload directory. Check server permissions.";
                $msg_type = "red";
                goto end_upload_logic;
            }
        }

        $file_name = basename($_FILES["material_file"]["name"]);
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $new_file_name = uniqid('material_') . '.' . $file_extension;
        $target_file = $target_dir . $new_file_name;
        
        $uploadOk = 1;

        if ($_FILES["material_file"]["size"] > 50000000) {
            $message = "Sorry, your file is too large (max 50MB).";
            $msg_type = "red";
            $uploadOk = 0;
        } 
        if (!in_array($file_extension, ['pdf', 'doc', 'docx', 'zip', 'rar'])) {
            $message = "Sorry, only PDF, DOC/DOCX, ZIP & RAR files are allowed.";
            $msg_type = "red";
            $uploadOk = 0;
        }
        
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["material_file"]["tmp_name"], $target_file)) {
                
                $relative_path = "assets/study_materials/" . $subject_folder . "/" . $new_file_name;

                // 🌟 teacher_id ඇතුළත් කිරීම
                $sql = "INSERT INTO study_materials (teacher_id, subject_name, material_title, material_type, file_path, upload_date, status) VALUES (?, ?, ?, ?, ?, CURDATE(), 1)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("issss", $teacher_id, $subject_name, $material_title, $material_type, $relative_path);

                if ($stmt->execute()) {
                    $message = "Study material '" . htmlspecialchars($material_title) . "' uploaded successfully!";
                    $msg_type = "green";
                } else {
                    $message = "File uploaded, but database error: " . $conn->error;
                    $msg_type = "red";
                    unlink($target_file);
                }
                $stmt->close();
            } else {
                $message = "Sorry, there was an error uploading your file. Check server permissions.";
                $msg_type = "red";
            }
        }
    } else {
        $message = "Please select a file to upload.";
        $msg_type = "red";
    }
}
end_upload_logic:
?>

<?php include("../include/sidebar.php"); ?>

<div class="p-4 sm:ml-64 pb-20">
    <div class="flex items-center justify-between p-4 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm">
        <h2 class="text-xl font-bold text-gray-800"><?php echo $page_title; ?></h2>
        <div class="text-sm font-semibold text-gray-600">
            Your Subject: <span class="text-blue-600 font-bold"><?php echo htmlspecialchars($teacher_subject); ?></span>
        </div>
    </div>

    <?php if ($message): 
        $colorClass = ($msg_type == 'green') ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200';
    ?>
    <div id="msg" class="<?php echo $colorClass; ?> px-4 py-3 rounded-lg text-sm font-medium mb-6 shadow-sm border flex items-center gap-2">
        <i class="ph ph-info text-lg"></i> <?php echo $message; ?>
    </div>
    <?php endif; ?>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
        <h5 class="text-lg font-bold leading-none text-gray-900 mb-6">Upload New Material</h5>
        
        <form method="POST" action="" enctype="multipart/form-data">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div>
                    <label for="material_title" class="block mb-2 text-sm font-medium text-gray-900">Material Title</label>
                    <input type="text" id="material_title" name="material_title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required placeholder="e.g., Integration Past Paper 2023">
                </div>

                <div>
                    <label for="material_type" class="block mb-2 text-sm font-medium text-gray-900">Material Type</label>
                    <select id="material_type" name="material_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                        <option value="">Select Type</option>
                        <option value="Notes">Lesson Notes</option>
                        <option value="Past Paper">Past Paper</option>
                        <option value="Model Paper">Model Paper</option>
                        <option value="Assignment">Assignment / Worksheet</option>
                        <option value="Reading">Extra Reading Material</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="material_file" class="block mb-2 text-sm font-medium text-gray-900">Upload File (PDF, DOCX, ZIP/RAR max 50MB)</label>
                    <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2" 
                           id="material_file" name="material_file" type="file" required accept=".pdf,.doc,.docx,.zip,.rar">
                </div>
            </div>

            <button type="submit" name="upload_material" class="mt-6 text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors">
                <i class="ph ph-upload simple-bold mr-2"></i> Upload Material
            </button>
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <h5 class="text-lg font-bold leading-none text-gray-900 mb-6">Your Recently Uploaded Materials</h5>
        
        <?php
        // 🌟 teacher_id මත පදනම්ව filter කිරීම
        $list_sql = "SELECT material_title, material_type, upload_date, file_path FROM study_materials WHERE teacher_id = ? AND status = 1 ORDER BY upload_date DESC LIMIT 10";
        $list_stmt = $conn->prepare($list_sql);
        $list_stmt->bind_param("i", $teacher_id);
        $list_stmt->execute();
        $list_result = $list_stmt->get_result();

        if ($list_result->num_rows > 0) {
            echo '<ul class="divide-y divide-gray-200">';
            while ($item = $list_result->fetch_assoc()) {
                // File path: teacher_portal/pages/ සිට project root වෙත යාමට '../../'
                $download_path = '../../' . htmlspecialchars($item['file_path']);
                
                echo '<li class="py-3 sm:py-4">';
                echo '<div class="flex items-center justify-between space-x-4 rtl:space-x-reverse">';
                
                echo '<div class="flex-1 min-w-0 flex items-center gap-3">';
                echo '<div class="flex-shrink-0 text-blue-500"><i class="ph ph-file-text text-xl"></i></div>';
                echo '<div>';
                echo '<p class="text-sm font-medium text-gray-900 truncate">' . htmlspecialchars($item['material_title']) . '</p>';
                echo '<p class="text-xs text-gray-500 truncate">' . htmlspecialchars($item['material_type']) . ' | Uploaded: ' . date('M d, Y', strtotime($item['upload_date'])) . '</p>';
                echo '</div>';
                echo '</div>';
                
                echo '<a href="' . $download_path . '" target="_blank" class="inline-flex items-center text-xs font-semibold text-white bg-blue-500 hover:bg-blue-600 px-3 py-1.5 rounded-lg transition">';
                echo '<i class="ph ph-download-simple text-sm mr-1"></i> View';
                echo '</a>';
                
                echo '</div>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p class="text-sm text-gray-500">No materials uploaded for '.htmlspecialchars($teacher_subject).' yet.</p>';
        }
        $list_stmt->close();
        ?>
    </div>

    <?php include("../include/footer.php"); ?>
</div>