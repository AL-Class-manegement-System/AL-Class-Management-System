<?php include('../includes/student_header.php') ?>



<body class="bg-slate-50 font-sans text-slate-800">

    <div class="flex  overflow-hidden p-4 mb-8">

        <div class="flex-1 flex flex-col h-screen overflow-y-auto">

            <main class="p-4 md:p-8 max-w-5xl mx-auto w-full">

                <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden mb-8">

                    <div class="p-8">

                        <div class="flex justify-between items-start mb-6">

                            <div class="relative">
                                <img src="<?php echo $profile_pic; ?>"
                                    class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-slate-50 shadow-md object-cover bg-white">
                                <div
                                    class="absolute bottom-2 right-2 w-6 h-6 bg-green-500 border-2 border-white rounded-full">
                                </div>
                            </div>
                            <a href="../../student_portal/pages/profile_edit.php">
                                <div
                                    class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-lg font-semibold text-sm hover:bg-indigo-100 transition mt-2">
                                    <i class="fas fa-edit mr-1"></i> Edit Profile
                                </div>
                            </a>

                        </div>

                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
                                <?php echo htmlspecialchars($student['full_name']); ?>
                            </h1>
                            <p class="text-slate-500 font-medium flex items-center gap-2 mt-2">
                                <i class="fas fa-id-card"></i> <?php echo htmlspecialchars($student['reg_number']); ?>
                                <span
                                    class="px-2 py-0.5 bg-blue-100 text-blue-600 text-xs rounded-full font-bold uppercase">
                                    <?php echo htmlspecialchars($student['stream']); ?> Stream
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center border-b pb-3">
                            <i class="fas fa-user-circle text-primary mr-2"></i> Personal Information
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">Full Name</p>
                                <p class="text-slate-700 font-medium">
                                    <?php echo htmlspecialchars($student['full_name']); ?>
                                </p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">Date of
                                        Birth</p>
                                    <p class="text-slate-700 font-medium">
                                        <?php echo htmlspecialchars($student['dob']); ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">Gender</p>
                                    <p class="text-slate-700 font-medium capitalize">
                                        <?php echo htmlspecialchars($student['gender']); ?>
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">NIC Number</p>
                                <p class="text-slate-700 font-medium"><?php echo htmlspecialchars($student['nic']); ?>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">Address</p>
                                <p class="text-slate-700 font-medium">
                                    <?php echo htmlspecialchars($student['address']); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center border-b pb-3">
                                <i class="fas fa-graduation-cap text-green-500 mr-2"></i> Academic Details
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">School</p>
                                    <p class="text-slate-700 font-medium">
                                        <?php echo htmlspecialchars($student['school']); ?>
                                    </p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">Batch /
                                            Year</p>
                                        <p class="text-slate-700 font-medium">
                                            <?php echo htmlspecialchars($student['batch']); ?>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">Status
                                        </p>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active Student
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center border-b pb-3">
                                <i class="fas fa-address-book text-orange-500 mr-2"></i> Contact Details
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">Email
                                        Address</p>
                                    <p class="text-slate-700 font-medium">
                                        <?php echo htmlspecialchars($student['email']); ?>
                                    </p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">Student
                                            Phone</p>
                                        <p class="text-slate-700 font-medium">
                                            <?php echo htmlspecialchars($student['student_phone']); ?>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">Parent
                                            Phone</p>
                                        <p class="text-slate-700 font-medium">
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
    </div>
</body>



<?php include('../includes/footer.php') ?>