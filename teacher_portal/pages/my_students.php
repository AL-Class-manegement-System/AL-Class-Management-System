<?php
// Path variable for correct asset linking
$path = "../";

// Include the header
include $path . "include/head.php"; // Handles session_start()

// Include the database connection file
include "../../includes/connection.php"; //

// --- Security Check: Ensure teacher is logged in ---
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../../log/login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$students = [];
$error_message = null;

// The first version of this file had a simpler query. 
// We will use the secure, filtered logic from your latest version:
$page_title = "All Enrolled Students";

// Check if a specific Class ID is passed (e.g., from 'My Classes' page)
// Use filter_input for cleaner check
$class_id_filter = filter_input(INPUT_GET, 'class_id', FILTER_VALIDATE_INT);
$filter_by_class = $class_id_filter !== false && $class_id_filter > 0;

// --- Secure Database Query (using Prepared Statements) ---
$sql = "
    SELECT DISTINCT
        s.reg_number,
        s.full_name,
        s.student_phone,
        c.class_name
    FROM
        students s
    INNER JOIN
        enrollments e ON s.student_id = e.student_id
    INNER JOIN
        classes c ON e.class_id = c.class_id
    INNER JOIN
        teachers t ON c.teacher_name = t.full_name
    WHERE
        t.teacher_id = ?
";

$param_types = "i";
$param_values = [$teacher_id];

if ($filter_by_class) {
    $sql .= " AND c.class_id = ?";
    $param_types .= "i";
    $param_values[] = $class_id_filter;
    
    // Fetch the class name for the title
    $class_name_stmt = mysqli_prepare($conn, "SELECT class_name FROM classes WHERE class_id = ?");
    mysqli_stmt_bind_param($class_name_stmt, "i", $class_id_filter);
    mysqli_stmt_execute($class_name_stmt);
    $class_result = mysqli_stmt_get_result($class_name_stmt);
    if ($class_row = mysqli_fetch_assoc($class_result)) {
        $page_title = "Students in " . htmlspecialchars($class_row['class_name']);
    }
    mysqli_stmt_close($class_name_stmt);
}

$sql .= " ORDER BY c.class_name, s.reg_number";

// Prepare the main statement
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    // Bind parameters dynamically
    mysqli_stmt_bind_param($stmt, $param_types, ...$param_values);
    
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        $result = mysqli_stmt_get_result($stmt);
        $students = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error_message = "Error executing statement: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
} else {
    $error_message = "Error preparing statement: " . mysqli_error($conn);
}
?>

<div class="flex">
    
    <?php include $path . "include/sidebar.php"; ?>

    <main id="main-content" class="p-4 sm:ml-64 pt-20 w-full min-h-screen">

        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-md">
            
            <h1 class="text-2xl font-bold text-gray-900 mb-6"><?php echo $page_title; ?></h1>
            
            <?php if (isset($error_message)): ?>
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                    <span class="font-medium">Database Error!</span> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($students)): ?>
                <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50" role="alert">
                    <span class="font-medium">No Students Found!</span> There are no students currently enrolled in your <?php echo $filter_by_class ? "selected class" : "classes"; ?>.
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Registration No.</th>
                                <th scope="col" class="px-6 py-3">Student Name</th>
                                <th scope="col" class="px-6 py-3">Student Phone No.</th>
                                <th scope="col" class="px-6 py-3">Class Enrolled</th>
                                <th scope="col" class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        <?php echo htmlspecialchars($student['reg_number']); ?>
                                    </th>
                                    <td class="px-6 py-4">
                                        <?php echo htmlspecialchars($student['full_name']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php echo htmlspecialchars($student['student_phone']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php echo htmlspecialchars($student['class_name']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="#" class="font-medium text-blue-600 hover:underline mr-2">View Profile</a>
                                        <a href="#" class="font-medium text-indigo-600 hover:underline">Mark Attendance</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

        </div>
    </main>
</div>

<?php include $path . "include/footer.php"; ?>