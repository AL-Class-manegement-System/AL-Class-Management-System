<?php
session_start(); // 1. Session Start කරන්න ඕන මුලින්ම
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login — School System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#6366f1',
                        primaryHover: '#4f46e5',
                        darkCard: '#0f172a',
                        darkInput: '#1e293b',
                    }
                }
            }
        }
    </script>
    <script src="js/theme.js"></script>

    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .bg-overlay {
            background: rgba(255, 255, 255, 0.85);
        }
        .dark .bg-overlay {
            background: rgba(2, 6, 23, 0.85);
        }
    </style>
</head>

<body class="h-screen w-full font-sans antialiased relative text-slate-600 dark:text-gray-200 transition-colors duration-300">

    <div class="absolute inset-0 bg-overlay z-0 transition-colors duration-300"></div>

    <!-- Theme Toggle Button -->
    <button onclick="toggleTheme()" class="absolute top-6 right-6 z-30 p-2 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 text-slate-800 dark:text-white hover:bg-white/40 transition-all shadow-lg">
        <i id="theme-toggle-icon" class="fas fa-moon text-lg w-6 h-6 flex items-center justify-center"></i>
    </button>

    <a href="../index.php" class="absolute top-6 left-6 flex items-center gap-3 text-slate-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-white transition-all duration-300 z-20 group">
        <div class="w-10 h-10 rounded-full bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm flex items-center justify-center border border-white/50 dark:border-slate-700 group-hover:bg-primary group-hover:text-white group-hover:border-primary transition-all shadow-lg">
            <i class="fas fa-arrow-left text-sm group-hover:-translate-x-0.5 transition-transform"></i>
        </div>
        <span class="font-semibold text-sm tracking-wide opacity-0 -translate-x-2 sm:opacity-100 sm:translate-x-0 transition-all duration-300">Back to Home</span>
    </a>

    <div class="relative z-10 min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">

        <div class="max-w-md w-full space-y-8 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md p-8 rounded-2xl shadow-2xl border border-white/50 dark:border-slate-700/50 transform transition-all hover:scale-[1.01]">

            <div class="text-center">
                <div class="mx-auto h-12 w-12 bg-primary/10 dark:bg-primary/20 rounded-full flex items-center justify-center mb-4 border border-primary/20 dark:border-primary/30">
                    <i class="fas fa-school text-primary dark:text-indigo-400 text-xl"></i>
                </div>
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Welcome Back
                </h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">
                    Sign in to your account
                </p>
            </div>

            <form class="mt-8 space-y-6" method="POST" action="../lib/loginbackend.php">

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md mb-4 flex items-start gap-3 dark:bg-red-500/10 dark:text-red-200">
                        <i class="fas fa-circle-exclamation text-red-500 dark:text-red-400 mt-0.5"></i>
                        <div>
                            <h3 class="text-sm font-bold text-red-800 dark:text-red-200">Login Failed</h3>
                            <p class="text-xs text-red-600 dark:text-red-300 mt-1">
                                <?php 
                                    echo htmlspecialchars($_SESSION['error']); 
                                    unset($_SESSION['error']); // 3. පණිවිඩය පෙන්නුවාට පස්සේ මකලා දානවා
                                ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="rounded-md space-y-5">

                    <div class="relative">
                        <label for="username" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-1">Username / User ID</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-slate-400 dark:text-gray-500 text-sm"></i>
                            </div>
                            <input type="text" name="username" id="username" required autocomplete="username"
                                class="block w-full pl-10 pr-3 py-3 bg-slate-50 dark:bg-darkInput border border-slate-300 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent sm:text-sm transition duration-200 ease-in-out"
                                placeholder="Enter your ID">
                        </div>
                    </div>

                    <div class="relative">
                        <div class="flex justify-between items-center mb-1">
                            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-gray-300">Password</label>
                            <a href="forgot_password.php" class="text-xs font-medium text-primary hover:text-primaryHover dark:text-indigo-400 dark:hover:text-indigo-300 hover:underline">
                                Forgot Password?
                            </a>
                        </div>

                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-slate-400 dark:text-gray-500 text-sm"></i>
                            </div>

                            <input type="password" name="password" id="password" required autocomplete="current-password"
                                class="block w-full pl-10 pr-10 py-3 bg-slate-50 dark:bg-darkInput border border-slate-300 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent sm:text-sm transition duration-200 ease-in-out"
                                placeholder="Enter your password">

                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer z-20" onclick="togglePasswordVisibility()">
                                <i class="fas fa-eye text-slate-400 dark:text-gray-500 hover:text-primary dark:hover:text-indigo-400 transition-colors" id="toggleIcon"></i>
                            </div>
                        </div>

                    </div>

                </div>

                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-primary hover:bg-primaryHover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-900 focus:ring-primary shadow-lg shadow-indigo-500/30 transition-all duration-300 ease-in-out transform hover:-translate-y-1">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-right-to-bracket text-indigo-200 group-hover:text-white transition ease-in-out duration-150"></i>
                        </span>
                        Sign In
                    </button>
                </div>

                <div class="text-center mt-4">
                    <p class="text-sm text-slate-500 dark:text-gray-400">
                        Don't have an account?
                        <a href="../log/registration.php" class="font-bold text-primary hover:text-primaryHover dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-200 hover:underline">
                            Register here
                        </a>
                    </p>
                </div>

            </form>
        </div>

        <div class="absolute bottom-4 text-center w-full text-slate-600 dark:text-slate-500 text-xs z-10 font-medium">
            &copy; <?php echo date("Y"); ?> School Management System. All rights reserved.
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>