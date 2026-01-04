<?php
// teacher_portal/pages/upload_materials.php
// Updated: Fixed Page Refresh Issue (Form Resubmission) using Redirect

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../../includes/connection.php');

// ගුරුවරයාගේ දත්ත ලබා ගැනීම
$teacher_db_id = $_SESSION['teacher_db_id'] ?? null;

if (!$teacher_db_id) {
    header("Location: ../../log/login.php");
    exit();
}

// ගුරුවරයාගේ නම ලබා ගැනීම
$t_sql = "SELECT full_name, subject FROM teachers WHERE teacher_id = ?";
$t_stmt = $conn->prepare($t_sql);
$t_stmt->bind_param("i", $teacher_db_id);
$t_stmt->execute();
$t_res = $t_stmt->get_result();
$teacher_data = $t_res->fetch_assoc();
$teacher_name = $teacher_data['full_name'];
$teacher_subject = $teacher_data['subject'];

// ගුරුවරයාට අදාළ පන්ති ලැයිස්තුව ලබා ගැනීම
$class_sql = "SELECT class_id, class_name FROM classes WHERE teacher_name = ?";
$class_stmt = $conn->prepare($class_sql);
$class_stmt->bind_param("s", $teacher_name);
$class_stmt->execute();
$classes_result = $class_stmt->get_result();

$message = "";
$msg_type = "";

// ---------------------------------------------------------
// 1. Success Message එක URL එකෙන් ලබා ගැනීම (Redirect වූ පසු)
// ---------------------------------------------------------
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'success') {
        $message = "Material uploaded successfully!";
        $msg_type = "green";
    } elseif ($_GET['msg'] == 'error_db') {
        $message = "Database Error occurred.";
        $msg_type = "red";
    } elseif ($_GET['msg'] == 'error_upload') {
        $message = "File upload failed.";
        $msg_type = "red";
    } elseif ($_GET['msg'] == 'error_type') {
        $message = "Invalid file type.";
        $msg_type = "red";
    } elseif ($_GET['msg'] == 'error_file') {
        $message = "Please select a file.";
        $msg_type = "red";
    }
}

// ---------------------------------------------------------
// 2. Form Submission Handling (POST)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_material'])) {

    $title = trim($_POST['material_title']);
    $class_id = intval($_POST['class_id']);

    // File Validation
    if (isset($_FILES['material_file']) && $_FILES['material_file']['error'] == 0) {
        $allowed = ['pdf', 'doc', 'docx', 'zip', 'rar', 'jpg', 'png'];
        $filename = $_FILES['material_file']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            // Upload Path
            $upload_dir = "../../uploads/study_materials/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $new_filename = "mat_" . time() . "_" . uniqid() . "." . $ext;
            $target_file = $upload_dir . $new_filename;
            $db_path = "uploads/study_materials/" . $new_filename;

            if (move_uploaded_file($_FILES['material_file']['tmp_name'], $target_file)) {
                
                // Status = 1 (Approved / Live immediately)
                $sql = "INSERT INTO study_materials (title, class_id, file_path, uploaded_by, uploaded_on, status) VALUES (?, ?, ?, ?, NOW(), 1)";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sisi", $title, $class_id, $db_path, $teacher_db_id);

                if ($stmt->execute()) {
                    // === SOLUTION ===
                    // සාර්ථක වූ පසු Redirect කරන්න. එවිට Refresh කළාට ප්‍රශ්නයක් නැත.
                    header("Location: upload_materials.php?msg=success");
                    exit(); 
                } else {
                    header("Location: upload_materials.php?msg=error_db");
                    exit();
                }
            } else {
                header("Location: upload_materials.php?msg=error_upload");
                exit();
            }
        } else {
            header("Location: upload_materials.php?msg=error_type");
            exit();
        }
    } else {
        header("Location: upload_materials.php?msg=error_file");
        exit();
    }
}

include("../include/head.php");
include("../include/sidebar.php");
?>

<div class="p-4 sm:ml-64 pt-20 pb-20">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Upload Study Materials</h2>

        <?php if ($message): ?>
            <div class="p-4 mb-4 text-sm rounded-lg flex items-center gap-2 <?php echo $msg_type == 'green' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                <i class="fas <?php echo $msg_type == 'green' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Material Title</label>
                    <input type="text" name="material_title"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" required
                        placeholder="Ex: Combined Maths Unit 1 Tute">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Select Class</label>
                    <select name="class_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" required>
                        <option value="">-- Choose Class --</option>
                        <?php while ($row = $classes_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['class_id']; ?>">
                                <?php echo htmlspecialchars($row['class_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Upload File (PDF, DOC, ZIP, IMG)</label>
                    <input type="file" name="material_file"
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                        required>
                    <p class="mt-1 text-xs text-gray-500">Allowed formats: PDF, Word, Zip, JPG, PNG.</p>
                </div>
            </div>
            <button type="submit" name="upload_material"
                class="mt-6 text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 transition flex items-center gap-2">
                <i class="fas fa-cloud-upload-alt"></i> Upload Material
            </button>
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">My Upload Status</h3>
        </div>
        <div class="p-6">
            <?php
            $his_sql = "SELECT sm.*, c.class_name FROM study_materials sm 
                        JOIN classes c ON sm.class_id = c.class_id 
                        WHERE sm.uploaded_by = ? ORDER BY sm.uploaded_on DESC LIMIT 10";
            $his_stmt = $conn->prepare($his_sql);
            $his_stmt->bind_param("i", $teacher_db_id);
            $his_stmt->execute();
            $res = $his_stmt->get_result();
            ?>

            <?php if ($res->num_rows > 0): ?>
                <ul class="divide-y divide-gray-100">
                    <?php while ($row = $res->fetch_assoc()): 
                        $status_label = "";
                        // Status Logic
                        if ($row['status'] == 1) {
                            $status_label = '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded border border-green-200"><i class="fas fa-check-circle"></i> Approved</span>';
                        } elseif ($row['status'] == 2) {
                            $status_label = '<span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded border border-red-200"><i class="fas fa-times-circle"></i> Rejected</span>';
                        } else {
                            $status_label = '<span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded border border-yellow-200"><i class="fas fa-clock"></i> Pending</span>';
                        }
                    ?>
                        <li class="py-4 flex justify-between items-center hover:bg-gray-50 transition px-2 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900"><?php echo htmlspecialchars($row['title']); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo htmlspecialchars($row['class_name']); ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <?php echo $status_label; ?>
                                <p class="text-[10px] text-gray-400 mt-1"><?php echo date('Y-m-d H:i', strtotime($row['uploaded_on'])); ?></p>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="text-gray-500 text-center py-4">No uploads found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include("../include/footer.php"); ?>