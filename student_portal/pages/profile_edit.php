<?php
include('../includes/student_header.php');

?>




<body class="bg-slate-50 font-sans text-slate-800">

    <div class="w-full max-w-3xl mx-auto my-10 bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
        
        <div class="bg-primary px-8 py-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white"><i class="fas fa-user-edit mr-2"></i> Update Profile</h1>
            <a href="profile.php" class="text-indigo-100 hover:text-white transition"><i class="fas fa-times text-xl"></i></a>
        </div>

        <form action="update_profile_process.php" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">

            <div class="flex items-center gap-6 pb-6 border-b border-slate-100">
                <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center border-2 border-dashed border-slate-300 overflow-hidden">
                    <img id="preview" src="#" alt="" class="w-full h-full object-cover hidden">
                    <i id="placeholder" class="fas fa-camera text-slate-400 text-2xl"></i>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Change Profile Photo</label>
                    <input type="file" name="photo" accept="image/*" onchange="previewImage(this)" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                    <p class="text-xs text-slate-400 mt-1">JPG, PNG only. Max 2MB.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Student Phone</label>
                    <input type="text" name="student_phone" value="<?php echo htmlspecialchars($student['student_phone']); ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Parent Phone</label>
                    <input type="text" name="parent_phone" value="<?php echo htmlspecialchars($student['parent_phone']); ?>" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-1">Home Address</label>
                    <textarea name="address" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"><?php echo htmlspecialchars($student['address']); ?></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="profile.php" class="px-6 py-2.5 rounded-lg text-slate-600 font-bold hover:bg-slate-100 transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary text-white font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition transform hover:-translate-y-0.5">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                    document.getElementById('preview').classList.remove('hidden');
                    document.getElementById('placeholder').classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>