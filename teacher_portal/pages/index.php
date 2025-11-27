<?php
// 1. Path variable setup
$path = "../../"; 

// 2. Include Head
include("../include/head.php"); 
?>

<?php include("../include/sidebar.php"); ?>

<div class="p-4 sm:ml-64 pb-20">
   
   <div class="flex items-center justify-between p-4 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm">
      <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
         <span class="sr-only">Open sidebar</span>
         <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
         </svg>
      </button>

      <div class="hidden md:block w-1/2">
         <div class="relative">
             <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                 <i class="ph ph-magnifying-glass text-gray-500"></i>
             </div>
             <input type="text" id="search-navbar" class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Search students, classes...">
         </div>
      </div>

      <div class="flex items-center space-x-4">
          <button class="relative text-gray-500 hover:text-gray-700">
              <i class="ph ph-bell text-2xl"></i>
              <span class="absolute top-0 right-0 inline-flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-red-500 rounded-full">2</span>
          </button>
          
          <div class="flex items-center space-x-2">
              <div class="text-right hidden md:block">
                  <p class="text-sm font-semibold text-gray-900">Mr. Teacher Name</p>
                  <p class="text-xs text-gray-500">Physics Instructor</p>
              </div>
              <img class="w-10 h-10 rounded-full border-2 border-gray-200" src="../../assests/images/user2.jpg" alt="User dropdown">
          </div>
      </div>
   </div>

   <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
       <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
           <div class="flex items-center justify-between mb-4">
               <h3 class="text-lg font-bold text-gray-700">Total Students</h3>
               <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                   <i class="ph ph-student text-2xl"></i>
               </div>
           </div>
           <p class="text-3xl font-bold text-gray-900">120</p>
           <p class="text-sm text-green-500 mt-2 flex items-center"><i class="ph ph-trend-up mr-1"></i> +5 New this month</p>
       </div>

       <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
           <div class="flex items-center justify-between mb-4">
               <h3 class="text-lg font-bold text-gray-700">Monthly Income</h3>
               <div class="p-2 bg-green-100 rounded-lg text-green-600">
                   <i class="ph ph-money text-2xl"></i>
               </div>
           </div>
           <p class="text-3xl font-bold text-gray-900">LKR 45k</p>
           <p class="text-sm text-gray-500 mt-2">Pending: LKR 12k</p>
       </div>

       <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
           <div class="flex items-center justify-between mb-4">
               <h3 class="text-lg font-bold text-gray-700">Active Classes</h3>
               <div class="p-2 bg-purple-100 rounded-lg text-purple-600">
                   <i class="ph ph-chalkboard text-2xl"></i>
               </div>
           </div>
           <p class="text-3xl font-bold text-gray-900">05</p>
           <p class="text-sm text-gray-500 mt-2">Next: Physics (Today 4PM)</p>
       </div>
   </div>

   <div class="w-full bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
       <div class="flex justify-between items-center mb-4">
           <div>
               <h5 class="text-xl font-bold leading-none text-gray-900">Student Attendance</h5>
               <p class="text-sm text-gray-500 mt-1">Attendance overview for the last 7 days</p>
           </div>
       </div>
       <div id="attendance-chart" class="w-full"></div>
   </div>

   <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
   <script src="../js/dashboard.js"></script>

   <?php include("../include/footer.php"); ?>

</div>