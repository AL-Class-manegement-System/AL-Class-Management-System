<?php
// student_header.php හරහා DB connection, Session සහ $student array එක ලබා ගනී.
include('../includes/student_header.php');

// ==========================================
// 1. PHP Helper Functions (Dynamic Time/Status)
// ==========================================

// වේලාව නිවැරදිව ගණනය කිරීම සඳහා වේලා කලාපය සකසන්න (ශ්‍රී ලංකාව සඳහා)
date_default_timezone_set('Asia/Colombo');

/**
 * පන්තියේ වත්මන් තත්ත්වය තීරණය කරයි.
 * @param string $class_day පන්තිය පැවැත්වෙන දිනය (e.g., 'Monday')
 * @param string $class_time පන්තිය ආරම්භ වන වේලාව (e.g., '08:00 AM')
 * @return array 
 */
function getClassStatus($class_day, $class_time) {
    // පන්තියේ සාමාන්‍ය කාලය (මිනිත්තු)
    $duration_minutes = 90; 
    
    $current_day = date('l');
    $current_timestamp = time();

    // පන්තිය අද දිනදැයි පරීක්ෂා කිරීම
    if ($class_day === $current_day) {
        
        // පන්තියේ ආරම්භක සහ අවසන් වන වේලාවන්හි timestamp
        // strtotime() මගින් අද දිනට අදාළ timestamp එක ගණනය කරයි.
        $start_timestamp = strtotime($class_time);
        $end_timestamp = $start_timestamp + ($duration_minutes * 60); 

        if ($current_timestamp >= $start_timestamp && $current_timestamp < $end_timestamp) {
            // දැනට පන්තිය පැවැත්වේ
            return ['status' => 'Ongoing', 'color' => 'bg-green-500', 'text' => 'text-white', 'icon' => 'fas fa-dot-circle animate-pulse'];
        } elseif ($current_timestamp < $start_timestamp && $start_timestamp - $current_timestamp <= (30 * 60)) {
            // ඉදිරි මිනිත්තු 30 තුළ ආරම්භ වේ (30 minutes starting soon window)
            return ['status' => 'Starting Soon', 'color' => 'bg-orange-500', 'text' => 'text-white', 'icon' => 'fas fa-stopwatch'];
        } elseif ($current_timestamp < $start_timestamp) {
            // ඉදිරියේදී පැවැත්වීමට නියමිතයි
            return ['status' => 'Scheduled Today', 'color' => 'bg-indigo-500', 'text' => 'text-white', 'icon' => 'far fa-clock'];
        } else {
            // අද දින පන්තිය අවසන්
            return ['status' => 'Finished Today', 'color' => 'bg-slate-300', 'text' => 'text-slate-700', 'icon' => 'fas fa-check'];
        }

    } else {
        // අද දින නොවේ
        return ['status' => 'Scheduled', 'color' => 'bg-slate-500', 'text' => 'text-white', 'icon' => 'far fa-calendar-alt'];
    }
}

/**
 * වත්මන් දිනයට අදාළව Container එක Highlight කිරීම සඳහා CSS Class ලබා දෙයි.
 */
function getDayHighlightClass($class_day) {
    $current_day = date('l');
    if ($class_day === $current_day) {
        // වත්මන් දිනය
        return 'bg-indigo-50/70 border-2 border-indigo-400 shadow-xl';
    }
    // වෙනත් දින
    return 'bg-white border border-slate-100 shadow-sm';
}

// ==========================================
// 2. Database Query Logic (Prepared Statement)
// ==========================================

$db_student_id = $student['student_id'];
$time_table_data = [];

// සතියේ දින නිවැරදි පිළිවෙලට පෙන්වීම සඳහා දින අනුපිළිවෙල
$day_order_list = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

// Enroll වී ඇති පන්ති වල දත්ත classes වගුවෙන් ලබා ගැනීම.
// FIELD() භාවිතයෙන් දින නිවැරදි පිළිවෙලට වර්ග කරයි.
$sql_timetable = "
    SELECT 
        c.subject, 
        c.class_name, 
        c.day, 
        c.time, 
        c.hall_number,
        c.teacher_name,
        c.stream
    FROM enrollments e
    JOIN classes c ON e.class_id = c.class_id
    WHERE e.student_id = ?
    ORDER BY FIELD(c.day, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), c.time ASC
";

$stmt = $conn->prepare($sql_timetable);

