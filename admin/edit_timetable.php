<?php
// admin/edit_timetable.php - Prepared Statements භාවිතයෙන් පන්ති තොරතුරු සංස්කරණය කිරීම
session_start();
include('includes/auth.php'); 
include('db_con.php');

$error = null;
$class_id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['class_id']) ? intval($_POST['class_id']) : 0);
$class_data = null;

// ==========================================
// 1. UPDATE LOGIC (POST Request)
// ==========================================
if (isset($_POST['update_class']) && $class_id > 0) {
    
    $class_name = trim($_POST['class_name']);
    $stream = trim($_POST['stream']);
    $subject = trim($_POST['subject']);
    $teacher_name = trim($_POST['teacher_name']);
    $day = trim($_POST['day']);
    $time = trim($_POST['time']);
    $fee = $_POST['fee'];
    $status = isset($_POST['status']) ? 1 : 0; 
    
    // UPDATE Query (Prepared Statement)
    // Placeholder ගණන: 9ක්
    $stmt_update = $conn->prepare("UPDATE classes SET class_name=?, stream=?, subject=?, teacher_name=?, fee=?, day=?, time=?, status=? WHERE class_id=?");
    
    if ($stmt_update) {
        // FIX: 'sssssssii' ලෙස යාවත්කාලීන කර ඇත. (7 strings, 2 integers)
        // class_name, stream, subject, teacher_name, fee, day, time (7 x s)
        // status, class_id (2 x i)
        $stmt_update->bind_param("sssssssii", $class_name, $stream, $subject, $teacher_name, $fee, $day, $time, $status, $class_id);

        if ($stmt_update->execute()) {
            header("Location: timetable.php?msg=Class '{$class_name}' updated successfully!");
            exit();
        } else {
            $error = "Error updating class: " . $stmt_update->error;
        }
        $stmt_update->close();
    } else {
         $error = "Database Prepare Error: " . $conn->error;
    }
}

// ==========================================
// 2. INITIAL FETCH/REFRESH (GET Request or after POST fail)
// ==========================================
if ($class_id > 0) {
    $stmt_select = $conn->prepare("SELECT * FROM classes WHERE class_id = ?");
    
    if ($stmt_select) {
        $stmt_select->bind_param("i", $class_id);
        $stmt_select->execute();
        $result = $stmt_select->get_result();

        if ($result->num_rows > 0) {
            $class_data = $result->fetch_assoc();
        } else {
            header("Location: timetable.php?error=Class ID not found.");
            exit();
        }
        $stmt_select->close();
    } else {
        $error = "Initial fetch prepare error: " . $conn->error;
    }
} else {
    header("Location: timetable.php?error=Invalid Class ID.");
    exit();
}

// ==========================================
// 3. DROPDOWN DATA FETCHING
// ==========================================
$teachers = [];
$t_sql = "SELECT full_name FROM teachers WHERE status=1 ORDER BY full_name ASC";
$t_res = $conn->query($t_sql);
if ($t_res) {
    while($t_row = $t_res->fetch_assoc()){
        $teachers[] = $t_row['full_name'];
    }
}

// Stream and Day arrays for dropdowns
$streams = [
    'Physical Science', 'Bio Science', 'Commerce', 'Arts', 'Technology', 'ICT (Common)'
];
$days = [
    'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Class - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <?php include('includes/sidebar.php'); ?>

    <div class="ml-64 flex flex-col min-h-screen">
        <header class="bg-white shadow-sm border-b border-gray-200 h-20 flex items-center justify-between px-8 sticky top-0 z-30">
            <h2 class="text-2xl font-bold text-gray-800">Edit Class: <?php echo htmlspecialchars($class_data['subject'] . ' - ' . $class_data['class_name']); ?></h2>
            <a href="timetable.php" class="text-gray-500 hover:text-indigo-600 font-medium text-sm flex items-center gap-2"><i class="fas fa-arrow-left"></i> Back to Time Table</a>
        </header>

        <main class="p-8 flex justify-center">
            <div class="w-full max-w-2xl bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-indigo-600 px-8 py-4">
                    <h3 class="text-white font-semibold text-lg">Update Class Details (ID: <?php echo $class_id; ?>)</h3>
                </div>
                
                <form method="POST" action="" class="p-8 space-y-6">
                    <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                    
                    <?php if(isset($error)) echo "<p class='text-red-500 bg-red-100 p-3 rounded'>".htmlspecialchars($error)."</p>"; ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Class Name</label>
                            <input type="text" name="class_name" value="<?php echo htmlspecialchars($class_data['class_name']); ?>" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stream</label>
                            <select name="stream" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200">
                                <?php foreach($streams as $stream): ?>
                                    <option value="<?php echo $stream; ?>" <?php echo ($class_data['stream'] == $stream) ? 'selected' : ''; ?>>
                                        <?php echo $stream; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <input type="text" name="subject" value="<?php echo htmlspecialchars($class_data['subject']); ?>" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teacher</label>
                            <select name="teacher_name" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200">
                                <option value="" disabled>Select Teacher</option>
                                <?php foreach($teachers as $teacher_name): ?>
                                    <option value="<?php echo htmlspecialchars($teacher_name); ?>" <?php echo ($class_data['teacher_name'] == $teacher_name) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($teacher_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Fee (LKR)</label>
                            <input type="number" name="fee" value="<?php echo htmlspecialchars($class_data['fee']); ?>" step="0.01" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Day</label>
                            <select name="day" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200">
                                <?php foreach($days as $day): ?>
                                    <option value="<?php echo $day; ?>" <?php echo ($class_data['day'] == $day) ? 'selected' : ''; ?>>
                                        <?php echo $day; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time (Text)</label>
                            <input type="text" name="time" value="<?php echo htmlspecialchars($class_data['time']); ?>" placeholder="Ex: 08:00 AM - 12:00 PM" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="status" value="1" class="w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-green-500"
                                    <?php echo ($class_data['status'] == 1) ? 'checked' : ''; ?>>
                                <span class="ml-3 text-sm font-medium text-gray-700">Set Class as Active</span>
                            </label>
                        </div>
                        
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <button type="submit" name="update_class" class="px-8 py-3 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 shadow-lg transition transform hover:-translate-y-0.5">
                            <i class="fas fa-check-circle mr-2"></i> Update Class
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>