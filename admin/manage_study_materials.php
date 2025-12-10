<?php
session_start();
// db_con.php and auth.php are required.
// --- PLACEHOLDER FOR INCLUDES (ASSUMING THEY EXIST) ---
// include('includes/auth.php'); 
// include('db_con.php');

// Placeholder for Database Connection (Replace with actual db_con.php content)
class MockDB {
    public $conn;
    public function __construct() {
        // Mock connection details - user must replace with actual DB connection logic
        // This is necessary to prevent errors if the file is run without a real database setup.
        // For a real application, the include('db_con.php'); would handle this.
        $this->conn = new mysqli('localhost', 'user', 'password', 'database');
    }
    public function query($sql) { /* Mock */ }
    public function prepare($sql) { /* Mock */ }
}
// Placeholder for Authentication (assuming it sets $_SESSION['admin_id'])
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['admin_id'] = 1; // Default to 1 for demonstration if auth.php is missing
}

// Ensure includes/auth.php and db_con.php are functioning in a real environment
include('includes/auth.php'); 
include('db_con.php');
// --- END PLACEHOLDER ---


$message = null;
$error = null;

// ==========================================
// 1. Fetch Classes for the form (for dropdowns)
// ==========================================
$classes = [];
$class_sql = "SELECT class_id, class_name FROM classes ORDER BY class_name";
$class_result = $conn->query($class_sql);
if ($class_result && $class_result->num_rows > 0) {
    while ($row = $class_result->fetch_assoc()) {
        $classes[] = $row;
    }
}


// ==========================================
// 2. ACTION: Add New Material (Form Submission)
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_material'])) {
    
    $title = trim($_POST['material_title']);
    $class_id = intval($_POST['class_id']);
    
    $uploaded_by = $_SESSION['admin_id'] ?? 1; 
    $upload_dir = 'uploads/study_materials/'; 
    $file_path = '';
    // Added more common file types
    $allowed_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar'];

    // 1. Input Validation (Checking only Title and Class ID)
    if (empty($title) || $class_id <= 0) {
        $error = "All fields (Title, Class) are mandatory."; 
    } 
    // 2. File Upload Handling
    elseif (isset($_FILES['material_file']) && $_FILES['material_file']['error'] == 0) {
        $file = $_FILES['material_file'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            $error = "Only allowed file types (PDF, DOCX, JPG, etc.) can be uploaded.";
        } else {
            $new_file_name = uniqid('material_') . '.' . $file_extension;
            // IMPORTANT: Using real path for moving file
            $target_file = '../' . $upload_dir . $new_file_name; 
            $db_file_path = $upload_dir . $new_file_name; 

            // Create directory if it doesn't exist
            if (!is_dir('../' . $upload_dir)) {
                // Use a safer permission setting like 0755 or 0777 only if needed
                if (!mkdir('../' . $upload_dir, 0777, true)) {
                    $error = "Failed to create upload directory.";
                }
            }

            if (empty($error) && move_uploaded_file($file["tmp_name"], $target_file)) { 
                $file_path = $db_file_path;
            } else if (empty($error)) {
                $error = "An error occurred during file upload. Please check folder permissions (0777 for uploads).";
            }
        }
    } else {
        $error = "Please select a file to upload or check file size/upload limits.";
    }

    // 3. Database Insertion
    if (empty($error) && !empty($file_path)) {
        // Query adjusted: subject_id removed
        $sql = "INSERT INTO study_materials (title, class_id, file_path, uploaded_by, uploaded_on, status) 
                VALUES (?, ?, ?, ?, NOW(), 1)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            // "sisi" -> string, int, string, int (title, class_id, file_path, uploaded_by)
            $stmt->bind_param("sisi", $title, $class_id, $file_path, $uploaded_by);
            
            if ($stmt->execute()) {
                $message = "New study material added successfully.";
                // Removed redirect for instant message display, but kept the functionality as it was good practice.
                // header("Location: manage_study_materials.php?msg=" . urlencode($message));
                // exit(); 
            } else {
                $error = "Database insertion error (Check table structure): " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "SQL statement preparation error: " . $conn->error;
        }
    }
}
// ==========================================
// END: Add New Material Logic
// ==========================================


