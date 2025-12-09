<?php 
// admin/mark_attendance.php - All Prepared Statements are used for security.
include 'db_con.php'; 

$notifications = [];
$msg = null;

// ==========================================
// 1. DATA SAVE LOGIC (Secure)
// ==========================================
if (isset($_POST['save_attendance'])) {
    // 1. Get Input Data safely
    $date = $_POST['date']; // We trust the hidden/date input for now, but will bind it.
    $attendance_data = isset($_POST['status']) ? $_POST['status'] : []; 
    $send_whatsapp = isset($_POST['send_whatsapp']); 
    $send_sms = isset($_POST['send_sms']); 

    // Prepare statements outside the loop for efficiency
    $check_stmt = $conn->prepare("SELECT attendance_id FROM attendance WHERE student_id = ? AND date = ?");
    $update_stmt = $conn->prepare("UPDATE attendance SET status = ?, time = ? WHERE student_id = ? AND date = ?");
    $insert_stmt = $conn->prepare("INSERT INTO attendance (student_id, date, status, time) VALUES (?, ?, ?, ?)");
    $stu_stmt = $conn->prepare("SELECT full_name, parent_phone FROM students WHERE student_id = ?");

    if (!$check_stmt || !$update_stmt || !$insert_stmt || !$stu_stmt) {
        $msg = "Database Prepare Error: One or more statements failed to prepare.";
        $msg_type = "error";
    } else {

        foreach ($attendance_data as $student_id => $status) {
            $sid = intval($student_id);
            $stat = $status;
            $current_time = date("H:i:s");

            // 2. Database Update / Insert (Using Prepared Statements)
            
            // Check if attendance record exists
            $check_stmt->bind_param("is", $sid, $date);
            $check_stmt->execute();
            $check_res = $check_stmt->get_result();
            
            if ($check_res->num_rows > 0) {
                // Update
                $update_stmt->bind_param("ssis", $stat, $current_time, $sid, $date);
                $update_stmt->execute();
            } else {
                // Insert
                $insert_stmt->bind_param("isss", $sid, $date, $stat, $current_time);
                $insert_stmt->execute();
            }

            // 3. Notification Links Creation
            if (($send_whatsapp || $send_sms) && !empty($stat)) {
                
                // Fetch student details
                $stu_stmt->bind_param("i", $sid);
                $stu_stmt->execute();
                $stu_res = $stu_stmt->get_result();
                
                if ($stu_res && $stu_res->num_rows > 0) {
                    $stu_data = $stu_res->fetch_assoc();
                    $parent_phone = $stu_data['parent_phone'];
                    $student_name = $stu_data['full_name'];

                    $icon = ($stat == 'Present') ? "✅" : "❌";
                    $message_body = "Future Minds: $student_name is marked $stat $icon for the class on $date.";

                    $clean_phone = preg_replace('/[^0-9]/', '', $parent_phone);
                    if (substr($clean_phone, 0, 2) == '94') {
                        $wa_phone = $clean_phone;
                    } elseif (substr($clean_phone, 0, 1) == '0') {
                        $wa_phone = '94' . substr($clean_phone, 1);
                    } else {
                        $wa_phone = '94' . $clean_phone;
                    }

                    $wa_link = $send_whatsapp ? "https://api.whatsapp.com/send?phone=$wa_phone&text=" . urlencode($message_body) : "";
                    $sms_link = $send_sms ? "sms:$wa_phone?&body=" . urlencode($message_body) : "";
                    
                    $notifications[] = [
                        'name' => $student_name,
                        'status' => $stat,
                        'phone' => $parent_phone,
                        'wa_link' => $wa_link,
                        'sms_link' => $sms_link
                    ];
                }
            }
        }
        
        // Close statements after loop
        $check_stmt->close();
        $update_stmt->close();
        $insert_stmt->close();
        $stu_stmt->close();
        
        if (!isset($msg)) { // If no prepare error occurred
            $msg = "Attendance marked successfully!";
            $msg_type = "success";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mark Attendance - Future Minds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <?php include('includes/sidebar.php'); ?>

    <div class="ml-64 flex flex-col min-h-screen">
        
        <header class="bg-white shadow-sm py-4 px-8 flex justify-between items-center sticky top-0 z-40">
            <h2 class="text-2xl font-bold text-gray-800">Mark Attendance</h2>
        </header>

        <main class="p-8">
            
            <?php if(isset($msg)): ?>
            <div class="<?php echo ($msg_type == 'success') ? 'bg-green-100 border-l-4 border-green-500 text-green-700' : 'bg-red-100 border-l-4 border-red-500 text-red-700'; ?> p-4 mb-6 rounded shadow-sm">
                <p class="font-bold"><?php echo ($msg_type == 'success') ? 'Success' : 'Error'; ?></p>
                <p class="text-sm"><?php echo $msg; ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($notifications)): ?>
            <div class="bg-white p-6 rounded-xl shadow-lg border border-indigo-200 mb-8">
                <h3 class="font-bold text-lg text-gray-800 mb-2 flex items-center gap-2">
                    <i class="fas fa-paper-plane text-indigo-600"></i> Send Notifications
                </h3>
                <p class="text-sm text-gray-500 mb-4">Click below buttons to send messages to parents.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($notifications as $note): ?>
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50 flex flex-col gap-2">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-sm text-gray-700 truncate"><?php echo htmlspecialchars($note['name']); ?></span>
                            <span class="text-xs font-bold px-2 py-1 rounded <?php echo ($note['status'] == 'Present') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                                <?php echo $note['status']; ?>
                            </span>
                        </div>
                        <div class="text-xs text-gray-400 mb-1"><i class="fas fa-phone mr-1"></i><?php echo $note['phone']; ?></div>
                        
                        <div class="flex gap-2 mt-auto">
                            <?php if(!empty($note['wa_link'])): ?>
                            <a href="<?php echo $note['wa_link']; ?>" target="_blank" class="flex-1 bg-[#25D366] hover:bg-[#128C7E] text-white text-xs font-bold py-2 px-2 rounded text-center transition flex items-center justify-center gap-1">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                            <?php endif; ?>
                            
                            <?php if(!empty($note['sms_link'])): ?>
                            <a href="<?php echo $note['sms_link']; ?>" target="_blank" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold py-2 px-2 rounded text-center transition flex items-center justify-center gap-1">
                                <i class="fas fa-comment-dots"></i> SMS
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
                <form method="GET" action="" class="flex flex-wrap gap-4 items-end">
                    
                    <div class="w-full md:w-64">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Class</label>
                        <select name="class_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Select Class --</option>
                            <?php
                            $class_sql = "SELECT * FROM classes WHERE status = 1";
                            $class_res = $conn->query($class_sql);
                            while($c_row = $class_res->fetch_assoc()){
                                $selected = (isset($_GET['class_id']) && $_GET['class_id'] == $c_row['class_id']) ? 'selected' : '';
                                echo "<option value='{$c_row['class_id']}' $selected>".htmlspecialchars($c_row['class_name'])." (".htmlspecialchars($c_row['stream']).")</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="w-full md:w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" name="date" value="<?php echo isset($_GET['date']) ? htmlspecialchars($_GET['date']) : date('Y-m-d'); ?>" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                        Load Students
                    </button>
                </form>
            </div>

            <?php if(isset($_GET['class_id']) && !empty($_GET['class_id'])): ?>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <form method="POST" action="">
                    <input type="hidden" name="date" value="<?php echo htmlspecialchars($_GET['date']); ?>">
                    
                    <div class="p-4 bg-gray-50 border-b border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <h3 class="font-bold text-gray-700">Student List</h3>
                            <span class="text-sm text-gray-500">Date: <?php echo htmlspecialchars($_GET['date']); ?></span>
                        </div>

                        <div class="flex items-center gap-4 bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
                            <span class="text-sm font-semibold text-gray-600">Send Alerts via:</span>
                            
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="send_whatsapp" value="1" class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                                <span class="ml-2 text-sm text-gray-700"><i class="fab fa-whatsapp text-green-500 text-lg align-middle"></i> WhatsApp</span>
                            </label>

                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="send_sms" value="1" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700"><i class="fas fa-comment-dots text-blue-500 text-lg align-middle"></i> SMS</span>
                            </label>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Reg No</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Parent Phone</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $class_id = $conn->real_escape_string($_GET['class_id']);
                                $search_date = $conn->real_escape_string($_GET['date']);

                                // 1. Fetch Class Data (Prepared Statement)
                                $cls_stmt = $conn->prepare("SELECT stream, class_name FROM classes WHERE class_id = ?");
                                
                                if($cls_stmt) {
                                    $cls_stmt->bind_param("i", $class_id);
                                    $cls_stmt->execute();
                                    $cls_res = $cls_stmt->get_result();
                                    
                                    if($cls_res && $cls_res->num_rows > 0) {
                                        $cls_data = $cls_res->fetch_assoc();
                                        $class_stream_full = $cls_data['stream'];
                                        $cls_stmt->close(); 

                                        // 2. Stream Mapping (Converts 'Physical Science' to 'Maths' for student table)
                                        $stream_map = [
                                            'Physical Science' => 'Maths',
                                            'Bio Science' => 'Bio',
                                            'Technology' => 'Tech',
                                            'Arts' => 'Art',
                                            'Commerce' => 'Commerce',
                                            'ICT (Common)' => 'ICT' // Assuming ICT students are tagged as 'ICT'
                                        ];

                                        $student_stream_name = isset($stream_map[$class_stream_full]) ? $stream_map[$class_stream_full] : $class_stream_full;
                                        
                                        // 3. Select Students (Prepared Statement)
                                        $st_stmt = $conn->prepare("SELECT student_id, reg_number, full_name, parent_phone FROM students WHERE stream = ? AND status = 1 ORDER BY reg_number ASC");
                                        
                                        if($st_stmt) {
                                            $st_stmt->bind_param("s", $student_stream_name);
                                            $st_stmt->execute();
                                            $st_res = $st_stmt->get_result();
                                            
                                            if ($st_res->num_rows > 0) {
                                                while($student = $st_res->fetch_assoc()) {
                                                    $sid = $student['student_id'];
                                                    
                                                    // Check previous Attendance (Prepared Statement)
                                                    $att_stmt = $conn->prepare("SELECT status FROM attendance WHERE student_id = ? AND date = ?");
                                                    if ($att_stmt) {
                                                        $att_stmt->bind_param("is", $sid, $search_date);
                                                        $att_stmt->execute();
                                                        $att_res = $att_stmt->get_result();
                                                        $current_status = ($att_res->num_rows > 0) ? $att_res->fetch_assoc()['status'] : '';
                                                        $att_stmt->close(); 
                                                    } else {
                                                        $current_status = ''; // Default if prepare fails
                                                    }
                                ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($student['reg_number']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo htmlspecialchars($student['full_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        <?php echo htmlspecialchars($student['parent_phone']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="inline-flex rounded-md shadow-sm" role="group">
                                            <label class="cursor-pointer">
                                                <input type="radio" name="status[<?php echo $sid; ?>]" value="Present" class="peer sr-only" <?php echo ($current_status == 'Present') ? 'checked' : ''; ?>>
                                                <span class="px-4 py-2 text-sm font-medium bg-white border border-gray-200 rounded-l-lg hover:bg-green-50 peer-checked:bg-green-600 peer-checked:text-white transition">Present</span>
                                            </label>
                                            <label class="cursor-pointer">
                                                <input type="radio" name="status[<?php echo $sid; ?>]" value="Absent" class="peer sr-only" <?php echo ($current_status == 'Absent') ? 'checked' : ''; ?>>
                                                <span class="px-4 py-2 text-sm font-medium bg-white border border-gray-200 rounded-r-lg hover:bg-red-50 peer-checked:bg-red-600 peer-checked:text-white transition">Absent</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                                }
                                                $st_stmt->close(); 
                                            } else {
                                                echo "<tr><td colspan='4' class='text-center py-6 text-gray-500 bg-gray-50'>
                                                    No students found for stream: <b>".htmlspecialchars($student_stream_name)."</b>.<br>
                                                    <span class='text-xs'>Please check if students are registered under this stream.</span>
                                                </td></tr>";
                                            }
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                        <button type="submit" name="save_attendance" class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-indigo-700 shadow-lg transition transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Save & Generate Alerts
                        </button>
                    </div>
                </form>
            </div>
            
            <?php endif; ?>

        </main>
    </div>
</body>
</html>