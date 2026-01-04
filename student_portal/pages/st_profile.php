<?php include('../includes/student_header.php'); ?>

<!-- Main Content Wrapper: Height = 100vh - Header(80px/5rem) -->
<!-- Keeps the main outer scroll stuck, and uses internal scroll for content -->
<div class="flex flex-col h-[calc(100vh-5rem)] overflow-hidden">
    
    <!-- Scrollable Content -->
    <div class="flex-1 overflow-y-auto custom-scrollbar">
        <main class="flex-grow p-4 md:p-8 max-w-5xl mx-auto w-full">

            <!-- Header Card -->
            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden mb-8 relative">
                
                <!-- Decorative Background -->
                <div class="h-32 bg-gradient-to-r from-indigo-500 to-purple-600"></div>

                <div class="px-8 pb-8">
                    <div class="relative flex justify-between items-end -mt-12 mb-6">
                        <div class="relative">
                            <img src="<?php echo $profile_pic; ?>"
                                class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-white shadow-md object-cover bg-white">
                            <div class="absolute bottom-2 right-2 w-6 h-6 bg-green-500 border-2 border-white rounded-full" title="Active"></div>
                        </div>
                        
                        <a href="profile_edit.php" class="mb-2">
                            <div class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-lg font-semibold text-sm hover:bg-indigo-100 transition shadow-sm border border-indigo-100">
                                <i class="fas fa-edit mr-1"></i> Edit Profile
                            </div>
                        </a>
                    </div>

                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
                            <?php echo htmlspecialchars($student['full_name']); ?>
                        </h1>
                        <div class="flex items-center gap-3 mt-2 text-slate-500 font-medium">
                            <span class="flex items-center gap-1 bg-slate-100 px-3 py-1 rounded-full text-xs text-slate-600">
                                <i class="fas fa-id-card"></i> <?php echo htmlspecialchars($student['reg_number']); ?>
                            </span>
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-600 text-xs rounded-full font-bold uppercase">
                                <?php echo htmlspecialchars($student['stream']); ?> Stream
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Personal Info -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 h-full">
                    <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center border-b border-slate-100 pb-3">
                        <i class="fas fa-user-circle text-indigo-500 mr-2"></i> Personal Information
                    </h3>

                    <div class="space-y-6">
                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-wide font-bold mb-1">Full Name</p>
                            <p class="text-slate-700 font-medium text-base">
                                <?php echo htmlspecialchars($student['full_name']); ?>
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wide font-bold mb-1">Date of Birth</p>
                                <p class="text-slate-700 font-medium">
                                    <i class="far fa-calendar-alt text-slate-400 mr-1"></i>
                                    <?php echo htmlspecialchars($student['dob']); ?>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wide font-bold mb-1">Gender</p>
                                <p class="text-slate-700 font-medium capitalize">
                                    <?php echo htmlspecialchars($student['gender']); ?>
                                </p>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-wide font-bold mb-1">NIC Number</p>
                            <p class="text-slate-700 font-medium font-mono bg-slate-50 inline-block px-2 py-1 rounded">
                                <?php echo htmlspecialchars($student['nic']); ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-wide font-bold mb-1">Address</p>
                            <p class="text-slate-700 font-medium">
                                <i class="fas fa-map-marker-alt text-slate-400 mr-1"></i>
                                <?php echo htmlspecialchars($student['address']); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    <!-- Academic Details -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center border-b border-slate-100 pb-3">
                            <i class="fas fa-graduation-cap text-green-500 mr-2"></i> Academic Details
                        </h3>
                        <div class="space-y-5">
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wide font-bold mb-1">School</p>
                                <p class="text-slate-700 font-medium">
                                    <?php echo htmlspecialchars($student['school']); ?>
                                </p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-slate-400 uppercase tracking-wide font-bold mb-1">Batch / Year</p>
                                    <p class="text-slate-700 font-medium bg-green-50 text-green-700 inline-block px-3 py-1 rounded-lg">
                                        <?php echo htmlspecialchars($student['batch']); ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 uppercase tracking-wide font-bold mb-1">Status</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1.5"></span> Active Student
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center border-b border-slate-100 pb-3">
                            <i class="fas fa-address-book text-orange-500 mr-2"></i> Contact Details
                        </h3>
                        <div class="space-y-5">
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wide font-bold mb-1">Email Address</p>
                                <p class="text-slate-700 font-medium truncate">
                                    <a href="mailto:<?php echo htmlspecialchars($student['email']); ?>" class="hover:text-indigo-600 transition">
                                        <?php echo htmlspecialchars($student['email']); ?>
                                    </a>
                                </p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-slate-400 uppercase tracking-wide font-bold mb-1">Student Phone</p>
                                    <p class="text-slate-700 font-medium">
                                        <i class="fas fa-phone-alt text-slate-300 text-xs mr-1"></i>
                                        <?php echo htmlspecialchars($student['student_phone']); ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 uppercase tracking-wide font-bold mb-1">Parent Phone</p>
                                    <p class="text-slate-700 font-medium">
                                        <i class="fas fa-phone-alt text-slate-300 text-xs mr-1"></i>
                                        <?php echo htmlspecialchars($student['parent_phone']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <!-- Static Footer -->
    <div class="flex-none bg-white border-t border-gray-200">
        <?php include('../includes/footer.php'); ?>
    </div>
</div>