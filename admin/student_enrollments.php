<?php
// admin/student_enrollments.php
include 'db_con.php'; 

// සිසුවාගේ ID එක ලැබී ඇත්දැයි බැලීම
if(!isset($_GET['student_id'])) {
    header("Location: student.php");
    exit();
}

$student_id = intval($_GET['student_id']);

// 1. සිසුවාගේ මූලික විස්තර ලබා ගැනීම
$std_sql = "SELECT full_name, reg_number FROM students WHERE student_id = ?";
$stmt = $conn->prepare($std_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student_res = $stmt->get_result();

if($student_res->num_rows == 0) {
    echo "Student not found.";
    exit();
}
$student = $student_res->fetch_assoc();
$stmt->close();

// 2. සිසුවාගේ පන්ති විස්තර (Enrollments) ලබා ගැනීම
// Active (1), Pending (0), Unenrolled (2) සියල්ල පෙන්වයි.
$sql = "SELECT 
            e.enrollment_id,
            e.joined_date,
            e.status,
            c.class_name,
            c.teacher_name,
            c.stream,
            c.subject
        FROM enrollments e
        JOIN classes c ON e.class_id = c.class_id
        WHERE e.student_id = ?
        ORDER BY e.joined_date DESC";

$stmt_en = $conn->prepare($sql);
$stmt_en->bind_param("i", $student_id);
$stmt_en->execute();
$enrollments = $stmt_en->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment History - <?php echo htmlspecialchars($student['full_name']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <?php include('includes/sidebar.php'); ?>

    <div class="ml-64 flex flex-col min-h-screen">
        <header class="bg-white shadow-sm py-4 px-8 flex justify-between items-center sticky top-0 z-40">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Enrollment History</h2>
                <p class="text-sm text-gray-500">Student: <span class="font-semibold text-indigo-600"><?php echo htmlspecialchars($student['full_name']); ?></span> (<?php echo htmlspecialchars($student['reg_number']); ?>)</p>
            </div>
            <a href="student.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Students
            </a>
        </header>

        <main class="p-8">
            
            <?php if(isset($_GET['msg'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                <p class="font-bold">Success</p>
                <p class="text-sm"><?php echo htmlspecialchars($_GET['msg']); ?></p>
            </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Class Name</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Teacher</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Stream/Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Joined Date</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php 
                            if ($enrollments->num_rows > 0) {
                                while($row = $enrollments->fetch_assoc()) {
                                    $status_label = "Unknown";
                                    $status_class = "bg-gray-100 text-gray-600";
                                    
                                    if($row['status'] == 1) {
                                        $status_label = "Active";
                                        $status_class = "bg-green-100 text-green-800 border border-green-200";
                                    } elseif($row['status'] == 0) {
                                        $status_label = "Pending";
                                        $status_class = "bg-yellow-100 text-yellow-800 border border-yellow-200";
                                    } elseif($row['status'] == 2) {
                                        $status_label = "Unenrolled";
                                        $status_class = "bg-red-100 text-red-800 border border-red-200";
                                    }
                            ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                    <?php echo htmlspecialchars($row['class_name']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    <?php echo htmlspecialchars($row['teacher_name']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm">
                                    <?php echo htmlspecialchars($row['stream']) . " - " . htmlspecialchars($row['subject']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm">
                                    <?php echo date('Y-m-d h:i A', strtotime($row['joined_date'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 inline-flex text-xs font-bold rounded-full <?php echo $status_class; ?>">
                                        <?php echo $status_label; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <?php if($row['status'] == 1): ?>
                                        <a href="process_unenroll.php?enroll_id=<?php echo $row['enrollment_id']; ?>&sid=<?php echo $student_id; ?>" 
                                           onclick="return confirm('Are you sure you want to unenroll this student? They will lose access to materials, but history will be kept.')"
                                           class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition border border-red-200">
                                           <i class="fas fa-user-minus mr-1"></i> Unenroll
                                        </a>
                                    <?php elseif($row['status'] == 2): ?>
                                        <span class="text-gray-400 text-xs italic">Ended</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center py-8 text-gray-500'>No enrollments found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>