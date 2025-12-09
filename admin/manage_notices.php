<?php
session_start();
// db_con.php සහ auth.php අනිවාර්යයෙන්ම තිබිය යුතුය.
include('includes/auth.php'); 
include('db_con.php');

$message = null;
$error = null;

// ==========================================
// CRUD LOGIC: ADD/EDIT/DELETE
// ==========================================

// Handle POST request for Add or Edit
if (isset($_POST['submit_notice'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $status = isset($_POST['status']) ? 1 : 0;
    $notice_id = isset($_POST['notice_id']) ? intval($_POST['notice_id']) : 0;

    if ($notice_id > 0) {
        // UPDATE Existing Notice (Prepared Statement)
        // FIX: created_at යාවත්කාලීන නොකිරීමට, එය යාවත්කාලීන කරන්නේ නම් NOW() යොදන්න.
        $sql = "UPDATE notices SET title = ?, description = ?, status = ? WHERE notice_id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ssii", $title, $description, $status, $notice_id);
            if ($stmt->execute()) {
                $message = "Notice updated successfully!";
            } else {
                $error = "Error updating notice: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Database Prepare Error (Update): " . $conn->error;
        }

    } else {
        // INSERT New Notice (Prepared Statement)
        // මෙහිදී 'description' තීරුව DB හි නොමැති වීම නිසා ඔබට දෝෂය ඇති විය.
        $sql = "INSERT INTO notices (title, description, status, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ssi", $title, $description, $status);
            if ($stmt->execute()) {
                $message = "New notice posted successfully!";
            } else {
                // ඔබගේ දෝෂය මෙතැනින් පැමිණියේය.
                $error = "Error adding notice: " . $stmt->error; 
            }
            $stmt->close();
        } else {
            $error = "Database Prepare Error (Insert): " . $conn->error;
        }
    }
}

// Handle GET request for Delete (redirect-based)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "DELETE FROM notices WHERE notice_id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Notice deleted successfully!";
        } else {
            $error = "Error deleting notice: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Database Prepare Error (Delete)";
    }
    // Redirect with message/error
    header("Location: manage_notices.php?msg=" . urlencode($message) . "&err=" . urlencode($error));
    exit();
}

// Handle GET request for Status Toggle (redirect-based)
if (isset($_GET['action']) && $_GET['action'] == 'toggle_status' && isset($_GET['id']) && isset($_GET['current_status'])) {
    $id = intval($_GET['id']);
    $current_status = intval($_GET['current_status']);
    $new_status = ($current_status == 1) ? 0 : 1;
    
    $sql = "UPDATE notices SET status = ? WHERE notice_id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ii", $new_status, $id);
        if ($stmt->execute()) {
            $message = "Notice status updated successfully!";
        } else {
            $error = "Error updating status: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Database Prepare Error (Toggle)";
    }
    // Redirect with message/error
    header("Location: manage_notices.php?msg=" . urlencode($message) . "&err=" . urlencode($error));
    exit();
}

// Check for messages/errors from redirects
if (isset($_GET['msg'])) $message = htmlspecialchars($_GET['msg']);
if (isset($_GET['err']) && $_GET['err'] !== 'null') $error = htmlspecialchars($_GET['err']);


// ==========================================
// DATA FETCHING: All Notices
// ==========================================
$notices = [];
// Status=1 ඇති ඒවා මුලින්ම, අලුත්ම දේ මුලින්ම පෙන්වයි.
$sql_notices = "SELECT * FROM notices ORDER BY status DESC, created_at DESC"; 
$result_notices = $conn->query($sql_notices);

if ($result_notices && $result_notices->num_rows > 0) {
    while($row = $result_notices->fetch_assoc()) {
        $notices[] = $row;
    }
}

// Check if we are editing a notice (for form pre-population)
$edit_notice = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $edit_id = intval($_GET['id']);
    $stmt_edit = $conn->prepare("SELECT * FROM notices WHERE notice_id = ?");
    if($stmt_edit) {
        $stmt_edit->bind_param("i", $edit_id);
        $stmt_edit->execute();
        $edit_result = $stmt_edit->get_result();
        if ($edit_result->num_rows > 0) {
            $edit_notice = $edit_result->fetch_assoc();
        }
        $stmt_edit->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notice Board Management | Admin</title>
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
                <h1 class="text-3xl font-bold text-gray-800 mb-6">📢 Notice Board Management</h1>
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

                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8 mb-8">
                    <h2 class="text-xl font-bold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-plus-circle text-indigo-600"></i> 
                        <?php echo $edit_notice ? 'Edit Notice: ' . htmlspecialchars($edit_notice['title']) : 'Post New Notice'; ?>
                    </h2>
                    
                    <form method="POST" action="manage_notices.php" class="space-y-4">
                        <input type="hidden" name="notice_id" value="<?php echo $edit_notice ? htmlspecialchars($edit_notice['notice_id']) : '0'; ?>">

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                            <input type="text" id="title" name="title" value="<?php echo $edit_notice ? htmlspecialchars($edit_notice['title']) : ''; ?>" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description / Full Content</label>
                            <textarea id="description" name="description" rows="4" required
                                class="w-full p-4 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"><?php echo $edit_notice ? htmlspecialchars($edit_notice['description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="status" value="1" class="w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-green-500"
                                    <?php echo ($edit_notice === null || $edit_notice['status'] == 1) ? 'checked' : ''; ?>>
                                <span class="ml-3 text-sm font-medium text-gray-700">Make Active (Visible to students)</span>
                            </label>

                            <button type="submit" name="submit_notice" class="px-6 py-2 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 shadow-lg transition">
                                <i class="fas fa-save mr-2"></i> <?php echo $edit_notice ? 'Update Notice' : 'Post Notice'; ?>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <h2 class="text-xl font-bold text-gray-700 p-4 border-b border-gray-100">All Notices (<?php echo count($notices); ?>)</h2>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase w-1/4">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase w-1/2">Content Preview</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Posted Date</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (!empty($notices)): ?>
                                    <?php foreach($notices as $notice): 
                                        $is_active = $notice['status'] == 1;
                                        $status_color = $is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                        $status_text = $is_active ? 'Active' : 'Inactive';
                                    ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900"><?php echo htmlspecialchars($notice['title']); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-sm"><?php echo htmlspecialchars($notice['description']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('M d, Y', strtotime($notice['created_at'])); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded-full <?php echo $status_color; ?>">
                                                    <?php echo $status_text; ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-2">
                                                
                                                <a href="manage_notices.php?action=edit&id=<?php echo $notice['notice_id']; ?>" 
                                                   class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 p-2 rounded-lg transition" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <a href="manage_notices.php?action=toggle_status&id=<?php echo $notice['notice_id']; ?>&current_status=<?php echo $notice['status']; ?>" 
                                                   onclick="return confirm('Are you sure you want to change the status of this notice?');"
                                                   class="<?php echo $is_active ? 'text-red-600 bg-red-50 hover:bg-red-100' : 'text-green-600 bg-green-50 hover:bg-green-100'; ?> p-2 rounded-lg transition" 
                                                   title="<?php echo $is_active ? 'Deactivate' : 'Activate'; ?>">
                                                    <i class="fas <?php echo $is_active ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                                                </a>

                                                <a href="manage_notices.php?action=delete&id=<?php echo $notice['notice_id']; ?>" 
                                                   onclick="return confirm('WARNING: Are you sure you want to permanently delete this notice? This action cannot be undone.');"
                                                   class="text-gray-600 hover:text-gray-900 bg-gray-100 p-2 rounded-lg transition" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-6 text-gray-500">No notices have been posted yet.</td></tr>
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