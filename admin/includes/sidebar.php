<?php
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
                    class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-indigo-600 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-indigo-400 pr-6 <?php echo ($current_page == 'student.php') ? 'bg-indigo-800 text-white border-indigo-500' : ''; ?>">
                    <span class="inline-flex justify-center items-center ml-4">
                        <i class="fas fa-user-graduate"></i>
                    </span>
                    <span class="ml-2 text-sm tracking-wide truncate">Students</span>
                </a>
            </li>

            <li class="px-5 mt-5">
                <div class="flex flex-row items-center h-8">
                    <div class="text-sm font-light tracking-wide text-gray-400">Settings</div>
                </div>
            </li>

            <li>
                <a href="../log/login.php"
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