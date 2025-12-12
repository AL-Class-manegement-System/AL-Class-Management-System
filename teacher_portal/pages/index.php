<?php
// teacher_portal/pages/index.php
// Updated: Shows New Enrollments Notification & Dashboard Stats

// 1. Session Start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Security Check
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../../log/login.php");
    exit();
}

// 3. Include Configs
$path = "../../"; 
include("../include/head.php"); 
require_once("../../includes/connection.php"); // DB Connection

// 4. Teacher Data
$teacher_db_id = $_SESSION['teacher_db_id']; // Teacher's DB ID
$teacher_name = $_SESSION['full_name'] ?? 'Teacher';
$teacher_pic = $_SESSION['profile_pic'] ?? '';
$teacher_subject = $_SESSION['subject'] ?? '';

// Profile Picture Path Logic
$image_folder = "../../assets/images/teachers/";
$default_image = "../../assests/images/user2.jpg"; 
$display_image = (!empty($teacher_pic) && file_exists($image_folder . $teacher_pic)) ? $image_folder . $teacher_pic : $default_image;

// ==========================================
// DASHBOARD DATA QUERIES
// ==========================================

// A. අලුතින් එකතු වූ සිසුන් (පසුගිය දින 7 ඇතුලත)
$new_students = [];
$sql_new = "
    SELECT s.full_name, c.class_name, e.joined_date
    FROM enrollments e
    JOIN classes c ON e.class_id = c.class_id
    JOIN students s ON e.student_id = s.student_id
    JOIN teachers t ON c.teacher_name = t.full_name
    WHERE t.teacher_id = ? 
    AND e.joined_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ORDER BY e.joined_date DESC LIMIT 5
";
$stmt = $conn->prepare($sql_new);
$stmt->bind_param("i", $teacher_db_id);
$stmt->execute();
$res_new = $stmt->get_result();
while ($row = $res_new->fetch_assoc()) {
    $new_students[] = $row;
}
$stmt->close();

// B. මුළු සිසුන් ගණන (Total Students)
$total_std_sql = "
    SELECT COUNT(DISTINCT e.student_id) as count 
    FROM enrollments e 
    JOIN classes c ON e.class_id = c.class_id 
    JOIN teachers t ON c.teacher_name = t.full_name 
    WHERE t.teacher_id = ?
";
$stmt2 = $conn->prepare($total_std_sql);
$stmt2->bind_param("i", $teacher_db_id);
$stmt2->execute();
$total_students = $stmt2->get_result()->fetch_assoc()['count'];
$stmt2->close();

// C. සක්‍රීය පන්ති ගණන (Active Classes)
$active_cls_sql = "
    SELECT COUNT(*) as count 
    FROM classes c 
    JOIN teachers t ON c.teacher_name = t.full_name 
    WHERE t.teacher_id = ? AND c.status = 1
";
$stmt3 = $conn->prepare($active_cls_sql);
$stmt3->bind_param("i", $teacher_db_id);
$stmt3->execute();
$active_classes = $stmt3->get_result()->fetch_assoc()['count'];
$stmt3->close();
?>

<?php include("../include/sidebar.php"); ?>

<div class="p-4 sm:ml-64 pb-20">
   
   <div class="flex items-center justify-between p-4 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm">
      <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
         <span class="sr-only">Open sidebar</span>
         <i class="ph ph-list text-2xl"></i>
      </button>

      <div class="hidden md:block">
         <h2 class="text-xl font-bold text-gray-800">Welcome, <?php echo htmlspecialchars($teacher_name); ?>! 👋</h2>
         <p class="text-xs text-gray-500">Here's what's happening with your classes today.</p>
      </div>

      <div class="flex items-center space-x-4">
          <button class="relative text-gray-500 hover:text-gray-700">
              <i class="ph ph-bell text-2xl"></i>
              <?php if(count($new_students) > 0): ?>
                <span class="absolute top-0 right-0 inline-flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-red-500 rounded-full"><?php echo count($new_students); ?></span>
              <?php endif; ?>
          </button>
          
          <div class="flex items-center space-x-2">
              <div class="text-right hidden md:block">
                  <p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($teacher_name); ?></p>
                  <p class="text-xs text-gray-500"><?php echo htmlspecialchars($teacher_subject); ?></p>
              </div>
              <img class="w-10 h-10 rounded-full border-2 border-gray-200 object-cover" src="<?php echo $display_image; ?>" alt="User profile">
          </div>
      </div>
   </div>

   <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
       <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
           <div class="flex items-center justify-between mb-4">
               <h3 class="text-lg font-bold text-gray-700">Total Students</h3>
               <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                   <i class="ph ph-student text-2xl"></i>
               </div>
           </div>
           <p class="text-3xl font-bold text-gray-900"><?php echo $total_students; ?></p>
           <p class="text-sm text-gray-500 mt-2 flex items-center">
               <span class="text-green-500 font-bold mr-1">+<?php echo count($new_students); ?></span> New this week
           </p>
       </div>

       <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
           <div class="flex items-center justify-between mb-4">
               <h3 class="text-lg font-bold text-gray-700">Active Classes</h3>
               <div class="p-2 bg-purple-100 rounded-lg text-purple-600">
                   <i class="ph ph-chalkboard text-2xl"></i>
               </div>
           </div>
           <p class="text-3xl font-bold text-gray-900"><?php echo $active_classes; ?></p>
           <p class="text-sm text-gray-500 mt-2">Check your timetable for details.</p>
       </div>
   </div>

   <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
       
       <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 h-full">
           <div class="flex justify-between items-center mb-4 border-b pb-2">
               <h5 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                   <i class="ph ph-user-plus text-green-600"></i> New Enrollments
               </h5>
               <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2.5 py-0.5 rounded">Last 7 Days</span>
           </div>
           
           <?php if (!empty($new_students)): ?>
               <ul class="divide-y divide-gray-100">
                   <?php foreach ($new_students as $std): ?>
                   <li class="py-3 flex justify-between items-center hover:bg-gray-50 transition px-2 rounded-lg">
                       <div class="flex items-center gap-3">
                           <div class="bg-indigo-100 text-indigo-600 rounded-full w-8 h-8 flex items-center justify-center font-bold text-xs">
                               <?php echo strtoupper(substr($std['full_name'], 0, 1)); ?>
                           </div>
                           <div>
                               <p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($std['full_name']); ?></p>
                               <p class="text-xs text-gray-500">Class: <?php echo htmlspecialchars($std['class_name']); ?></p>
                           </div>
                       </div>
                       <div class="text-right">
                           <span class="block text-xs font-medium text-gray-600">Joined</span>
                           <span class="text-xs text-gray-400"><?php echo date('M d', strtotime($std['joined_date'])); ?></span>
                       </div>
                   </li>
                   <?php endforeach; ?>
               </ul>
           <?php else: ?>
               <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                   <i class="ph ph-users text-4xl mb-2"></i>
                   <p class="text-sm">No new students joined this week.</p>
               </div>
           <?php endif; ?>
       </div>

       <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 h-full">
           <div class="flex justify-between items-center mb-4">
               <div>
                   <h5 class="text-lg font-bold text-gray-900">Attendance Overview</h5>
                   <p class="text-sm text-gray-500">Weekly student participation</p>
               </div>
           </div>
           <div id="attendance-chart" class="w-full h-64"></div>
       </div>
   </div>

   <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
   <script src="../js/dashboard.js"></script>

   <?php include("../include/footer.php"); ?>

</div>