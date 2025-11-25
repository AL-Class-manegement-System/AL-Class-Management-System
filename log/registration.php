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
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        dark: {
                            900: '#111827', // Body bg
                            800: '#1F2937', // Card bg
                            700: '#374151', // Input bg
                            600: '#4B5563', // Borders
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Dark Theme Background */
        body { background-color: #111827; }
        .required::after { content: " *"; color: #F87171; } /* Lighter red for dark mode */
        
        /* Custom Scrollbar for Dark Mode */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #1F2937; }
        ::-webkit-scrollbar-thumb { background: #4B5563; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #6B7280; }

        /* Date picker icon invert for dark mode */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
    </style>
</head>
<body class="antialiased text-gray-300 py-6 px-4">

    <div class="max-w-3xl mx-auto">
        
        <div class="bg-gray-800 rounded-xl shadow-2xl overflow-hidden border border-gray-700 relative">
            
            <div class="bg-gradient-to-r from-indigo-900 to-blue-900 p-6 text-center relative overflow-hidden border-b border-gray-700">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-24 h-24 rounded-full bg-white opacity-5 blur-xl pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 rounded-full bg-white opacity-5 blur-xl pointer-events-none"></div>
                
                <div class="relative z-10 flex items-center justify-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/20 shadow-lg">
                        <i class="fa-solid fa-user-graduate text-lg text-indigo-300"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white tracking-tight">Student Registration</h2>
                </div>
            </div>

            <div class="p-6">
                <form id="studentForm" method="POST" enctype="multipart/form-data">

                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-4 border-b border-gray-700 pb-2">
                            <i class="fa-solid fa-address-card text-indigo-400"></i>
                            <h3 class="text-lg font-bold text-gray-100">Personal Details</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-400 mb-1 required">Full Name</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                                        <i class="fa-solid fa-user text-sm"></i>
                                    </div>
                                    <input type="text" id="fullName" name="full_name" placeholder="Enter full name" 
                                        class="w-full pl-9 pr-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white placeholder-gray-400 focus:bg-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 mb-1 required">Date of Birth</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                                        <i class="fa-solid fa-calendar-day text-sm"></i>
                                    </div>
                                    <input type="date" id="dob" name="date_of_birth" 
                                        class="w-full pl-9 pr-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:bg-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 mb-1 required">Gender</label>
                                <div class="flex items-center space-x-4 bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 h-[38px]">
                                    <label class="flex items-center cursor-pointer hover:text-indigo-400 transition-colors">
                                        <input type="radio" name="gender" value="Male" class="w-3.5 h-3.5 text-indigo-500 border-gray-500 focus:ring-indigo-500 bg-gray-600" required>
                                        <span class="ml-2 text-xs font-medium text-gray-300">Male</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer hover:text-indigo-400 transition-colors">
                                        <input type="radio" name="gender" value="Female" class="w-3.5 h-3.5 text-indigo-500 border-gray-500 focus:ring-indigo-500 bg-gray-600">
                                        <span class="ml-2 text-xs font-medium text-gray-300">Female</span>
                                    </label>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-400 mb-1">Current School</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                                        <i class="fa-solid fa-school text-sm"></i>
                                    </div>
                                    <input type="text" id="school" name="school" placeholder="School Name" 
                                        class="w-full pl-9 pr-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white placeholder-gray-400 focus:bg-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none">
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-400 mb-1">Address</label>
                                <div class="relative group">
                                    <div class="absolute top-2.5 left-3 flex items-center pointer-events-none text-gray-500">
                                        <i class="fa-solid fa-location-dot text-sm"></i>
                                    </div>
                                    <textarea id="address" name="address" rows="1" placeholder="Home Address" 
                                        class="w-full pl-9 pr-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white placeholder-gray-400 focus:bg-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-4 border-b border-gray-700 pb-2">
                            <i class="fa-solid fa-phone-volume text-indigo-400"></i>
                            <h3 class="text-lg font-bold text-gray-100">Contact</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-400 mb-1">Student Phone</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                                        <i class="fa-solid fa-mobile-screen text-sm"></i>
                                    </div>
                                    <input type="tel" id="studentPhone" name="student_phone" placeholder="07xxxxxxxx" 
                                        class="w-full pl-9 pr-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white placeholder-gray-400 focus:bg-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 mb-1 required">Parent Phone</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                                        <i class="fa-solid fa-users text-sm"></i>
                                    </div>
                                    <input type="tel" id="parentPhone" name="parent_phone" placeholder="07xxxxxxxx" 
                                        class="w-full pl-9 pr-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white placeholder-gray-400 focus:bg-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-4 border-b border-gray-700 pb-2">
                            <i class="fa-solid fa-book-open-reader text-indigo-400"></i>
                            <h3 class="text-lg font-bold text-gray-100">Academic</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-400 mb-1">Reg No</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-indigo-400">
                                        <i class="fa-solid fa-id-card text-sm"></i>
                                    </div>
                                    <input type="text" id="regNumber" name="reg_number" 
                                        value="ST<?php echo date('Y') . '001'; ?>" readonly
                                        class="w-full pl-9 pr-3 py-2 bg-indigo-900/20 border border-indigo-800 text-indigo-300 font-bold text-sm rounded-lg cursor-not-allowed">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 mb-1 required">Stream</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                                        <i class="fa-solid fa-layer-group text-sm"></i>
                                    </div>
                                    <select id="stream" name="stream" class="w-full pl-9 pr-8 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:bg-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 appearance-none outline-none cursor-pointer" required>
                                        <option value="" selected disabled class="bg-gray-800 text-gray-400">Select</option>
                                        <option value="Bio" class="bg-gray-800">Bio Science</option>
                                        <option value="Maths" class="bg-gray-800">Phy Science</option>
                                        <option value="Tech" class="bg-gray-800">Technology</option>
                                        <option value="Art" class="bg-gray-800">Arts</option>
                                        <option value="Commerce" class="bg-gray-800">Commerce</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-500">
                                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 mb-1 required">Batch</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                                        <i class="fa-solid fa-graduation-cap text-sm"></i>
                                    </div>
                                    <select id="batch" name="batch" class="w-full pl-9 pr-8 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm text-white focus:bg-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 appearance-none outline-none cursor-pointer" required>
                                        <option value="" selected disabled class="bg-gray-800 text-gray-400">Year</option>
                                        <option value="2025" class="bg-gray-800">2025</option>
                                        <option value="2026" class="bg-gray-800">2026</option>
                                        <option value="2027" class="bg-gray-800">2027</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-500">
                                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                         <div class="flex items-center gap-2 mb-4 border-b border-gray-700 pb-2">
                            <i class="fa-solid fa-camera text-indigo-400"></i>
                            <h3 class="text-lg font-bold text-gray-100">Photo</h3>
                        </div>
                        <div class="flex items-center gap-6 bg-gray-700/30 p-4 rounded-xl border border-dashed border-gray-600">
                            <div class="w-16 h-16 rounded-full border-2 border-gray-500 shadow-lg overflow-hidden bg-gray-600 flex-shrink-0" id="photoPreview">
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fa-solid fa-user text-2xl text-gray-400"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <label for="photo" class="cursor-pointer text-sm bg-gray-600 border border-gray-500 text-gray-200 font-medium py-1.5 px-3 rounded shadow-sm hover:bg-gray-500 hover:text-white transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-upload"></i> Choose Image
                                </label>
                                <input type="file" id="photo" name="photo" accept="image/*" class="hidden" onchange="previewPhoto(event)">
                                <p class="text-[10px] text-gray-500 mt-1">Max: 2MB (JPG/PNG)</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-gray-700 mt-2">
                        
                        <a href="login.php" class="w-1/3 flex items-center justify-center gap-2 py-3 bg-transparent border border-gray-600 text-gray-300 text-sm font-bold rounded-xl hover:bg-gray-700 hover:text-white hover:border-gray-500 transition-all shadow-sm">
                            <i class="fa-solid fa-arrow-left"></i> 
                            Back
                        </a>

                        <button type="submit" class="w-2/3 flex items-center justify-center gap-2 py-3 text-white bg-indigo-600 hover:bg-indigo-500 text-sm font-bold rounded-xl shadow-lg shadow-indigo-900/50 transition-all transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-check-circle"></i>
                            Register Student
                        </button>
                    </div>

                </form>
            </div>
        </div>
        
        <p class="text-center text-gray-500 text-xs mt-6">&copy; <?php echo date('Y'); ?> Education Management System</p>
    </div>

    <script>
        function previewPhoto(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('photoPreview');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Photo" class="w-full h-full object-cover">`;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = `<div class="w-full h-full flex items-center justify-center"><i class="fa-solid fa-user text-2xl text-gray-400"></i></div>`;
            }
        }
    </script>
</body>
</html>