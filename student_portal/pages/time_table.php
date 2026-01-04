<?php
// Include the Student Header (Handles Session, DB Connection, and Student Data)
include('../includes/student_header.php');

// Explicit Database Connection Check
if (!isset($conn) || !($conn instanceof mysqli)) {
    die("System Error: Database connection is missing. Please try reloading or contact support.");
}

// Set Timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

/**
 * ==========================================
 * Helper Function: Get Class Status
 * ==========================================
 * Determines if a class is Active, Upcoming, or Finished based on Day and Time.
 */
function getClassStatus($class_day, $class_time)
{
    // Configuration: Class duration in minutes
    $duration_minutes = 90; 

    $current_day = date('l'); 
    $current_timestamp = time();

    // If the class is NOT today
    if (strtolower($class_day) !== strtolower($current_day)) {
        return [
            'status' => 'Scheduled',
            'color' => 'bg-slate-100 text-slate-500 border border-slate-200',
            'icon' => 'far fa-calendar-alt',
            'action' => 'disabled'
        ];
    }

    // Parse Class Time
    $start_timestamp = strtotime($class_time); // Assumes time is today if no date provided
    
    if ($start_timestamp === false) {
        return [
            'status' => 'Time Error',
            'color' => 'bg-red-100 text-red-600',
            'icon' => 'fas fa-exclamation-triangle',
            'action' => 'disabled'
        ];
    }

    $end_timestamp = $start_timestamp + ($duration_minutes * 60);
    $starting_soon_window = 30 * 60; // 30 minutes before

    if ($current_timestamp >= $start_timestamp && $current_timestamp < $end_timestamp) {
        // ONGOING
        return [
            'status' => 'Live Now',
            'color' => 'bg-green-100 text-green-700 border border-green-200 animate-pulse',
            'icon' => 'fas fa-video',
            'action' => 'active'
        ];
    } elseif ($current_timestamp < $start_timestamp && ($start_timestamp - $current_timestamp) <= $starting_soon_window) {
        // STARTING SOON
        return [
            'status' => 'Starting Soon',
            'color' => 'bg-orange-100 text-orange-700 border border-orange-200',
            'icon' => 'fas fa-stopwatch',
            'action' => 'active' // Allow joining early? or maybe wait. Let's say active for checking.
        ];
    } elseif ($current_timestamp < $start_timestamp) {
        // FUTURE TODAY
        return [
            'status' => 'Today',
            'color' => 'bg-indigo-100 text-indigo-700 border border-indigo-200',
            'icon' => 'far fa-clock',
            'action' => 'disabled'
        ];
    } else {
        // FINISHED TODAY
        return [
            'status' => 'Finished',
            'color' => 'bg-gray-100 text-gray-500 border border-gray-200',
            'icon' => 'fas fa-check-circle',
            'action' => 'disabled'
        ];
    }
}

// ==========================================
// Data Fetching Logic
// ==========================================

$db_student_id = $student['student_id'];
$timetable_grouped = [];
$days_order = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

// The SQL Query
// Uses FIELD() to strictly order days
$sql = "
    SELECT 
        c.class_name, 
        c.subject, 
        c.teacher_name, 
        c.day, 
        c.time, 
        c.hall_number
    FROM enrollments e
    JOIN classes c ON e.class_id = c.class_id
    WHERE e.student_id = ?
    ORDER BY FIELD(c.day, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), c.time ASC
";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $db_student_id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            // Group by Day
            $timetable_grouped[$row['day']][] = $row;
        }
    }
    $stmt->close();
} else {
    // If prepare fails, handling it silently on UI or showing error
    echo '<div class="p-4 bg-red-100 text-red-700">Error loading timetable data. Please contact admin.</div>';
}

?>

