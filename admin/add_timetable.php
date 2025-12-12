<?php 
// admin/add_timetable.php
// Updated: Teacher Selection Dropdown added for accurate linking

include 'db_con.php'; 

$error = null;
$msg = null;

if (isset($_POST['submit'])) {
    
    // Data Variables
    $class_name = $_POST['class_name'];
    $stream = $_POST['stream'];
    $subject = $_POST['subject'];
    $teacher_name = $_POST['teacher_name']; // Dropdown එකෙන් එන අගය
    $day = $_POST['day'];
    $time = $_POST['time'];
    $fee = $_POST['fee'];
    $status = 1;

    // Insert Query (Prepared Statement - Secure)
    $stmt = $conn->prepare("INSERT INTO classes (class_name, stream, subject, teacher_name, fee, day, time, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("sssssssi", $class_name, $stream, $subject, $teacher_name, $fee, $day, $time, $status);
        
        if ($stmt->execute()) {
            // Success - Redirect with message
            header("Location: timetable.php?msg=Class added successfully!");
            exit();
        } else {
            $error = "Error saving data: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Database Prepare Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Class - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <?php include('includes/sidebar.php'); ?>

    <div class="ml-64 flex flex-col min-h-screen">
        <header class="bg-white shadow-sm border-b border-gray-200 h-20 flex items-center justify-between px-8 sticky top-0 z-30">
            <h2 class="text-2xl font-bold text-gray-800">Add New Class</h2>
            <a href="timetable.php" class="text-gray-500 hover:text-indigo-600 font-medium text-sm flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </header>

        <main class="p-8 flex justify-center">
            <div class="w-full max-w-2xl bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-indigo-600 px-8 py-4">
                    <h3 class="text-white font-semibold text-lg">Class Details</h3>
                </div>
                
                <form method="POST" action="" class="p-8 space-y-6">
                    
                    <?php if($error): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                            <p><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Class Name</label>
                            <input type="text" name="class_name" placeholder="Ex: 2026 Revision" required 
                                class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stream</label>
                            <select name="stream" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="" disabled selected>Select Stream</option>
                                <option value="Physical Science">Physical Science</option>
                                <option value="Bio Science">Bio Science</option>
                                <option value="Commerce">Commerce</option>
                                <option value="Arts">Arts</option>
                                <option value="Technology">Technology</option>
                                <option value="ICT">ICT</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <input type="text" name="subject" placeholder="Ex: Physics" required 
                                class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teacher</label>
                            <div class="relative">
                                <select name="teacher_name" required 
                                    class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 appearance-none">
                                    <option value="" disabled selected>-- Select Teacher --</option>
                                    <?php
                                    // Fetch teachers from DB to avoid spelling mistakes
                                    $t_sql = "SELECT full_name FROM teachers WHERE status=1 ORDER BY full_name ASC";
                                    $t_res = $conn->query($t_sql);
                                    if ($t_res) {
                                        while($t_row = $t_res->fetch_assoc()){
                                            // Value එක සහ Display Text එක දෙකම Teacher ගේ නම විය යුතුය
                                            echo "<option value='".htmlspecialchars($t_row['full_name'])."'>".htmlspecialchars($t_row['full_name'])."</option>";
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-700">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Select the teacher from the list to link this class to their dashboard.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Fee (LKR)</label>
                            <input type="number" name="fee" placeholder="2500.00" required 
                                class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Day</label>
                            <select name="day" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="" disabled selected>Select Day</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                                <option value="Sunday">Sunday</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time Slot</label>
                            <input type="text" name="time" placeholder="Ex: 08:00 AM - 12:00 PM" required 
                                class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="flex justify-end pt-6 border-t border-gray-100">
                        <button type="submit" name="submit" class="px-8 py-3 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Save Class
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>