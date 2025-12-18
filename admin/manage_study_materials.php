<?php
// admin/manage_study_materials.php - Fixed Error Handling
session_start();
include('includes/auth.php');
include('db_con.php');

$message = null;
$error = null; // දෝෂ පෙන්වීමට අලුත් විචල්‍යයක්

// --- Approve / Reject / Delete Actions (Secure) ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == 'approve') {
        $conn->query("UPDATE study_materials SET status = 1 WHERE material_id = $id");
        $message = "Material Approved Successfully!";
    } elseif ($action == 'reject') {
        $conn->query("UPDATE study_materials SET status = 2 WHERE material_id = $id");
        $message = "Material Rejected!";
    } elseif ($action == 'delete') {
        // 1. Get File Path
        $stmt_path = $conn->prepare("SELECT file_path FROM study_materials WHERE material_id = ?");
        $stmt_path->bind_param("i", $id);
        $stmt_path->execute();
        $res = $stmt_path->get_result();
        
        if ($row = $res->fetch_assoc()) {
            $file_on_disk = '../' . $row['file_path']; // Adjust path relative to admin folder
            if (file_exists($file_on_disk)) {
                unlink($file_on_disk); // Delete physical file
            }
        }
        $stmt_path->close();

        // 2. Delete DB Record
        $conn->query("DELETE FROM study_materials WHERE material_id = $id");
        $message = "Material Deleted Successfully!";
    }
    
    // Redirect to clear query params (Optional but recommended)
    // header("Location: manage_study_materials.php");
}

// --- Admin Direct Upload Logic (Fixed) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_material'])) {
    $title = trim($_POST['material_title']);
    $class_id = intval($_POST['class_id']);
    $admin_id = $_SESSION['admin_id'] ?? 1; // Default to 1 if session missing

    // Check for upload errors
    if (isset($_FILES['material_file']) && $_FILES['material_file']['error'] == 0) {
        
        $upload_dir = '../uploads/study_materials/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Unique Filename
        $file_ext = strtolower(pathinfo($_FILES['material_file']['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'doc', 'docx', 'jpg', 'png', 'zip'];
        
        if (in_array($file_ext, $allowed)) {
            $filename = "admin_" . time() . "_" . uniqid() . "." . $file_ext;
            $target_file = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['material_file']['tmp_name'], $target_file)) {
                
                // DB Path (Relative to root)
                $db_path = "uploads/study_materials/" . $filename;

                // Insert into Database
                $stmt = $conn->prepare("INSERT INTO study_materials (title, class_id, file_path, uploaded_by, uploaded_on, status) VALUES (?, ?, ?, ?, NOW(), 1)");
                
                if ($stmt) {
                    $stmt->bind_param("sisi", $title, $class_id, $db_path, $admin_id);
                    
                    if ($stmt->execute()) {
                        $message = "Admin Material Uploaded & Auto-Approved!";
                    } else {
                        $error = "Database Error: " . $stmt->error;
                        // Upload වුන ෆයිල් එක මකන්න (DB Error එකක් නිසා)
                        unlink($target_file);
                    }
                    $stmt->close();
                } else {
                    $error = "Prepare Error: " . $conn->error;
                }

            } else {
                $error = "Failed to move uploaded file.";
            }
        } else {
            $error = "Invalid file type. Allowed: PDF, DOC, JPG, PNG, ZIP.";
        }
    } else {
        $error = "Please select a valid file.";
    }
}

// Fetch Pending Materials
$pending_sql = "SELECT sm.*, c.class_name, t.full_name as teacher_name 
                FROM study_materials sm 
                JOIN classes c ON sm.class_id = c.class_id 
                LEFT JOIN teachers t ON sm.uploaded_by = t.teacher_id 
                WHERE sm.status = 0 ORDER BY sm.uploaded_on DESC";
$pending_res = $conn->query($pending_sql);

// Fetch All Approved Materials
$active_sql = "SELECT sm.*, c.class_name FROM study_materials sm 
               JOIN classes c ON sm.class_id = c.class_id 
               WHERE sm.status = 1 ORDER BY sm.uploaded_on DESC";
