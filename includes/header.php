<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Future Minds A/L Institute</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- GSAP for Animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }
        
        /* Moving Background Shapes */
        .shape {
            position: absolute;
            filter: blur(60px);
            z-index: -1;
            animation: float 12s infinite ease-in-out;
        }
        .shape-1 {
            top: -5%;
            left: -5%;
            width: 400px;
            height: 400px;
            background: #4f46e5; /* Indigo */
            border-radius: 50%;
            opacity: 0.4;
        }
        .shape-2 {
            bottom: 10%;
            right: -10%;
            width: 350px;
            height: 350px;
            background: #ec4899; /* Pink */
            border-radius: 50%;
            animation-delay: 2s;
            opacity: 0.4;
        }
        .shape-3 {
            top: 40%;
            left: 30%;
            width: 200px;
            height: 200px;
            background: #06b6d4; /* Cyan */
            border-radius: 50%;
            animation-delay: 4s;
            opacity: 0.3;
        }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0, 0) scale(1); }
        }

        /* Glassmorphism Styles */
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .glass-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            background: rgba(255, 255, 255, 0.9);
        }

        /* Hero Image Container */
        .hero-img-container {
            position: relative;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-gray-800 relative">

    <!-- Background Shapes -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <!-- Navigation Bar -->
    <nav class="glass-nav fixed w-full z-50 top-0 left-0 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <!-- Logo -->
            <div class="logo text-2xl font-bold text-indigo-700 flex items-center gap-2 cursor-pointer">
                <i class="fas fa-university"></i>
                <span>Future Minds</span>
            </div>

            <!-- Menu (Desktop) -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium transition nav-link">Home</a>
                <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium transition nav-link">Streams</a>
                <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium transition nav-link">Timetable</a>
                <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium transition nav-link">Results</a>
            </div>

            <!-- Buttons -->
            <div class="hidden md:flex items-center space-x-4">
                <a href="#" class="text-indigo-600 font-medium hover:text-indigo-800 transition nav-btn">LMS Login</a>
                <a href="student_register.html" class="bg-indigo-600 text-white px-5 py-2 rounded-full font-medium hover:bg-indigo-700 transition shadow-lg hover:shadow-indigo-500/30 nav-btn">Join 2026 Batch</a>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden text-2xl text-gray-600 cursor-pointer">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 container mx-auto px-6">