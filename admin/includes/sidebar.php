<?php
// AL-Class-Management-System/admin/includes/sidebar.php - UPDATED CODE
// FIX: Logout link path corrected.

// Active Page Check
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="fixed left-0 top-0 w-64 h-full bg-slate-900 text-white transition-all duration-300 z-50 sidebar-menu">
    <div class="flex items-center justify-center h-20 border-b border-gray-800">
        <div class="text-2xl font-bold text-white flex items-center gap-2">
            <i class="fas fa-university text-indigo-500"></i>
            <span>Future Minds</span>
        </div>
    </div>

    <div class="overflow-y-auto overflow-x-hidden flex-grow">
        <ul class="flex flex-col py-4 space-y-1">

            <li class="px-5">
                <div class="flex flex-row items-center h-8">
                    <div class="text-sm font-light tracking-wide text-gray-400">Menu</div>
                </div>
            </li>

            <li>
                <a href="index.php"
                    class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-indigo-600 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-indigo-400 pr-6 <?php echo ($current_page == 'index.php') ? 'bg-indigo-800 text-white border-indigo-500' : ''; ?>">
                    <span class="inline-flex justify-center items-center ml-4">
                        <i class="fas fa-th-large"></i>
                    </span>
                    <span class="ml-2 text-sm tracking-wide truncate">Dashboard</span>
                </a>
            </li>

            <li>
                <a href="student.php"
                    class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-indigo-600 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-indigo-400 pr-6 <?php echo ($current_page == 'student.php' || $current_page == 'student_status.php') ? 'bg-indigo-800 text-white border-indigo-500' : ''; ?>">
                    <span class="inline-flex justify-center items-center ml-4">
                        <i class="fas fa-user-graduate"></i>
                    </span>
                    <span class="ml-2 text-sm tracking-wide truncate">Students</span>
                </a>
            </li>

            <li>
                <a href="teachers.php"
                    class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-indigo-600 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-indigo-400 pr-6 <?php echo ($current_page == 'teachers.php' || $current_page == 'add_teacher.php' || $current_page == 'edit_teacher.php' || $current_page == 'delete_teacher.php') ? 'bg-indigo-800 text-white border-indigo-500' : ''; ?>">
                    <span class="inline-flex justify-center items-center ml-4">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </span>
                    <span class="ml-2 text-sm tracking-wide truncate">Teachers</span>
                </a>
            </li>
            <li>
                <a href="mark_attendance.php"
                    class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-indigo-600 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-indigo-400 pr-6 <?php echo ($current_page == 'mark_attendance.php') ? 'bg-indigo-800 text-white border-indigo-500' : ''; ?>">
                    <span class="inline-flex justify-center items-center ml-4">
                        <i class="fas fa-calendar-check"></i>
                    </span>
                    <span class="ml-2 text-sm tracking-wide truncate">Mark Attendance</span>
                </a>
            </li>
            <li>
                <a href="timetable.php"
                    class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-indigo-600 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-indigo-400 pr-6 <?php echo ($current_page == 'timetable.php' || $current_page == 'add_timetable.php' || $current_page == 'edit_timetable.php') ? 'bg-indigo-800 text-white border-indigo-500' : ''; ?>">
                    <span class="inline-flex justify-center items-center ml-4">
                        <i class="fas fa-clock"></i>
                    </span>
                    <span class="ml-2 text-sm tracking-wide truncate">Time Table</span>
                </a>
            </li>

            <li class="px-5 mt-5">
                <div class="flex flex-row items-center h-8">
                    <div class="text-sm font-light tracking-wide text-gray-400">Finance & Reports</div>
                </div>
            </li>

            <li>
                <a href="payments.php"
                    class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-indigo-600 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-indigo-400 pr-6 <?php echo ($current_page == 'payments.php') ? 'bg-indigo-800 text-white border-indigo-500' : ''; ?>">
                    <span class="inline-flex justify-center items-center ml-4">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </span>
                    <span class="ml-2 text-sm tracking-wide truncate">Payment Management</span>
                </a>
            </li>
            <li>
                <a href="accounting.php"
                    class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-indigo-600 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-indigo-400 pr-6 <?php echo ($current_page == 'accounting.php') ? 'bg-indigo-800 text-white border-indigo-500' : ''; ?>">
                    <span class="inline-flex justify-center items-center ml-4">
                        <i class="fas fa-chart-line"></i>
                    </span>
                    <span class="ml-2 text-sm tracking-wide truncate">Accounting Reports</span>
                </a>
            </li>

            <li class="px-5 mt-5">
                <div class="flex flex-row items-center h-8">
                    <div class="text-sm font-light tracking-wide text-gray-400">Settings</div>
                </div>
            </li>
            <li>
                <a href="settings.php"
                    class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-indigo-600 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-indigo-400 pr-6 <?php echo ($current_page == 'settings.php') ? 'bg-indigo-800 text-white border-indigo-500' : ''; ?>">
                    <span class="inline-flex justify-center items-center ml-4">
                        <i class="fas fa-cog"></i>
                    </span>
                    <span class="ml-2 text-sm tracking-wide truncate">System Settings</span>
                </a>
            </li>
            
            <li>
                <a href="logout.php"
                    class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-red-600 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-red-400 pr-6">
                    <span class="inline-flex justify-center items-center ml-4">
                        <i class="fas fa-sign-out-alt"></i>
                    </span>
                    <span class="ml-2 text-sm tracking-wide truncate">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>