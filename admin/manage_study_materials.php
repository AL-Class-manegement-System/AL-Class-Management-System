<?php
// admin/manage_study_materials.php
session_start();
include('includes/auth.php'); 
include('db_con.php');

$message = null;

// --- Approve / Reject Actions ---
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
        // Delete File
        $res = $conn->query("SELECT file_path FROM study_materials WHERE material_id = $id");
        $row = $res->fetch_assoc();
        if ($row && file_exists('../' . $row['file_path'])) {
            unlink('../' . $row['file_path']);
        }
        $conn->query("DELETE FROM study_materials WHERE material_id = $id");
        $message = "Material Deleted!";
    }
}

// --- Admin Direct Upload Logic ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_material'])) {
    $title = $_POST['material_title'];
    $class_id = $_POST['class_id'];
    $admin_id = $_SESSION['admin_id'] ?? 1;

    if (isset($_FILES['material_file']) && $_FILES['material_file']['error'] == 0) {
        $upload_dir = '../uploads/study_materials/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $filename = "admin_" . time() . "_" . $_FILES['material_file']['name'];
        if (move_uploaded_file($_FILES['material_file']['tmp_name'], $upload_dir . $filename)) {
            $db_path = "uploads/study_materials/" . $filename;
            $stmt = $conn->prepare("INSERT INTO study_materials (title, class_id, file_path, uploaded_by, uploaded_on, status) VALUES (?, ?, ?, ?, NOW(), 1)");
            $stmt->bind_param("sisi", $title, $class_id, $db_path, $admin_id);
            $stmt->execute();
            $message = "Admin Material Uploaded & Auto-Approved!";
        }
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
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

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
                        <?php while($row = $pending_res->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold">
                                <?php echo $row['title']; ?>
                                <br>
                                <a href="../<?php echo $row['file_path']; ?>" target="_blank" class="text-blue-500 text-xs underline">View File</a>
                            </td>
                            <td class="px-6 py-4"><?php echo $row['class_name']; ?></td>
                            <td class="px-6 py-4"><?php echo $row['teacher_name'] ?? 'Admin/Unknown'; ?></td>
                            <td class="px-6 py-4 flex gap-2">
                                <a href="?action=approve&id=<?php echo $row['material_id']; ?>" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Approve</a>
                                <a href="?action=reject&id=<?php echo $row['material_id']; ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Reject</a>
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
                    <input type="text" name="material_title" class="w-full border p-2 rounded" required>
                </div>
                <div class="w-full">
                    <label class="text-sm font-bold text-gray-600">Class</label>
                    <select name="class_id" class="w-full border p-2 rounded" required>
                        <?php while($c = $classes->fetch_assoc()) { echo "<option value='{$c['class_id']}'>{$c['class_name']} - {$c['subject']}</option>"; } ?>
                    </select>
                </div>
                <div class="w-full">
                    <label class="text-sm font-bold text-gray-600">File</label>
                    <input type="file" name="material_file" class="w-full border p-1 rounded" required>
                </div>
                <button type="submit" name="add_material" class="bg-indigo-600 text-white px-6 py-2.5 rounded font-bold hover:bg-indigo-700">Upload</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h3 class="font-bold text-gray-800">Approved & Active Materials</h3>
            </div>
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
                    <?php while($row = $active_res->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            <?php echo $row['title']; ?>
                            <a href="../<?php echo $row['file_path']; ?>" target="_blank" class="ml-2 text-blue-500 text-xs"><i class="fas fa-external-link-alt"></i></a>
                        </td>
                        <td class="px-6 py-4"><?php echo $row['class_name']; ?></td>
                        <td class="px-6 py-4"><?php echo date('Y-m-d', strtotime($row['uploaded_on'])); ?></td>
                        <td class="px-6 py-4">
                            <a href="?action=delete&id=<?php echo $row['material_id']; ?>" onclick="return confirm('Delete this file?')" class="text-red-600 hover:underline">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>