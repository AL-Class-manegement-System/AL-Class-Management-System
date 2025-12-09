<?php
session_start();
// db_con.php සහ auth.php අනිවාර්යයෙන්ම තිබිය යුතුය.
include('includes/auth.php'); 
include('db_con.php');

$message = null;
$error = null;

// ==========================================
// ACTION: Delete Material (Secure)
// ==========================================
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // 1. Fetch file_path and delete the physical file (optional, but good practice)
    $path_sql = "SELECT file_path FROM study_materials WHERE material_id = ?";
    $path_stmt = $conn->prepare($path_sql);
    
    if ($path_stmt) {
        $path_stmt->bind_param("i", $id);
        $path_stmt->execute();
        $path_result = $path_stmt->get_result();
        
        if ($path_result->num_rows > 0) {
            $row = $path_result->fetch_assoc();
            $file_path = '../' . $row['file_path']; 
            if (!empty($row['file_path']) && file_exists($file_path)) {
                 // unlink($file_path); // Uncomment this line to enable physical file deletion
            }
        }
        $path_stmt->close();
    }
    
    // 2. Delete the record from the database (Prepared Statement)
    $delete_sql = "DELETE FROM study_materials WHERE material_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    
    if ($delete_stmt) {
        $delete_stmt->bind_param("i", $id);
        if ($delete_stmt->execute()) {
            $message = "Study material deleted successfully (Record removed).";
        } else {
            $error = "Error deleting record: " . $delete_stmt->error;
        }
        $delete_stmt->close();
    } else {
        $error = "Database Prepare Error (Delete)";
    }
    header("Location: manage_study_materials.php?msg=" . urlencode($message) . "&err=" . urlencode($error));
    exit();
}

// ==========================================
// ACTION: Status Toggle (Secure)
// ==========================================
if (isset($_GET['action']) && $_GET['action'] == 'toggle_status' && isset($_GET['id']) && isset($_GET['current_status'])) {
    $id = intval($_GET['id']);
    $current_status = intval($_GET['current_status']);
    $new_status = ($current_status == 1) ? 0 : 1;
    
    $sql = "UPDATE study_materials SET status = ? WHERE material_id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ii", $new_status, $id);
        if ($stmt->execute()) {
            $message = "Material status updated successfully!";
        } else {
            $error = "Error updating status: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Database Prepare Error (Toggle)";
    }
    header("Location: manage_study_materials.php?msg=" . urlencode($message) . "&err=" . urlencode($error));
    exit();
}

// Check for messages/errors from redirects
if (isset($_GET['msg'])) $message = htmlspecialchars($_GET['msg']);
if (isset($_GET['err']) && $_GET['err'] !== 'null') $error = htmlspecialchars($_GET['err']);


// ==========================================
// DATA FETCHING: All Study Materials
// ==========================================
$materials = [];
// **වැඩිදියුණු කළ JOIN:** LEFT JOIN භාවිතයෙන්, teacher_id 0 වුවත් දත්ත පෙන්වයි.
$sql_materials = "SELECT 
                    sm.*, 
                    COALESCE(t.full_name, 'Unknown (ID: ' || sm.teacher_id || ')') AS teacher_name 
                  FROM study_materials sm
                  LEFT JOIN teachers t ON sm.teacher_id = t.teacher_id
                  ORDER BY sm.upload_date DESC, sm.material_id DESC"; 
$result_materials = $conn->query($sql_materials);

if ($result_materials && $result_materials->num_rows > 0) {
    while($row = $result_materials->fetch_assoc()) {
        $materials[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Study Material Management | Admin</title>
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
                <h1 class="text-3xl font-bold text-gray-800 mb-6">📚 Study Material Management</h1>
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

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <h2 class="text-xl font-bold text-gray-700 p-4 border-b border-gray-100">All Uploaded Materials (<?php echo count($materials); ?>)</h2>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Title / Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Subject / Teacher</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Uploaded Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (!empty($materials)): ?>
                                    <?php foreach($materials as $material): 
                                        $is_active = $material['status'] == 1;
                                        $status_color = $is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                        $status_text = $is_active ? 'Active' : 'Inactive';
                                        $view_path = '../' . htmlspecialchars($material['file_path']);
                                    ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold text-gray-900"><?php echo htmlspecialchars($material['material_title']); ?></div>
                                                <div class="text-xs text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded inline-block mt-1">
                                                    <?php echo htmlspecialchars($material['material_type']); ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div class="font-medium text-gray-700"><?php echo htmlspecialchars($material['subject_name']); ?></div>
                                                <div class="text-xs text-gray-500">Teacher: <?php echo htmlspecialchars($material['teacher_name']); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('M d, Y', strtotime($material['upload_date'])); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded-full <?php echo $status_color; ?>">
                                                    <?php echo $status_text; ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-2">
                                                
                                                <a href="<?php echo $view_path; ?>" target="_blank"
                                                   class="text-blue-600 hover:text-blue-800 bg-blue-50 p-2 rounded-lg transition" title="View File">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="manage_study_materials.php?action=toggle_status&id=<?php echo $material['material_id']; ?>&current_status=<?php echo $material['status']; ?>" 
                                                   onclick="return confirm('Are you sure you want to change the status of this material?');"
                                                   class="<?php echo $is_active ? 'text-red-600 bg-red-50 hover:bg-red-100' : 'text-green-600 bg-green-50 hover:bg-green-100'; ?> p-2 rounded-lg transition" 
                                                   title="<?php echo $is_active ? 'Deactivate (Hide from Students)' : 'Activate (Show to Students)'; ?>">
                                                    <i class="fas <?php echo $is_active ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                                                </a>

                                                <a href="manage_study_materials.php?action=delete&id=<?php echo $material['material_id']; ?>" 
                                                   onclick="return confirm('WARNING: Are you sure you want to permanently delete this material and its record?');"
                                                   class="text-gray-600 hover:text-gray-900 bg-gray-100 p-2 rounded-lg transition" title="Delete Permanently">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-6 text-gray-500">No study materials have been uploaded by teachers yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>
</body>
</html>