// ==========================================
// ACTION: Delete Material (Secure) 
// ** IMPROVEMENT: ADDED FILE UNLINK **
// ==========================================
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $success = false;
    
    // 1. Fetch file_path
    $path_sql = "SELECT file_path FROM study_materials WHERE material_id = ?";
    $path_stmt = $conn->prepare($path_sql);
    
    if ($path_stmt) {
        $path_stmt->bind_param("i", $id);
        $path_stmt->execute();
        $path_result = $path_stmt->get_result();
        
        if ($path_result->num_rows > 0) {
            $row = $path_result->fetch_assoc();
            $file_path = '../' . $row['file_path']; // Prepend '..' as file path is relative to the script
            
            // Check if file_path is set AND if the file exists before attempting to delete it
            if (!empty($row['file_path']) && file_exists($file_path)) {
                 if (!unlink($file_path)) {
                     // Log or handle the error, but proceed with DB deletion if file unlink fails
                     error_log("Failed to unlink study material file: " . $file_path);
                 }
            }
        }
        $path_stmt->close();
        
        // 2. Delete the record from the database
        $delete_sql = "DELETE FROM study_materials WHERE material_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        
        if ($delete_stmt) {
            $delete_stmt->bind_param("i", $id);
            if ($delete_stmt->execute()) {
                $message = "Study material and associated file deleted successfully.";
                $success = true;
            } else {
                $error = "Error deleting record: " . $delete_stmt->error;
            }
            $delete_stmt->close();
        } else {
            $error = "Database Prepare Error (Delete)";
        }
    } else {
        $error = "Database Prepare Error (Fetch Path)";
    }
    
    // Redirect to prevent re-submission
    header("Location: manage_study_materials.php?msg=" . urlencode($message) . "&err=" . urlencode($error));
    exit();
}
// ==========================================
// END: Delete Material Logic
// ==========================================

// ==========================================
// ACTION: Toggle Active Status 
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
// ==========================================
// END: Toggle Status Logic
// ==========================================


// Check for messages/errors from redirects
// Added a check for 'null' string in error to prevent displaying empty error boxes.
if (isset($_GET['msg'])) $message = htmlspecialchars($_GET['msg']);
if (isset($_GET['err']) && $_GET['err'] !== 'null' && $_GET['err'] !== '') $error = htmlspecialchars($_GET['err']);


// ==========================================
// 3. Fetch All Study Materials
// ==========================================
$materials = [];
$fetch_sql = "
    SELECT 
        sm.*, 
        c.class_name
    FROM 
        study_materials sm
    JOIN 
        classes c ON sm.class_id = c.class_id
    ORDER BY 
        sm.uploaded_on DESC
";
$fetch_result = $conn->query($fetch_sql);
if ($fetch_result && $fetch_result->num_rows > 0) {
    while ($row = $fetch_result->fetch_assoc()) {
        $materials[] = $row;
    }
}

// Close connection after all database operations
if (isset($conn)) {
    $conn->close();
}


