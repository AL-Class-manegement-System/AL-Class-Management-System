<?php
// teacher_portal/pages/my_classes.php
// Updated: Correctly linked with Admin's added classes using Teacher Name

// Include Header & Connection
include('../include/head.php');
require_once '../../includes/connection.php';

// --- Security Check ---
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../../log/login.php");
    exit();
}

// Get Logged-in Teacher's ID
$teacher_db_id = $_SESSION['teacher_db_id']; 

// Fetch Classes Logic
// We join 'classes' table with 'teachers' table to match the Logged-in ID with the Teacher Name stored in Classes
$sql = "
    SELECT 
        c.class_id,
        c.class_name,
        c.stream,
        c.subject,
        c.day,
        c.time,
        c.fee
    FROM
        classes c
    INNER JOIN
        teachers t ON c.teacher_name = t.full_name
    WHERE
        t.teacher_id = ?
    ORDER BY
        FIELD(c.day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), c.time
";

$stmt = $conn->prepare($sql);
$result = null;
$error_message = null;

if ($stmt) {
    $stmt->bind_param("i", $teacher_db_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
    } else {
        $error_message = "Error executing query: " . $conn->error;
    }
} else {
    $error_message = "Database error: " . $conn->error;
}
?>

<?php include("../include/sidebar.php"); ?>

<div class="p-4 sm:ml-64 pb-20">
    
    <div class="flex items-center justify-between p-4 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-gray-800">My Classes</h2>
            <p class="text-sm text-gray-500">Manage your assigned classes and schedules</p>
        </div>
        <div class="hidden md:block">
            <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded border border-indigo-400">
                <i class="ph ph-chalkboard-teacher"></i> Active Schedules
            </span>
        </div>
    </div>

    <?php if ($error_message): ?>
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
            <span class="font-medium">Error!</span> <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                        <tr>
                            <th scope="col" class="px-6 py-3">Class Info</th>
                            <th scope="col" class="px-6 py-3">Stream / Subject</th>
                            <th scope="col" class="px-6 py-3">Schedule</th>
                            <th scope="col" class="px-6 py-3">Fee</th>
                            <th scope="col" class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="bg-white border-b hover:bg-gray-50 transition">
                                
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    <div class="text-base font-semibold"><?php echo htmlspecialchars($row['class_name']); ?></div>
                                    <div class="text-xs text-gray-500">ID: #<?php echo $row['class_id']; ?></div>
                                </th>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded w-fit mb-1">
                                            <?php echo htmlspecialchars($row['stream']); ?>
                                        </span>
                                        <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($row['subject']); ?></span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <i class="ph ph-calendar-blank text-lg text-indigo-500"></i>
                                        <div>
                                            <div class="text-gray-900 font-medium"><?php echo htmlspecialchars($row['day']); ?></div>
                                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($row['time']); ?></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-medium text-gray-900">
                                    LKR <?php echo number_format($row['fee'], 2); ?>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="my_students.php?class_id=<?php echo $row['class_id']; ?>" 
                                           class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-xs px-3 py-2 focus:outline-none transition">
                                            <i class="ph ph-users"></i> Students
                                        </a>
                                        <a href="assignments.php?class_id=<?php echo $row['class_id']; ?>" 
                                           class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-xs px-3 py-2 transition">
                                            <i class="ph ph-clipboard-text"></i> Tasks
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
                <div class="bg-gray-100 rounded-full p-4 mb-4">
                    <i class="ph ph-chalkboard text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">No Classes Assigned Yet</h3>
                <p class="text-gray-500 text-sm max-w-sm">
                    You haven't been assigned to any classes. Please contact the administrator to add your classes to the timetable.
                </p>
            </div>
        <?php endif; ?>
        
        <?php if ($stmt) $stmt->close(); ?>
    </div>
</div>

<?php include("../include/footer.php"); ?>