<?php
// teacher_portal/pages/my_students.php
// Updated: Status display logic to show Active, Unenrolled, and Pending statuses

include('../include/head.php');
require_once '../../includes/connection.php';

// 1. Session & Security Check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../../log/login.php");
    exit();
}

$teacher_db_id = $_SESSION['teacher_db_id']; // Teacher ID from DB
$teacher_full_name = "";
$debug_msg = "";

// 2. ගුරුවරයාගේ නම Database එකෙන් ලබා ගැනීම
$sql_t = "SELECT full_name FROM teachers WHERE teacher_id = ?";
$stmt_t = $conn->prepare($sql_t);
$stmt_t->bind_param("i", $teacher_db_id);
$stmt_t->execute();
$res_t = $stmt_t->get_result();

if ($row_t = $res_t->fetch_assoc()) {
    $teacher_full_name = trim($row_t['full_name']); 
} else {
    $debug_msg .= "Error: Teacher ID ($teacher_db_id) not found in 'teachers' table.<br>";
}
$stmt_t->close();

$selected_class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$students = [];
$classes_list = [];
$class_details = null;

// 3. Smart Class Searching
if (!empty($teacher_full_name)) {
    
    $search_name = strtolower($teacher_full_name); 

    $sql_classes = "
        SELECT class_id, class_name, stream, teacher_name 
        FROM classes 
        WHERE 
            LOWER(teacher_name) LIKE CONCAT('%', ?, '%') 
            OR 
            ? LIKE CONCAT('%', LOWER(teacher_name), '%')
        ORDER BY class_name ASC
    ";
    
    if ($stmt_cls = $conn->prepare($sql_classes)) {
        $stmt_cls->bind_param("ss", $search_name, $search_name);
        
        if ($stmt_cls->execute()) {
            $res_cls = $stmt_cls->get_result();
            while ($row = $res_cls->fetch_assoc()) {
                $classes_list[] = $row;
            }
        }
        $stmt_cls->close();
    }
}

// Debugging
if (empty($classes_list) && !empty($teacher_full_name)) {
    $debug_msg .= "⚠️ No classes found for teacher: '<strong>$teacher_full_name</strong>'.<br>";
}

// 4. සිසුන් ලබා ගැනීම (Selected Class)
if ($selected_class_id > 0) {
    
    // A. Class Info
    $get_class_sql = "SELECT class_name, stream FROM classes WHERE class_id = ?";
    if ($stmt_c_info = $conn->prepare($get_class_sql)) {
        $stmt_c_info->bind_param("i", $selected_class_id);
        $stmt_c_info->execute();
        $res_c_info = $stmt_c_info->get_result();
        if ($res_c_info->num_rows > 0) {
            $class_details = $res_c_info->fetch_assoc();
        }
        $stmt_c_info->close();
    }

    // B. Get Enrolled Students (All statuses including Active, Pending, Unenrolled)
    $sql_students = "
        SELECT 
            s.student_id,
            s.full_name,
            s.reg_number,
            s.student_phone,
            e.joined_date,
            e.status
        FROM students s
        INNER JOIN enrollments e ON s.student_id = e.student_id
        WHERE e.class_id = ? 
        ORDER BY s.full_name ASC
    ";
    
    if ($stmt_std = $conn->prepare($sql_students)) {
        $stmt_std->bind_param("i", $selected_class_id);
        if ($stmt_std->execute()) {
            $result_std = $stmt_std->get_result();
            while ($row = $result_std->fetch_assoc()) {
                $students[] = $row;
            }
        }
        $stmt_std->close();
    }
}
?>

<?php include("../include/sidebar.php"); ?>

<div class="p-4 sm:ml-64 pb-20">
    
    <div class="flex items-center justify-between p-4 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm mt-14">
        <div>
            <h2 class="text-xl font-bold text-gray-800">My Students</h2>
            <p class="text-sm text-gray-500">Manage your class enrollments</p>
        </div>
        <div class="hidden md:block">
            <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded border border-indigo-200">
                Found Classes: <?php echo count($classes_list); ?>
            </span>
        </div>
    </div>

    <?php if (!empty($debug_msg)): ?>
    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
        <span class="font-bold"><i class="fas fa-exclamation-circle"></i> Debug Info:</span><br>
        <?php echo $debug_msg; ?>
    </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/2">
                <label for="class_id" class="block mb-2 text-sm font-medium text-gray-900">Select Class</label>
                <select id="class_id" name="class_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" required>
                    <option value="">-- Choose a Class --</option>
                    <?php foreach ($classes_list as $cls): ?>
                        <option value="<?php echo $cls['class_id']; ?>" <?php if($selected_class_id == $cls['class_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($cls['class_name']) . " (" . htmlspecialchars($cls['stream']) . ")"; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 w-full md:w-auto transition">
                Show Students
            </button>
        </form>
    </div>

    <?php if ($selected_class_id > 0 && $class_details): ?>
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">
                    <?php echo htmlspecialchars($class_details['class_name']); ?> 
                    <span class="text-sm font-normal text-gray-500">(<?php echo htmlspecialchars($class_details['stream']); ?>)</span>
                </h3>
                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                    Total Students: <?php echo count($students); ?>
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Student Name</th>
                            <th class="px-6 py-3">Reg Number</th>
                            <th class="px-6 py-3">Phone</th>
                            <th class="px-6 py-3">Joined Date</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($students) > 0): ?>
                            <?php $i = 1; foreach ($students as $std): ?>
                                <tr class="bg-white border-b hover:bg-gray-50 transition">
                                    <td class="px-6 py-4"><?php echo $i++; ?></td>
                                    <td class="px-6 py-4 font-medium text-gray-900 flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                            <?php echo strtoupper(substr($std['full_name'], 0, 1)); ?>
                                        </div>
                                        <?php echo htmlspecialchars($std['full_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs bg-gray-100 px-2 py-1 rounded w-fit">
                                        <?php echo htmlspecialchars($std['reg_number']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="tel:<?php echo htmlspecialchars($std['student_phone']); ?>" class="text-indigo-600 hover:underline">
                                            <?php echo htmlspecialchars($std['student_phone']); ?>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        <?php echo date('Y-m-d', strtotime($std['joined_date'])); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if($std['status'] == 1): ?>
                                            <span class="text-green-600 font-bold text-xs bg-green-100 px-2 py-1 rounded border border-green-200">
                                                Active
                                            </span>
                                        <?php elseif($std['status'] == 2): ?>
                                            <span class="text-red-600 font-bold text-xs bg-red-100 px-2 py-1 rounded border border-red-200">
                                                Unenrolled
                                            </span>
                                        <?php else: ?>
                                            <span class="text-yellow-600 font-bold text-xs bg-yellow-100 px-2 py-1 rounded border border-yellow-200">
                                                Pending
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-user-slash text-4xl mb-3 text-gray-300"></i>
                                        <p>No students found for this class.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif($selected_class_id == 0): ?>
        <div class="text-center py-12 bg-white rounded-lg border border-dashed border-gray-300">
            <i class="fas fa-chalkboard text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500 mt-2">Please select a class from the dropdown above to view students.</p>
        </div>
    <?php endif; ?>

</div>

<?php include("../include/footer.php"); ?>