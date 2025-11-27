<?php
include("../includes/header.php");
?>

<div class="bg-gray-50 min-h-screen font-sans">
    
    <div class="bg-gradient-to-r from-blue-900 to-blue-600 text-white shadow-lg">
        <div class="max-w-screen-xl mx-auto px-4 py-10 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0 text-center md:text-left">
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">Welcome to Student Portal 👋</h1>
                <p class="mt-2 text-blue-100 text-lg opacity-90">Manage your classes, payments, and assignments in one place.</p>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-4 text-center border border-white/20">
                <p class="text-sm font-medium text-blue-100 uppercase tracking-wider">Today is</p>
                <p class="text-2xl font-bold text-white"><?php echo date("F j"); ?></p>
                <p class="text-sm text-blue-200"><?php echo date("l"); ?></p>
            </div>
        </div>
    </div>

    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="bg-white rounded-xl shadow-md p-6 flex items-center border-l-4 border-blue-500 transition-transform hover:scale-105 duration-300">
                <div class="p-4 rounded-full bg-blue-50 text-blue-600 mr-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-semibold uppercase tracking-wide">My Subjects</p>
                    <p class="text-3xl font-bold text-gray-800">03</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 flex items-center border-l-4 border-green-500 transition-transform hover:scale-105 duration-300">
                <div class="p-4 rounded-full bg-green-50 text-green-600 mr-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-semibold uppercase tracking-wide">Attendance</p>
                    <p class="text-3xl font-bold text-gray-800">92%</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 flex items-center border-l-4 border-purple-500 transition-transform hover:scale-105 duration-300">
                <div class="p-4 rounded-full bg-purple-50 text-purple-600 mr-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-semibold uppercase tracking-wide">Next Class</p>
                    <p class="text-lg font-bold text-gray-800">Chemistry</p>
                    <p class="text-xs text-gray-400">Today at 4:00 PM</p>
                </div>
            </div>

        </div>
    </div>

    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-800 border-l-4 border-blue-600 pl-4">Dashboard Menu</h2>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <a href="#" class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-blue-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                <div class="p-8">
                    <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">Class Schedule</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4">View your weekly timetable, upcoming classes, and join Zoom sessions directly.</p>
                    <span class="inline-flex items-center text-sm font-semibold text-blue-600 group-hover:translate-x-2 transition-transform duration-300">
                        View Time Table <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </span>
                </div>
            </a>

            <a href="../PayHere/PayHere/index.php" class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-green-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                <div class="p-8">
                    <div class="w-14 h-14 bg-green-50 rounded-xl flex items-center justify-center text-green-600 mb-6 group-hover:bg-green-600 group-hover:text-white transition-colors duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-green-600 transition-colors">Payments</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4">Pay your class fees online securely and view your past payment history.</p>
                    <span class="inline-flex items-center text-sm font-semibold text-green-600 group-hover:translate-x-2 transition-transform duration-300">
                        Pay Fees <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </span>
                </div>
            </a>

            <a href="#" class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-purple-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                <div class="p-8">
                    <div class="w-14 h-14 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 mb-6 group-hover:bg-purple-600 group-hover:text-white transition-colors duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-purple-600 transition-colors">Assignments</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4">Access learning materials, download notes, and submit your homework.</p>
                    <span class="inline-flex items-center text-sm font-semibold text-purple-600 group-hover:translate-x-2 transition-transform duration-300">
                        View Assignments <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </span>
                </div>
            </a>

            <a href="#" class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-yellow-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                <div class="p-8">
                    <div class="w-14 h-14 bg-yellow-50 rounded-xl flex items-center justify-center text-yellow-600 mb-6 group-hover:bg-yellow-600 group-hover:text-white transition-colors duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-yellow-600 transition-colors">Exam Results</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4">Check your exam marks, progress reports, and class rankings.</p>
                    <span class="inline-flex items-center text-sm font-semibold text-yellow-600 group-hover:translate-x-2 transition-transform duration-300">
                        View Results <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </span>
                </div>
            </a>

             <a href="#" class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gray-600 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                <div class="p-8">
                    <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center text-gray-600 mb-6 group-hover:bg-gray-700 group-hover:text-white transition-colors duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-gray-700 transition-colors">My Profile</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4">Update your contact details, change password, and manage settings.</p>
                    <span class="inline-flex items-center text-sm font-semibold text-gray-600 group-hover:translate-x-2 transition-transform duration-300">
                        Edit Profile <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </span>
                </div>
            </a>

             <a href="../pages/contact.php" class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-red-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                <div class="p-8">
                    <div class="w-14 h-14 bg-red-50 rounded-xl flex items-center justify-center text-red-600 mb-6 group-hover:bg-red-600 group-hover:text-white transition-colors duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-red-600 transition-colors">Help & Support</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4">Having trouble? Contact our support team for assistance.</p>
                    <span class="inline-flex items-center text-sm font-semibold text-red-600 group-hover:translate-x-2 transition-transform duration-300">
                        Contact Us <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </span>
                </div>
            </a>

        </div>
    </div>
</div>

