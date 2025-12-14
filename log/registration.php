<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                }
            }
        }
    </script>
    <script src="js/theme.js"></script>

    <style>
        body {
            background-color: #F8FAFC;
        }

        /* bg-slate-50 */
        .dark body {
            background-color: #111827;
        }

        /* bg-gray-900 */
        .required::after {
            content: " *";
            color: #EF4444;
        }

        /* text-red-500 */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #F1F5F9;
        }

        /* bg-slate-100 */
        .dark ::-webkit-scrollbar-track {
            background: #1F2937;
        }

        /* bg-gray-800 */
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 4px;
        }

        /* bg-slate-300 */
        .dark ::-webkit-scrollbar-thumb {
            background: #4B5563;
        }

        /* bg-gray-600 */
        input[type="date"] {
            cursor: pointer;
        }

        .dark input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }
    </style>
</head>

<body
    class="antialiased text-slate-600 dark:text-gray-300 py-10 px-4 bg-slate-50 dark:bg-gray-900 transition-colors duration-300">

    <!-- Theme Toggle Button -->
    <button onclick="toggleTheme()"
        class="absolute top-6 right-6 z-30 p-2 rounded-full bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border border-slate-200 dark:border-gray-700 text-slate-800 dark:text-white hover:bg-white dark:hover:bg-gray-700 transition-all shadow-lg">
        <i id="theme-toggle-icon" class="fas fa-moon text-lg w-6 h-6 flex items-center justify-center"></i>
    </button>

    <div class="max-w-3xl mx-auto">

        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-slate-100 dark:border-gray-700 relative transition-colors duration-300">

            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-center relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 -mr-8 -mt-8 w-24 h-24 rounded-full bg-white opacity-10 blur-xl pointer-events-none">
                </div>
                <div
                    class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 rounded-full bg-white opacity-10 blur-xl pointer-events-none">
                </div>

                <div class="relative z-10 flex flex-col items-center justify-center gap-4">
                    <div
                        class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30 shadow-lg transform rotate-3">
                        <i class="fa-solid fa-user-graduate text-3xl text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-white tracking-tight">Student Registration</h2>
                        <p class="text-indigo-100 text-sm mt-1">Enter the student details below to create a new account
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <?php if (isset($_GET['error'])): ?>
                    <div class="flex items-center p-4 mb-8 text-sm text-red-700 dark:text-red-200 border border-red-200 dark:border-red-500 rounded-xl bg-red-50 dark:bg-red-500/10"
                        role="alert">
                        <i class="fa-solid fa-circle-exclamation mr-3 text-xl text-red-500 dark:text-red-400"></i>
                        <div>
                            <span class="font-bold">Error:</span> <?php echo htmlspecialchars($_GET['error']); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['success'])): ?>
                    <div class="flex items-center p-4 mb-8 text-sm text-green-700 dark:text-green-200 border border-green-200 dark:border-green-500 rounded-xl bg-green-50 dark:bg-green-500/10"
                        role="alert">
                        <i class="fa-solid fa-circle-check mr-3 text-xl text-green-500 dark:text-green-400"></i>
                        <div>
                            <span class="font-bold">Success!</span> <?php echo htmlspecialchars($_GET['success']); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <form id="studentForm" action="../lib/registrationbackend.php" method="POST"
                    enctype="multipart/form-data">

                    <!-- Personal Details Section -->
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                <i class="fa-solid fa-address-card"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-gray-100">Personal Details</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-semibold text-slate-700 dark:text-gray-400 mb-2 required">Full
                                    Name</label>
                                <div class="relative group">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-gray-500 group-focus-within:text-indigo-500 dark:group-focus-within:text-indigo-400 transition-colors">
                                        <i class="fa-solid fa-user text-sm"></i>
                                    </div>
                                    <input type="text" id="fullName" name="full_name" placeholder="Enter full name"
                                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-gray-600 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none"
                                        required>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-slate-700 dark:text-gray-400 mb-2 required">NIC
                                    Number</label>
                                <div class="relative group">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-gray-500 group-focus-within:text-indigo-500 dark:group-focus-within:text-indigo-400 transition-colors">
                                        <i class="fa-solid fa-id-badge text-sm"></i>
                                    </div>
                                    <input type="text" id="nic" name="nic" placeholder="Enter NIC number"
                                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-gray-600 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none"
                                        required>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-slate-700 dark:text-gray-400 mb-2 required">Date
                                    of Birth</label>
                                <div class="relative group">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-gray-500 group-focus-within:text-indigo-500 dark:group-focus-within:text-indigo-400 transition-colors">
                                        <i class="fa-solid fa-calendar-day text-sm"></i>
                                    </div>
                                    <input type="date" id="dob" name="date_of_birth"
                                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl text-sm text-slate-900 dark:text-white focus:bg-white dark:focus:bg-gray-600 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none"
                                        required>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-slate-700 dark:text-gray-400 mb-2 required">Gender</label>
                                <div
                                    class="flex items-center space-x-6 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl px-4 py-2.5">
                                    <label
                                        class="flex items-center cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                        <input type="radio" name="gender" value="Male"
                                            class="w-4 h-4 text-indigo-600 border-slate-300 dark:border-gray-500 focus:ring-indigo-500 bg-white dark:bg-gray-600"
                                            required>
                                        <span
                                            class="ml-2 text-sm font-medium text-slate-700 dark:text-gray-300">Male</span>
                                    </label>
                                    <label
                                        class="flex items-center cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                        <input type="radio" name="gender" value="Female"
                                            class="w-4 h-4 text-indigo-600 border-slate-300 dark:border-gray-500 focus:ring-indigo-500 bg-white dark:bg-gray-600">
                                        <span
                                            class="ml-2 text-sm font-medium text-slate-700 dark:text-gray-300">Female</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-slate-700 dark:text-gray-400 mb-2">Current
                                    School</label>
                                <div class="relative group">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-gray-500 group-focus-within:text-indigo-500 dark:group-focus-within:text-indigo-400 transition-colors">
                                        <i class="fa-solid fa-school text-sm"></i>
                                    </div>
                                    <input type="text" id="school" name="school" placeholder="School Name"
                                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-gray-600 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-semibold text-slate-700 dark:text-gray-400 mb-2">Address</label>
                                <div class="relative group">
                                    <div
                                        class="absolute top-3 left-3 flex items-center pointer-events-none text-slate-400 dark:text-gray-500 group-focus-within:text-indigo-500 dark:group-focus-within:text-indigo-400 transition-colors">
                                        <i class="fa-solid fa-location-dot text-sm"></i>
                                    </div>
                                    <textarea id="address" name="address" rows="2" placeholder="Home Address"
                                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-gray-600 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none resize-none"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Details Section -->
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                <i class="fa-solid fa-phone-volume"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-gray-100">Contact Details</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-sm font-semibold text-slate-700 dark:text-gray-400 mb-2">Student
                                    Phone</label>
                                <div class="relative group">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-gray-500 group-focus-within:text-indigo-500 dark:group-focus-within:text-indigo-400 transition-colors">
                                        <i class="fa-solid fa-mobile-screen text-sm"></i>
                                    </div>
                                    <input type="tel" id="studentPhone" name="student_phone" placeholder="07xxxxxxxx"
                                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-gray-600 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-slate-700 dark:text-gray-400 mb-2 required">Parent
                                    Phone</label>
                                <div class="relative group">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-gray-500 group-focus-within:text-indigo-500 dark:group-focus-within:text-indigo-400 transition-colors">
                                        <i class="fa-solid fa-users text-sm"></i>
                                    </div>
                                    <input type="tel" id="parentPhone" name="parent_phone" placeholder="07xxxxxxxx"
                                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-gray-600 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none"
                                        required>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-semibold text-slate-700 dark:text-gray-400 mb-2 required">Email
                                    Address</label>
                                <div class="relative group">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-gray-500 group-focus-within:text-indigo-500 dark:group-focus-within:text-indigo-400 transition-colors">
                                        <i class="fa-solid fa-envelope text-sm"></i>
                                    </div>
                                    <input type="email" id="email" name="email" placeholder="student@example.com"
                                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-gray-600 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Section -->
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                <i class="fa-solid fa-book-open-reader"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-gray-100">Academic Details</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-gray-400 mb-2">Reg
                                    No</label>
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-indigo-500 dark:text-indigo-400">
                                        <i class="fa-solid fa-id-card text-sm"></i>
                                    </div>
                                    <input type="text" id="regNumber" name="reg_number"
                                        value="ST<?php echo date('Y') . '001'; ?>" readonly
                                        class="w-full pl-10 pr-4 py-2.5 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-500/30 text-indigo-700 dark:text-indigo-300 font-bold text-sm rounded-xl cursor-not-allowed">
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-slate-700 dark:text-gray-400 mb-2 required">Stream</label>
                                <div class="relative group">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-gray-500 group-focus-within:text-indigo-500 dark:group-focus-within:text-indigo-400 transition-colors">
                                        <i class="fa-solid fa-layer-group text-sm"></i>
                                    </div>
                                    <select id="stream" name="stream"
                                        class="w-full pl-10 pr-8 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl text-sm text-slate-900 dark:text-white focus:bg-white dark:focus:bg-gray-600 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-500/20 focus:border-indigo-500 transition-all appearance-none outline-none cursor-pointer"
                                        required>
                                        <option value="" selected disabled class="text-slate-400 dark:text-gray-500">
                                            Select Stream</option>
                                        <option value="Bio">Bio Science</option>
                                        <option value="Maths">Phy Science</option>
                                        <option value="Tech">Technology</option>
                                        <option value="Art">Arts</option>
                                        <option value="Commerce">Commerce</option>
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400 dark:text-gray-500">
                                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-slate-700 dark:text-gray-400 mb-2 required">Batch</label>
                                <div class="relative group">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-gray-500 group-focus-within:text-indigo-500 dark:group-focus-within:text-indigo-400 transition-colors">
                                        <i class="fa-solid fa-graduation-cap text-sm"></i>
                                    </div>
                                    <select id="batch" name="batch"
                                        class="w-full pl-10 pr-8 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl text-sm text-slate-900 dark:text-white focus:bg-white dark:focus:bg-gray-600 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-500/20 focus:border-indigo-500 transition-all appearance-none outline-none cursor-pointer"
                                        required>
                                        <option value="" selected disabled class="text-slate-400 dark:text-gray-500">
                                            Select Year</option>
                                        <option value="2025">2025</option>
                                        <option value="2026">2026</option>
                                        <option value="2027">2027</option>
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400 dark:text-gray-500">
                                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Photo Upload -->
                    <div class="mb-10">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                <i class="fa-solid fa-camera"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-gray-100">Student Photo</h3>
                        </div>
                        <div
                            class="flex items-center gap-6 bg-slate-50 dark:bg-gray-700/50 p-6 rounded-2xl border border-dashed border-slate-300 dark:border-gray-600">
                            <div class="w-20 h-20 rounded-full border-4 border-white dark:border-gray-600 shadow-md overflow-hidden bg-slate-200 dark:bg-gray-600 flex-shrink-0 relative"
                                id="photoPreview">
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fa-solid fa-user text-3xl text-slate-400 dark:text-gray-400"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <label for="photo"
                                    class="cursor-pointer text-sm bg-white dark:bg-gray-700 border border-slate-200 dark:border-gray-600 text-slate-700 dark:text-gray-300 font-medium py-2 px-4 rounded-xl shadow-sm hover:bg-slate-50 dark:hover:bg-gray-600 hover:border-slate-300 dark:hover:border-gray-500 hover:text-indigo-600 dark:hover:text-white transition-all inline-flex items-center gap-2 group">
                                    <i
                                        class="fa-solid fa-cloud-arrow-up group-hover:-translate-y-0.5 transition-transform"></i>
                                    <span>Upload Image</span>
                                </label>
                                <input type="file" id="photo" name="photo" accept="image/*" class="hidden"
                                    onchange="previewPhoto(event)">
                                <p class="text-xs text-slate-500 dark:text-gray-400 mt-2">Recommended: Square JPG/PNG,
                                    Max 2MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex gap-4 pt-6 border-t border-slate-100 dark:border-gray-700">
                        <a href="login.php"
                            class="w-1/3 flex items-center justify-center gap-2 py-3.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 text-slate-600 dark:text-gray-300 text-sm font-bold rounded-xl hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-slate-800 dark:hover:text-white hover:border-slate-300 dark:hover:border-gray-600 transition-all shadow-sm">
                            <i class="fa-solid fa-arrow-left"></i>
                            Back
                        </a>
                        <button type="submit"
                            class="w-2/3 flex items-center justify-center gap-2 py-3.5 text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-sm font-bold rounded-xl shadow-lg shadow-indigo-500/30 transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                            <i class="fa-solid fa-check-circle"></i>
                            Register Student
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <div class="text-center mt-8">
            <p class="text-slate-400 dark:text-gray-500 text-xs font-medium">&copy; <?php echo date('Y'); ?> Future
                Minds A/L Institute. All rights reserved.</p>
        </div>
    </div>

    <script>
        function previewPhoto(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('photoPreview');
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Photo" class="w-full h-full object-cover">`;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = `<div class="w-full h-full flex items-center justify-center"><i class="fa-solid fa-user text-3xl text-slate-400"></i></div>`;
            }
        }
    </script>
</body>

</html>