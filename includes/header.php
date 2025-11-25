<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Future Minds A/L Institute</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <link rel="stylesheet" href="../css/header.css">

    
</head>
<body class="bg-slate-50 min-h-screen text-gray-800 relative">

    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <nav class="glass-nav fixed w-full z-50 top-0 left-0 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center relative">
            <div class="logo text-2xl font-bold text-indigo-700 flex items-center gap-2 cursor-pointer">
                <i class="fas fa-university"></i>
                <span>Future Minds</span>
            </div>

            <div class="hidden md:flex items-center space-x-8">
                <a href="../pages/index.php" class="text-gray-700 hover:text-indigo-600 font-medium transition nav-link">Home</a>
                <a href="../pages/Streams.php" class="text-gray-700 hover:text-indigo-600 font-medium transition nav-link">Streams</a>
                <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium transition nav-link">Timetable</a>
                <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium transition nav-link">Results</a>
                <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium transition nav-link">Teachers</a>
                <a href="../pages/Streams.php" class="text-gray-700 hover:text-indigo-600 font-medium transition nav-link">Comments</a>
            </div>

            <div class="hidden md:flex items-center space-x-4">
                <a href="../log/login.php" class="text-indigo-600 font-medium hover:text-indigo-800 transition nav-btn">LMS Login</a>
                <a href="../log/registration.php" class="bg-indigo-600 text-white px-5 py-2 rounded-full font-medium hover:bg-indigo-700 transition shadow-lg hover:shadow-indigo-500/30 nav-btn">Join 2026 Batch</a>
            </div>

            <div id="mobile-menu-btn" class="md:hidden text-2xl text-gray-600 cursor-pointer p-2 z-50">
                <i class="fas fa-bars transition-all duration-300"></i>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden absolute top-full left-0 w-full bg-white/95 backdrop-blur-md border-t border-gray-200 shadow-xl transition-all duration-300 origin-top">
            <div class="flex flex-col p-6 space-y-4 text-center">
                <a href="../pages/index.php" class="text-gray-700 hover:text-indigo-600 font-medium py-2">Home</a>
                <a href="../pages/Streams.php" class="text-gray-700 hover:text-indigo-600 font-medium py-2">Streams</a>
                <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium py-2">Timetable</a>
                <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium py-2">Results</a>
                <hr class="border-gray-200">
                <a href="#" class="text-indigo-600 font-medium py-2">LMS Login</a>
                <a href="student_register.html" class="bg-indigo-600 text-white px-5 py-3 rounded-full font-medium shadow-md hover:bg-indigo-700 mx-auto w-full max-w-xs">Join 2026 Batch</a>
            </div>
        </div>
    </nav>

    <script>
        const menuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const icon = menuBtn.querySelector('i');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            
            // Icon animation (Bars to X)
            if (mobileMenu.classList.contains('hidden')) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            } else {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            }
        });
    </script>

    