$active_res = $conn->query($active_sql);

// Fetch Classes for Dropdown
$classes = $conn->query("SELECT * FROM classes WHERE status=1");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Study Materials</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 font-sans">
    <?php include('includes/sidebar.php'); ?>

    <div class="ml-64 p-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Study Material Management</h2>

        <?php if ($message): ?>
            <div id="status-alert" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm transition-opacity duration-500">
                <p class="font-bold">Success</p>
                <p><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div id="error-alert" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                <p class="font-bold">Error</p>
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <script>
            setTimeout(function () {
                const alerts = document.querySelectorAll('#status-alert, #error-alert');
                alerts.forEach(alert => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.style.display = 'none', 500);
                });
            }, 4000);
        </script>


        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-8 shadow-sm">
            <h3 class="text-xl font-bold text-yellow-800 mb-4 flex items-center gap-2">
                <i class="fas fa-clock"></i> Pending Teacher Approvals
            </h3>

            <?php if ($pending_res->num_rows > 0): ?>
                <div class="overflow-x-auto bg-white rounded-lg shadow">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Material</th>
                                <th class="px-6 py-3">Class</th>
                                <th class="px-6 py-3">Teacher</th>
                                <th class="px-6 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $pending_res->fetch_assoc()): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-bold">
                                        <?php echo htmlspecialchars($row['title']); ?>
                                        <br>
                                        <a href="../<?php echo $row['file_path']; ?>" target="_blank" class="text-blue-500 text-xs underline">View File</a>
                                    </td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['class_name']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['teacher_name'] ?? 'Admin/Unknown'); ?></td>
                                    <td class="px-6 py-4 flex gap-2">
                                        <a href="?action=approve&id=<?php echo $row['material_id']; ?>" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Approve</a>
                                        <a href="?action=reject&id=<?php echo $row['material_id']; ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" onclick="return confirm('Reject this material?');">Reject</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 italic">No pending materials for approval.</p>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-xl shadow p-6 mb-8 border border-gray-200">
            <h3 class="text-lg font-bold text-gray-700 mb-4">Direct Upload (Admin)</h3>
            <form method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="w-full">
                    <label class="text-sm font-bold text-gray-600">Title</label>
                    <input type="text" name="material_title" class="w-full border p-2 rounded focus:ring-2 focus:ring-indigo-500" required placeholder="Ex: Unit 1 Notes">
                </div>
                <div class="w-full">
                    <label class="text-sm font-bold text-gray-600">Class</label>
                    <select name="class_id" class="w-full border p-2 rounded focus:ring-2 focus:ring-indigo-500" required>
                        <?php while ($c = $classes->fetch_assoc()) {
                            echo "<option value='{$c['class_id']}'>{$c['class_name']} - {$c['subject']}</option>";
                        } ?>
                    </select>
                </div>
                <div class="w-full">
                    <label class="text-sm font-bold text-gray-600">File</label>
                    <input type="file" name="material_file" class="w-full border p-1.5 rounded bg-gray-50" required>
                </div>
                <button type="submit" name="add_material" class="bg-indigo-600 text-white px-6 py-2.5 rounded font-bold hover:bg-indigo-700 transition">Upload</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h3 class="font-bold text-gray-800">Approved & Active Materials</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Title</th>
                            <th class="px-6 py-3">Class</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($active_res->num_rows > 0): ?>
                            <?php while ($row = $active_res->fetch_assoc()): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        <?php echo htmlspecialchars($row['title']); ?>
                                        <a href="../<?php echo $row['file_path']; ?>" target="_blank" class="ml-2 text-indigo-500 hover:text-indigo-700 text-lg" title="View/Download">
                                            <i class="fas fa-file-download"></i>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['class_name']); ?></td>
                                    <td class="px-6 py-4"><?php echo date('Y-m-d', strtotime($row['uploaded_on'])); ?></td>
                                    <td class="px-6 py-4">
                                        <a href="?action=delete&id=<?php echo $row['material_id']; ?>" onclick="return confirm('Are you sure you want to delete this file?')" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-6">No active materials found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>