<!-- Main Content Area -->
<div class="flex-1 flex flex-col h-screen overflow-y-auto w-full"> 
    <!-- Added w-full to ensure full width usage -->
    
    <main class="flex-grow p-4 md:p-8 max-w-6xl mx-auto w-full">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Weekly Time Table</h1>
                <p class="text-slate-500 mt-1">Manage your class schedule efficiently.</p>
            </div>
            
            <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-slate-200 text-sm font-medium text-slate-600 flex items-center gap-2">
                <i class="far fa-clock text-indigo-500"></i>
                <span>Today: <?php echo date('l, M d'); ?></span>
            </div>
        </div>

        <?php if (empty($timetable_grouped)): ?>
            
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-12 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5 text-slate-300">
                    <i class="far fa-calendar text-4xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-700">No Classes Found</h3>
                <p class="text-slate-500 mt-2 mb-6">You haven't enrolled in any classes yet.</p>
                <a href="my_classes.php" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition shadow-lg shadow-indigo-500/20">
                    Browse Classes
                </a>
            </div>

        <?php else: ?>

            <!-- Timeline Layout -->
            <div class="space-y-6">
                
                <?php foreach ($days_order as $day_name): ?>
                    <?php if (isset($timetable_grouped[$day_name])): ?>
                        
                        <?php 
                        $is_today = (date('l') === $day_name);
                        $highlight_class = $is_today ? 'ring-2 ring-indigo-500 ring-offset-2' : '';
                        ?>

                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden <?php echo $highlight_class; ?>">
                            
                            <!-- Day Header -->
                            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                                <h3 class="font-bold text-slate-700 text-lg flex items-center gap-2">
                                    <?php echo $day_name; ?>
                                    <?php if ($is_today): ?>
                                        <span class="text-[10px] uppercase bg-indigo-600 text-white px-2 py-0.5 rounded-full tracking-wider">Today</span>
                                    <?php endif; ?>
                                </h3>
                                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                    <?php echo count($timetable_grouped[$day_name]); ?> Classes
                                </span>
                            </div>

                            <!-- Classes List -->
                            <div class="divide-y divide-slate-50">
                                <?php foreach ($timetable_grouped[$day_name] as $class): ?>
                                    <?php 
                                    $status = getClassStatus($class['day'], $class['time']); 
                                    ?>

                                    <div class="p-5 hover:bg-slate-50/50 transition flex flex-col md:flex-row items-start md:items-center gap-5">
                                        
                                        <!-- Time Badge -->
                                        <div class="w-full md:w-32 flex-shrink-0">
                                            <p class="text-lg font-black text-slate-800 leading-tight">
                                                <?php echo htmlspecialchars($class['time']); ?>
                                            </p>
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold mt-2 <?php echo $status['color']; ?>">
                                                <i class="<?php echo $status['icon']; ?>"></i>
                                                <?php echo $status['status']; ?>
                                            </div>
                                        </div>

                                        <!-- Class Details -->
                                        <div class="flex-grow">
                                            <h4 class="font-bold text-slate-800 text-lg">
                                                <?php echo htmlspecialchars($class['subject']); ?>
                                            </h4>
                                            <p class="text-slate-600 font-medium text-sm">
                                                <?php echo htmlspecialchars($class['class_name']); ?>
                                            </p>
                                            
                                            <div class="flex flex-wrap items-center gap-4 mt-2">
                                                <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium bg-slate-100 px-2 py-1 rounded">
                                                    <i class="fas fa-chalkboard-teacher"></i>
                                                    <?php echo htmlspecialchars($class['teacher_name']); ?>
                                                </div>
                                                <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium bg-slate-100 px-2 py-1 rounded">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <?php echo htmlspecialchars($class['hall_number']); ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action Button -->
                                        <div class="w-full md:w-auto mt-4 md:mt-0">
                                            <?php if ($status['action'] === 'active'): ?>
                                                <a href="live_class.php" class="block w-full text-center md:inline-block px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-500/30 transition transform hover:scale-105">
                                                    Join Class
                                                </a>
                                            <?php else: ?>
                                                <button disabled class="block w-full text-center md:inline-block px-5 py-2.5 bg-slate-100 text-slate-400 text-sm font-semibold rounded-xl cursor-not-allowed">
                                                    Inactive
                                                </button>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    <?php endif; ?>
                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </main>

    <!-- Footer -->
    <?php include('../includes/footer.php'); ?>

</div>