// ==========================================
// HTML Output Starts
// ==========================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Study Material Management | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Poppins', sans-serif; } 
        /* Ensure the main content pushes past the sidebar */
        .page-wrapper { margin-left: 16rem; } /* Equivalent to ml-64 (256px) */
        @media (max-width: 1024px) {
            .page-wrapper { margin-left: 0; } /* Adjust for smaller screens */
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">
    
    <?php 
    // Assuming includes/sidebar.php exists and provides the fixed sidebar
    include('includes/sidebar.php'); 
    ?>

    <div class="page-wrapper min-h-screen p-4 sm:p-8">
        <main class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">📚 Study Material Management</h1>
            <hr class="mb-6">

            <?php if ($message): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <p><?php echo $message; ?></p>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <p class="font-bold">Error</p>
                    <p class="text-sm"><?php echo $error; ?></p>
                </div>
            <?php endif; ?>
            
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8 mb-8">
                <h2 class="text-xl font-bold text-gray-700 mb-4 flex items-center gap-2 border-b pb-2">
                    <i class="fas fa-plus-circle text-indigo-600"></i> Upload New Study Material
                </h2>
                
                <form method="POST" action="manage_study_materials.php" enctype="multipart/form-data" class="space-y-6"> 
                    <input type="hidden" name="add_material" value="1">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <div>
                            <label for="material_title" class="block text-sm font-medium text-gray-700 mb-1">Material Title</label>
                            <input type="text" name="material_title" id="material_title" required placeholder="Ex: Physics Theory Paper 2024"
                                   class="mt-1 block w-full px-4 py-2 border-gray-300 rounded-lg shadow-sm border focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div class="md:col-span-2"> 
                            <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1">Select Relevant Class</label>
                            <select name="class_id" id="class_id" required
                                    class="mt-1 block w-full px-4 py-2 border-gray-300 rounded-lg shadow-sm border bg-white focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Select Class --</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?php echo $class['class_id']; ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        </div>
                    
                    <div class="pt-4">
                        <label for="material_file" class="block text-sm font-medium text-gray-700 mb-1">File to Upload</label>
                        <input type="file" name="material_file" id="material_file" required
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition cursor-pointer bg-gray-50 rounded-lg border border-gray-200"/>
                        <p class="mt-1 text-xs text-gray-500">Allowed types: PDF, DOCX, JPG, PNG, ZIP, etc. Max file size depends on PHP settings. **File will be deleted from server upon record deletion.**</p>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <button type="submit" 
                                class="px-6 py-2.5 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition duration-150 shadow-lg shadow-indigo-500/30">
                            <i class="fas fa-upload mr-2"></i> Upload & Save Material
                        </button>
                    </div>
                </form>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <h2 class="text-xl font-bold text-gray-700 p-4 border-b border-gray-100">Uploaded Study Materials</h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Class</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($materials)): ?>
                                <?php foreach ($materials as $material): ?>
                                    <?php 
                                    // Use 'status' column from DB
                                    $is_active = $material['status']; 
                                    ?>
                                    <tr class="hover:bg-gray-50 <?php echo $is_active ? '' : 'text-gray-500'; ?>">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <a href="../<?php echo htmlspecialchars($material['file_path']); ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
                                                <?php echo htmlspecialchars($material['title']); ?>
                                            </a>
                                            <div class="text-xs text-gray-400 mt-1">Uploaded: <?php echo date('Y-m-d', strtotime($material['uploaded_on'])); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($material['class_name']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                  <?php echo $is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo $is_active ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-2">
                                            
                                            <a href="manage_study_materials.php?action=toggle_status&id=<?php echo $material['material_id']; ?>&current_status=<?php echo $material['status']; ?>" 
                                               onclick="return confirm('Are you sure you want to change the status of this material?');"
                                               class="<?php echo $is_active ? 'text-red-600 bg-red-50 hover:bg-red-100' : 'text-green-600 bg-green-50 hover:bg-green-100'; ?> p-2 rounded-lg transition" 
                                               title="<?php echo $is_active ? 'Deactivate (Hide from Students)' : 'Activate (Show to Students)'; ?>">
                                                <i class="fas <?php echo $is_active ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                                            </a>

                                            <a href="manage_study_materials.php?action=delete&id=<?php echo $material['material_id']; ?>" 
                                               onclick="return confirm('WARNING: Are you sure you want to permanently delete this material and its record? This will also delete the uploaded file from the server.');"
                                               class="text-gray-600 hover:text-gray-900 bg-gray-100 p-2 rounded-lg transition" title="Delete Permanently">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-6 text-gray-500">No study materials have been uploaded yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            </main>
    </div>
</body>
</html>