<?php
// teacher_portal/pages/upload_materials.php
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

// ගුරුවරයාගේ නම ලබා ගැනීම (Classes table එකේ ඇත්තේ නම නිසා)
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_material'])) {

    $title = trim($_POST['material_title']);
    $class_id = intval($_POST['class_id']);

    // File Validation
    if (isset($_FILES['material_file']) && $_FILES['material_file']['error'] == 0) {
        $allowed = ['pdf', 'doc', 'docx', 'zip', 'rar', 'jpg', 'png'];
        $filename = $_FILES['material_file']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            // Upload Path: admin/uploads/study_materials/ (Admin folder එකට සාපේක්ෂව නොව Root එකට සාපේක්ෂව)
            // අපට අවශ්‍යයි `uploads/study_materials/` එකට දාන්න.

            // අපි ඉන්නේ teacher_portal/pages/ වල. Root එකට යන්න ../../
            $upload_dir = "../../uploads/study_materials/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $new_filename = "mat_" . time() . "_" . uniqid() . "." . $ext;
            $target_file = $upload_dir . $new_filename;

            // Database එකේ save කරන path එක (Root එකේ සිට)
            $db_path = "uploads/study_materials/" . $new_filename;

            if (move_uploaded_file($_FILES['material_file']['tmp_name'], $target_file)) {
                // Status = 0 (Pending Approval)
                $sql = "INSERT INTO study_materials (title, class_id, file_path, uploaded_by, uploaded_on, status) VALUES (?, ?, ?, ?, NOW(), 0)";
                $stmt = $conn->prepare($sql);
                // uploaded_by සඳහා teacher_id එක යොදමු (Admin නොවෙන නිසා)
                $stmt->bind_param("sisi", $title, $class_id, $db_path, $teacher_db_id);

                if ($stmt->execute()) {
                    $message = "Material uploaded successfully! Waiting for Admin Approval.";
                    $msg_type = "green";
                } else {
                    $message = "Database Error.";
                    $msg_type = "red";
                }
            } else {
                $message = "File upload failed.";
                $msg_type = "red";
            }
        } else {
            $message = "Invalid file type.";
            $msg_type = "red";
        }
    }
}

include("../include/head.php");
include("../include/sidebar.php");
?>

<div class="p-4 sm:ml-64 pb-20">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Upload Study Materials (Teacher)</h2>

        <?php if ($message): ?>
            <div
                class="p-3 mb-4 text-sm rounded-lg <?php echo $msg_type == 'green' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Title</label>
                    <input type="text" name="material_title"
                        class="bg-gray-50 border border-gray-300 rounded-lg w-full p-2.5" required
                        placeholder="Ex: Unit 1 Tute">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Select Class</label>
                    <select name="class_id" class="bg-gray-50 border border-gray-300 rounded-lg w-full p-2.5" required>
                        <option value="">-- Choose Class --</option>
                        <?php while ($row = $classes_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['class_id']; ?>">
                                <?php echo htmlspecialchars($row['class_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">File</label>
                    <input type="file" name="material_file"
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50"
                        required>
                </div>
            </div>
            <button type="submit" name="upload_material"
                class="mt-4 text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">Upload
                & Request Approval</button>
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-bold mb-4">My Uploads</h3>
        <ul class="divide-y divide-gray-200">
            <?php
            $his_sql = "SELECT sm.*, c.class_name FROM study_materials sm 
                        JOIN classes c ON sm.class_id = c.class_id 
                        WHERE sm.uploaded_by = ? ORDER BY sm.uploaded_on DESC";
            $his_stmt = $conn->prepare($his_sql);
            $his_stmt->bind_param("i", $teacher_db_id);
            $his_stmt->execute();
            $res = $his_stmt->get_result();

            while ($row = $res->fetch_assoc()) {
                $status = ($row['status'] == 1) ? '<span class="text-green-600 font-bold">Approved</span>' :
                    (($row['status'] == 2) ? '<span class="text-red-600 font-bold">Rejected</span>' : '<span class="text-orange-500 font-bold">Pending</span>');

                echo "<li class='py-3 flex justify-between items-center'>
                        <div>
                            <p class='font-medium text-gray-900'>{$row['title']}</p>
                            <p class='text-xs text-gray-500'>{$row['class_name']} | $status</p>
                        </div>
                        <div class='text-xs text-gray-400'>{$row['uploaded_on']}</div>
                      </li>";
            }
            ?>
        </ul>
    </div>
</div>
<?php include("../include/footer.php"); ?>