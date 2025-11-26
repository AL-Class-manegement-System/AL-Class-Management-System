<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Future Minds</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#4F46E5', // Indigo 600
                        secondary: '#1E293B', // Slate 800
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shadow-xl">
            
            <div class="flex items-center justify-center h-20 border-b border-slate-800 bg-slate-950">
                <div class="flex items-center gap-3">
                    <i class="fas fa-university text-indigo-500 text-2xl"></i>
                    <span class="text-xl font-bold tracking-wide">Future Minds</span>
                </div>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Main</p>
                
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-medium bg-indigo-600 text-white rounded-xl shadow-lg shadow-indigo-500/30 transition-all">
                    <i class="fas fa-th-large w-5"></i>
                    Dashboard
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 rounded-xl transition-all">
                    <i class="fas fa-user-graduate w-5"></i>
                    Students
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 rounded-xl transition-all">
                    <i class="fas fa-chalkboard-teacher w-5"></i>
                    Teachers
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 rounded-xl transition-all">
                    <i class="fas fa-book-open w-5"></i>
                    Classes & Subjects
                </a>

                <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mt-6 mb-2">Finance & Exams</p>

                <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 rounded-xl transition-all">
                    <i class="fas fa-file-invoice-dollar w-5"></i>
                    Payments
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 rounded-xl transition-all">
                    <i class="fas fa-clipboard-check w-5"></i>
                    Attendance
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 rounded-xl transition-all">
                    <i class="fas fa-poll w-5"></i>
                    Exam Results
                </a>
            </nav>

            <div class="p-4 border-t border-slate-800">
                <a href="../log/login.php" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-red-400 hover:text-red-300 hover:bg-slate-800 rounded-xl transition-all">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    Logout
                </a>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <header class="bg-white border-b border-gray-200 h-20 flex items-center justify-between px-6 lg:px-10">
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-indigo-600 focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>

                <div class="hidden sm:flex items-center bg-gray-100 rounded-full px-4 py-2 w-64 border border-transparent focus-within:border-indigo-300 focus-within:ring-2 focus-within:ring-indigo-100 transition-all">
                    <i class="fas fa-search text-gray-400 mr-2"></i>
                    <input type="text" placeholder="Search..." class="bg-transparent border-none focus:outline-none text-sm w-full text-gray-700">
                </div>

                <div class="flex items-center gap-4 lg:gap-6">
                    <button class="relative p-2 text-gray-400 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>

                    <div class="flex items-center gap-3 cursor-pointer pl-4 border-l border-gray-200">
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-bold text-gray-800">Admin User</p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 border border-indigo-200">
                            <i class="fas fa-user-shield"></i>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 lg:p-10">
                
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
                        <p class="text-gray-500 text-sm mt-1">Welcome back, here's what's happening today.</p>
                    </div>
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium shadow-lg shadow-indigo-500/30 flex items-center gap-2 transition-transform active:scale-95">
                        <i class="fas fa-plus"></i> New Registration
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Total Students</p>
                                <h3 class="text-3xl font-bold text-gray-800">1,250</h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs text-green-600 bg-green-50 w-fit px-2 py-1 rounded-lg">
                            <i class="fas fa-arrow-up mr-1"></i>
                            <span>+12% from last month</span>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Monthly Income</p>
                                <h3 class="text-3xl font-bold text-gray-800">LKR 450k</h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
                                <i class="fas fa-coins text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs text-green-600 bg-green-50 w-fit px-2 py-1 rounded-lg">
                            <i class="fas fa-arrow-up mr-1"></i>
                            <span>+5% from last month</span>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Active Classes</p>
                                <h3 class="text-3xl font-bold text-gray-800">24</h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
                                <i class="fas fa-chalkboard text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs text-gray-500 bg-gray-100 w-fit px-2 py-1 rounded-lg">
                            <span>4 Classes Today</span>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Teachers</p>
                                <h3 class="text-3xl font-bold text-gray-800">18</h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-pink-50 flex items-center justify-center text-pink-600">
                                <i class="fas fa-chalkboard-teacher text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs text-gray-500 bg-gray-100 w-fit px-2 py-1 rounded-lg">
                            <span>All Active</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">Recent Registrations</h3>
                        <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All</a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="px-6 py-4">Student</th>
                                    <th class="px-6 py-4">Reg No</th>
                                    <th class="px-6 py-4">Stream</th>
                                    <th class="px-6 py-4">Date</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                                KS
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800">Kasun Silva</p>
                                                <p class="text-xs text-gray-500">kasun@example.com</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">ST2025001</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            Maths
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">Nov 24, 2025</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button class="text-gray-400 hover:text-indigo-600 transition-colors"><i class="fas fa-edit"></i></button>
                                    </td>
                                </tr>
                                
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-pink-100 flex items-center justify-center text-pink-600 font-bold text-xs">
                                                AP
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800">Amasha Perera</p>
                                                <p class="text-xs text-gray-500">amasha@example.com</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">ST2025002</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            Bio Science
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">Nov 23, 2025</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700">
                                            Pending
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button class="text-gray-400 hover:text-indigo-600 transition-colors"><i class="fas fa-edit"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>

        <div x-show="sidebarOpen" @click="sidebarOpen = false" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden glass-backdrop">
        </div>
    </div>

</body>
</html>