<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../js/dashboard.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-200 font-sans">

    <div class="flex h-screen overflow-hidden">

        <aside class="w-64 bg-slate-900 text-white hidden md:flex flex-col shadow-xl z-20">
            <div class="h-20 flex items-center px-8 border-b border-slate-700">
                <i class="fas fa-graduation-cap text-3xl text-primary mr-3"></i>
                <span class="text-xl font-bold tracking-wide">Student Portal</span>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 ">
                <a href="#"
                    class="flex items-center px-4 py-3 bg-primary text-white rounded-xl transition-transform transform hover:scale-105 shadow-lg shadow-indigo-500/30">
                    <i class="fas fa-th-large w-6"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="#"
                    class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all">
                    <i class="fas fa-book w-6"></i>
                    <span class="font-medium">My Classes</span>
                </a>

                <a href="#"
                    class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all">
                    <i class="fas fa-clipboard-list w-6"></i>
                    <span class="font-medium">Exam Results</span>
                </a>

                <a href="#"
                    class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all">
                    <i class="fas fa-calendar-alt w-6"></i>
                    <span class="font-medium">Time Table</span>
                </a>
                <a href="#"
                    class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all">
                    <i class="fas fa-calendar-alt w-6"></i>
                    <span class="font-medium">Ongoing Classes</span>
                </a>
                <a href="#"
                    class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all">
                    <i class="fas fa-calendar-alt w-6"></i>
                    <span class="font-medium">Past Lessons</span>
                </a>
                <a href="#"
                    class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all">
                    <i class="fas fa-calendar-alt w-6"></i>
                    <span class="font-medium">Study Packes</span>
                </a>
            </nav>

            <div class="p-4 border-t border-slate-700">
                <a href="../../lib/logout.php"
                    class="flex items-center px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-xl transition-all">
                    <i class="fas fa-sign-out-alt w-6"></i>
                    <span class="font-medium">Logout</span>
                </a>
            </div>

        </aside>


        <div class="flex-1 flex flex-col h-screen overflow-y-auto">
            <header class="m-4 mb-0 rounded-xl h-20 bg-white shadow-sm flex items-center justify-between px-8 sticky top-0 z-10">
                <div class="md:hidden">
                    <button class="text-slate-500 hover:text-primary"><i class="fas fa-bars text-2xl"></i></button>
                </div>

                <h2 class="text-2xl font-bold text-purple-800 hidden md:block">Dashboard</h2>

                <div class="flex items-center gap-4">
                    <button
                        class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:text-primary transition relative">
                        <i class="fas fa-bell"></i>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <div class="flex items-center gap-3 pl-4 border-l">
                        <div class="text-right hidden sm:block">
                            <p class="text-md font-bold text-slate-700">
                                <?php echo htmlspecialchars($student['full_name']); ?>
                            </p>
                            <!-- <p class="text-xs text-slate-500"><?php echo htmlspecialchars($student_id); ?></p> -->
                        </div>
                        <div class="flex items-center gap-2 relative">

                            <div class="relative ml-3">

                                <button onclick="toggleDropdown()" class="flex items-center focus:outline-none">
                                    <!-- <div class="text-right hidden sm:block mr-3">
                                        <p class="text-sm font-bold text-slate-700">
                                            <?php echo htmlspecialchars($full_name); ?></p>
                                        <p class="text-xs text-slate-500"><?php echo htmlspecialchars($student_id); ?>
                                        </p>
                                    </div> -->
                                    <img src="<?php echo $profile_pic; ?>"
                                        class="w-10 h-10 rounded-full object-cover border-2 border-slate-200 hover:border-primary transition cursor-pointer">
                                </button>

                                <div id="userDropdown"
                                    class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5 transition-all duration-200 ease-out origin-top-right transform">

                                    <div class="px-4 py-2 border-b border-gray-100 md:hidden">
                                        <p class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($full_name); ?>
                                        </p>
                                    </div>

                                    <a href="#"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        <i class="fas fa-user mr-2 w-4"></i> Your Profile
                                    </a>
                                    <a href="#"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        <i class="fas fa-cog mr-2 w-4"></i> Settings
                                    </a>
                                    <div class="border-t border-gray-100"></div>
                                    <a href="../../lib/logout.php"
                                        class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700">
                                        <i class="fas fa-sign-out-alt mr-2 w-4"></i> Sign out
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </header>