// ==========================================
// ERROR FIX: Check if prepare was successful
// ==========================================
if ($stmt) { 
    $stmt->bind_param("i", $db_student_id); // Prepared Statement භාවිතය
    
    if ($stmt->execute()) { // Check if execution was successful
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // දත්ත සතියේ දින අනුව කාණ්ඩ කිරීම
                $time_table_data[$row['day']][] = $row;
            }
        }
    } else {
        // Query execution failed (optional error logging)
        // error_log("Time Table Query Execution Failed: " . $stmt->error);
    }
    $stmt->close();
} else {
    // Query preparation failed
    // error_log("Time Table Query Preparation Failed: " . $conn->error);
}
?>

<div class="flex-1 flex flex-col h-screen overflow-y-auto bg-gray-50">
    <main class="p-4 md:p-8">
        
        <h1 class="text-3xl font-bold text-slate-800 mb-8 flex items-center gap-3">
            Your Weekly Time Table 🗓️
        </h1>

        <?php if (empty($time_table_data)): ?>
            <div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-3xl border border-dashed border-gray-200">
                <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mb-4 text-red-600 text-3xl">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-500">No Enrolled Classes Found</h3>
                <p class="text-sm text-gray-400 mt-1">Please visit the 'My Classes' page to enroll in a class first.</p>
            </div>
        <?php else: ?>
        
            <div class="space-y-6">
            
                <?php 
                // දින නිවැරදි පිළිවෙලට (Sunday සිට Saturday දක්වා) Loop කිරීම
                foreach ($day_order_list as $day): 
                    if (isset($time_table_data[$day])):
                        
                        // Current Day Highlighting Class එක ලබා ගැනීම
                        $highlight_class = getDayHighlightClass($day);
                ?>
                
                <div class="rounded-2xl overflow-hidden <?php echo $highlight_class; ?> transition-all duration-300">
                    <div class="p-4 md:p-6 bg-indigo-600/10 border-l-4 border-indigo-600 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-slate-800 flex items-center gap-3">
                            <i class="fas fa-calendar-day text-indigo-600"></i> <?php echo htmlspecialchars($day); ?>
                            <span class="text-sm font-medium bg-indigo-100 text-indigo-600 px-3 py-1 rounded-full">
                                <?php echo count($time_table_data[$day]); ?> Classes
                            </span>
                        </h2>
                    </div>
                    
                    <div class="divide-y divide-slate-100">
                        <?php foreach ($time_table_data[$day] as $class): 
                            $status_info = getClassStatus($class['day'], $class['time']);
                        ?>
                            
                            <div class="p-4 md:p-6 flex items-center hover:bg-slate-50 transition-colors">
                                
                                <div class="w-20 md:w-28 flex-shrink-0 text-center relative">
                                    <p class="text-2xl font-extrabold text-slate-800 leading-none">
                                        <?php echo htmlspecialchars($class['time']); ?>
                                    </p>
                                    <span class="text-xs font-bold mt-1 inline-flex items-center gap-1 rounded-full px-2 py-0.5 <?php echo $status_info['color']; ?> <?php echo $status_info['text']; ?>">
                                        <i class="<?php echo $status_info['icon']; ?> text-[8px]"></i>
                                        <?php echo $status_info['status']; ?>
                                    </span>
                                </div>
                                
                                <div class="flex-1 ml-6 border-l pl-6 border-slate-200">
                                    <h3 class="font-bold text-lg text-slate-800 line-clamp-1">
                                        <?php echo htmlspecialchars($class['subject']); ?>
                                        <span class="text-xs font-normal text-slate-500 ml-2">(<?php echo htmlspecialchars($class['class_name']); ?>)</span>
                                    </h3>
                                    
                                    <div class="flex flex-wrap items-center text-sm text-slate-500 mt-1 gap-x-4 gap-y-1">
                                        <p class="flex items-center gap-1 font-medium">
                                            <i class="fas fa-chalkboard-teacher text-indigo-400"></i>
                                            <?php echo htmlspecialchars($class['teacher_name']); ?>
                                        </p>
                                        <p class="flex items-center gap-1">
                                            <i class="fas fa-map-marker-alt text-indigo-400"></i>
                                            <?php echo htmlspecialchars($class['hall_number'] ?? 'Online'); ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <?php if ($status_info['status'] === 'Ongoing' || $status_info['status'] === 'Starting Soon'): ?>
                                    <a href="live_class.php" 
                                        class="ml-4 flex-shrink-0 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors shadow-md shadow-green-500/30">
                                        <i class="fas fa-video mr-1"></i> Join Now
                                    </a>
                                <?php else: ?>
                                    <button disabled
                                        class="ml-4 flex-shrink-0 px-4 py-2 bg-gray-200 text-gray-500 text-sm font-semibold rounded-lg cursor-not-allowed">
                                        Inactive
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <?php 
                    endif; 
                endforeach; 
                ?>
                
            </div>
        <?php endif; ?>

    </main>
</div>

<?php include('../includes/footer.php'); ?>