<?php
// admin/add_timetable.php
session_start();
include 'db_con.php';

// Admin Login Check
// if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

$message = "";
$msg_type = "";

// Form Submission Handling
if (isset($_POST['submit'])) {
    $class_name = mysqli_real_escape_string($conn, $_POST['class_name']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $stream = mysqli_real_escape_string($conn, $_POST['stream']);
    $teacher_name = mysqli_real_escape_string($conn, $_POST['teacher_name']);
    $day = mysqli_real_escape_string($conn, $_POST['day']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $fee = floatval($_POST['fee']);

    // Insert Query
    $sql = "INSERT INTO classes (class_name, subject, stream, teacher_name, day, time, fee, status) 
            VALUES ('$class_name', '$subject', '$stream', '$teacher_name', '$day', '$time', '$fee', 1)";

    if ($conn->query($sql) === TRUE) {
        // Success: Redirect back to timetable list
        header("Location: timetable.php?msg=Class Added Successfully");
        exit();
    } else {
        $message = "Error: " . $conn->error;
        $msg_type = "red";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Class - Future Minds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>

<body class="bg-gray-100">

    <?php include('includes/sidebar.php'); ?>

    <div class="ml-64 flex flex-col min-h-screen">
        
        <header class="bg-white shadow-sm py-4 px-8 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Add New Class</h2>
            <a href="timetable.php" class="text-gray-600 hover:text-gray-900 font-medium flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </header>

        <main class="p-8">
            
            <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-8">
                    
                    <?php if ($message): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                            <p><?php echo $message; ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div class="col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Class Name</label>
                                <input type="text" name="class_name" placeholder="e.g. 2025 Revision - Group A" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Subject</label>
                                <input type="text" name="subject" placeholder="e.g. Combined Maths" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Stream</label>
                                <select name="stream" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                                    <option value="">-- Select Stream --</option>
                                    <option value="Physical Science">Physical Science (Maths)</option>
                                    <option value="Bio Science">Bio Science</option>
                                    <option value="Commerce">Commerce</option>
                                    <option value="Technology">Technology</option>
                                    <option value="Arts">Arts</option>
                                    <option value="ICT">ICT / General</option>
                                </select>
                            </div>

                            <div class="col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Teacher</label>
                                <select name="teacher_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                                    <option value="">-- Select Teacher --</option>
                                    <?php
                                    // Fetch teachers from database
                                    $t_sql = "SELECT full_name FROM teachers ORDER BY full_name ASC";
                                    $t_res = $conn->query($t_sql);
                                    if ($t_res->num_rows > 0) {
                                        while ($t_row = $t_res->fetch_assoc()) {
                                            echo "<option value='" . htmlspecialchars($t_row['full_name']) . "'>" . htmlspecialchars($t_row['full_name']) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Select the teacher conducting this class.</p>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Day</label>
                                <select name="day" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                    <option value="Sunday">Sunday</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Time</label>
                                <input type="text" name="time" placeholder="e.g. 08:00 AM - 10:00 AM" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div class="col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Class Fee (LKR)</label>
                                <input type="number" name="fee" step="0.01" placeholder="e.g. 2500.00" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>

                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit" name="submit" 
                                class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-indigo-700 transition duration-200 shadow-lg flex items-center gap-2">
                                <i class="fas fa-save"></i> Save Class
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>
</body>
</html>