<?php
// admin/includes/sidebar.php

// Active Link එක හඳුනාගැනීමේ Function එක
function getAdminActive($page)
{
   $current_page = basename($_SERVER['PHP_SELF']);
   if ($current_page == $page) {
      // Active State: නිල් පැහැති පසුබිමක් සහ සුදු අකුරු
      return 'bg-indigo-600 text-white shadow-sm';
   } else {
      // Inactive State: අළු පැහැති අකුරු
      return 'text-slate-400 hover:bg-slate-800 hover:text-white';
   }
}
?>

<aside id="logo-sidebar"
   class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full bg-slate-900 border-r border-slate-800 sm:translate-x-0 flex flex-col font-sans"
   aria-label="Sidebar">

   <div class="h-16 flex items-center px-5 border-b border-slate-800 bg-slate-950">
      <a href="dashboard.php" class="flex items-center gap-3 group">
         <div class="p-1.5 bg-indigo-600 rounded-md group-hover:bg-indigo-500 transition-colors">
            <i class="fas fa-graduation-cap text-white text-lg"></i>
         </div>
         <span class="self-center text-base font-bold whitespace-nowrap text-white tracking-wide">Admin Panel</span>
      </a>
   </div>

   <div class="flex-1 px-3 py-4 overflow-y-auto custom-scrollbar">
      <ul class="space-y-1 font-medium">

         <li>
            <a href="dashboard.php"
               class="flex items-center p-2 rounded-lg transition-all duration-200 group <?php echo getAdminActive('dashboard.php'); ?>">
               <i class="fas fa-th-large w-5 h-5 transition duration-75 text-center"></i>
               <span class="ms-3 text-sm">Dashboard</span>
            </a>
         </li>

         <li class="pt-3 pb-1 px-2">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">User Management</span>
         </li>

         <li>
            <a href="student.php"
               class="flex items-center p-2 rounded-lg transition-all duration-200 group <?php echo getAdminActive('student.php'); ?>">
               <i class="fas fa-user-graduate w-5 h-5 transition duration-75 text-center"></i>
               <span class="ms-3 text-sm">Students</span>
            </a>
         </li>

         <li>
            <a href="teachers.php"
               class="flex items-center p-2 rounded-lg transition-all duration-200 group <?php echo getAdminActive('teachers.php'); ?>">
               <i class="fas fa-chalkboard-teacher w-5 h-5 transition duration-75 text-center"></i>
               <span class="ms-3 text-sm">Teachers</span>
            </a>
         </li>

         <li class="pt-3 pb-1 px-2">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Academic</span>
         </li>

         <li>
            <a href="timetable.php"
               class="flex items-center p-2 rounded-lg transition-all duration-200 group <?php echo getAdminActive('timetable.php'); ?>">
               <i class="fas fa-calendar-alt w-5 h-5 transition duration-75 text-center"></i>
               <span class="ms-3 text-sm">Timetable</span>
            </a>
         </li>

         <li>
            <a href="approve_exams.php"
               class="flex items-center p-2 rounded-lg transition-all duration-200 group <?php echo getAdminActive('approve_exams.php'); ?>">
               <i class="fas fa-file-signature w-5 h-5 transition duration-75 text-center"></i>
               <span class="ms-3 text-sm">Approve Exams</span>
            </a>
         </li>

         <li>
            <a href="manage_study_materials.php"
               class="flex items-center p-2 rounded-lg transition-all duration-200 group <?php echo getAdminActive('manage_study_materials.php'); ?>">
               <i class="fas fa-book w-5 h-5 transition duration-75 text-center"></i>
               <span class="ms-3 text-sm">Study Materials</span>
            </a>
         </li>

         <li>
            <a href="manage_live_class.php"
               class="flex items-center p-2 rounded-lg transition-all duration-200 group <?php echo getAdminActive('manage_live_class.php'); ?>">
               <i class="fas fa-video w-5 h-5 transition duration-75 text-center"></i>
               <span class="ms-3 text-sm">Live Classes</span>
            </a>
         </li>

         <li class="pt-3 pb-1 px-2">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Administration</span>
         </li>

         <li>
            <a href="mark_attendance.php"
               class="flex items-center p-2 rounded-lg transition-all duration-200 group <?php echo getAdminActive('mark_attendance.php'); ?>">
               <i class="fas fa-clipboard-check w-5 h-5 transition duration-75 text-center"></i>
               <span class="ms-3 text-sm">Attendance</span>
            </a>
         </li>

         <li>
            <a href="payments.php"
               class="flex items-center p-2 rounded-lg transition-all duration-200 group <?php echo getAdminActive('payments.php'); ?>">
               <i class="fas fa-money-check-alt w-5 h-5 transition duration-75 text-center"></i>
               <span class="ms-3 text-sm">Payments</span>
            </a>
         </li>

         <li>
            <a href="add_manual_payment.php"
               class="flex items-center p-2 rounded-lg transition-all duration-200 group <?php echo getAdminActive('add_manual_payment.php'); ?>">
               <i class="fas fa-hand-holding-usd w-5 h-5 transition duration-75 text-center"></i>
               <span class="ms-3 text-sm">Manual Payment</span>
            </a>
         </li>

         <li>
            <a href="accounting.php"
               class="flex items-center p-2 rounded-lg transition-all duration-200 group <?php echo getAdminActive('accounting.php'); ?>">
               <i class="fas fa-calculator w-5 h-5 transition duration-75 text-center"></i>
               <span class="ms-3 text-sm">Accounting</span>
            </a>
         </li>

         <li>
            <a href="manage_notices.php"
               class="flex items-center p-2 rounded-lg transition-all duration-200 group <?php echo getAdminActive('manage_notices.php'); ?>">
               <i class="fas fa-bullhorn w-5 h-5 transition duration-75 text-center"></i>
               <span class="ms-3 text-sm">Notices</span>
            </a>
         </li>

         <li class="pt-3 pb-1 px-2">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">System</span>
         </li>

         <li>
            <a href="settings.php"
               class="flex items-center p-2 rounded-lg transition-all duration-200 group <?php echo getAdminActive('settings.php'); ?>">
               <i class="fas fa-cog w-5 h-5 transition duration-75 text-center"></i>
               <span class="ms-3 text-sm">Settings</span>
            </a>
         </li>

      </ul>
   </div>

   <div class="p-3 border-t border-slate-800 bg-slate-950">
      <a href="logout.php"
         class="flex items-center p-2 text-red-400 rounded-lg hover:bg-red-500/10 hover:text-red-300 transition-all duration-200 group">
         <i class="fas fa-sign-out-alt w-5 h-5 text-center transition duration-75"></i>
         <span class="ms-3 text-sm font-medium">Log Out</span>
      </a>
   </div>

</aside>

<style>
   .custom-scrollbar::-webkit-scrollbar {
      width: 3px;
   }

   .custom-scrollbar::-webkit-scrollbar-track {
      background: transparent;
   }

   .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #334155;
      border-radius: 10px;
   }

   .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: #475569;
   }
</style>