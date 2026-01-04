<?php
include('../includes/student_header.php');

// Handle Form Submission
$success_msg = "";
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_phone = $_POST['student_phone'] ?? '';
    $parent_phone = $_POST['parent_phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $school = $_POST['school'] ?? '';
    
    // File Upload Logic
    $new_photo_name = $student['photo']; // Default to existing
    
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $file_tmp = $_FILES['profile_pic']['tmp_name'];
        $file_name = $_FILES['profile_pic']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_ext, $allowed_exts)) {
            // Generate unique name: reg_number_timestamp.ext
            $new_file_name = $student['reg_number'] . '_' . time() . '.' . $file_ext;
            $upload_dir = '../../assets/images/students/';
            
            // Create directory if not exists
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                $new_photo_name = $new_file_name;
            } else {
                $error_msg = "Failed to upload image.";
            }
        } else {
            $error_msg = "Invalid file type. Only JPG, PNG, WEBP allowed.";
        }
    }

    if (empty($error_msg)) {
        // Update Database
        $update_sql = "UPDATE students SET student_phone = ?, parent_phone = ?, address = ?, school = ?, photo = ? WHERE reg_number = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("ssssss", $student_phone, $parent_phone, $address, $school, $new_photo_name, $student_id);
        
        if ($stmt_update->execute()) {
            $success_msg = "Profile updated successfully!";
            // Refresh student data to show changes immediately (though header re-fetches on reload, we might want to update local $student var for this view)
            $student['student_phone'] = $student_phone;
            $student['parent_phone'] = $parent_phone;
            $student['address'] = $address;
            $student['school'] = $school;
            $student['photo'] = $new_photo_name;
            
            // Update Profile Pic URL for display
            $image_path = "../../assets/images/students/" . $new_photo_name;
            if (!empty($new_photo_name) && file_exists($image_path)) {
                $profile_pic = $image_path;
            }
            
        } else {
            $error_msg = "Database error: " . $conn->error;
        }
        $stmt_update->close();
    }
}
?>

<!-- Main Content Wrapper: Height = 100vh less header. Fixed Footer area. -->
<div class="flex flex-col h-[calc(100vh-5rem)] overflow-hidden">
    
    <!-- Scrollable Form Area -->
    <div class="flex-1 overflow-y-auto custom-scrollbar">
        <main class="p-4 md:p-8 max-w-4xl mx-auto w-full">

            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Edit Profile</h1>
                    <p class="text-slate-500 mt-1">Update your personal and contact information.</p>
                </div>
                <a href="st_profile.php" class="text-slate-500 hover:text-indigo-600 transition flex items-center gap-2 font-medium">
                    <i class="fas fa-arrow-left"></i> Back to Profile
                </a>
            </div>

            <?php if ($success_msg): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg shadow-sm flex items-start gap-3">
                <i class="fas fa-check-circle mt-1"></i>
                <div>
                    <p class="font-bold">Success</p>
                    <p><?php echo $success_msg; ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($error_msg): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg shadow-sm flex items-start gap-3">
                <i class="fas fa-exclamation-circle mt-1"></i>
                <div>
                    <p class="font-bold">Error</p>
                    <p><?php echo $error_msg; ?></p>
                </div>
            </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                    <!-- Left Column: Profile Picture -->
                    <div class="md:col-span-1">
                        <div class="bg-white rounded-2xl shadow-sm p-6 text-center border border-slate-100 sticky top-0">
                            <div class="relative w-40 h-40 mx-auto mb-6 group">
                                <img src="<?php echo $profile_pic; ?>" alt="Profile" id="preview-img"
                                    class="w-full h-full rounded-full object-cover border-4 border-white shadow-lg bg-slate-100">

                                <label for="profile_pic_input"
                                    class="absolute inset-0 bg-slate-900/50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 cursor-pointer backdrop-blur-sm">
                                    <div class="text-white flex flex-col items-center">
                                        <i class="fas fa-camera text-2xl mb-1"></i>
                                        <span class="text-xs font-semibold">Change Photo</span>
                                    </div>
                                </label>
                                <input type="file" id="profile_pic_input" name="profile_pic" class="hidden" accept="image/*" onchange="previewImage(this)">
                            </div>

                            <h2 class="text-xl font-bold text-slate-800"><?php echo htmlspecialchars($student['full_name']); ?></h2>
                            <p class="text-sm text-slate-500 mb-4"><?php echo htmlspecialchars($student['reg_number']); ?></p>

                            <div class="border-t border-slate-100 pt-4 text-left">
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2">Instructions</p>
                                <ul class="text-xs text-slate-500 space-y-1 list-disc list-inside">
                                    <li>Use a clear, square image.</li>
                                    <li>Max size 5MB.</li>
                                    <li>Formats: JPG, PNG.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Form Fields -->
                    <div class="md:col-span-2 space-y-6">

                        <!-- Personal Info (Read-Only) -->
                        <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100">
                            <h3 class="text-lg font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100 flex items-center">
                                 <i class="fas fa-lock text-slate-300 mr-2 text-sm"></i> Non-Editable Information
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Full Name</label>
                                    <input type="text" value="<?php echo htmlspecialchars($student['full_name']); ?>"
                                        class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-500 cursor-not-allowed"
                                        readonly>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">NIC Number</label>
                                    <input type="text" value="<?php echo htmlspecialchars($student['nic']); ?>"
                                        class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-500 cursor-not-allowed"
                                        readonly>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Date of Birth</label>
                                    <input type="text" value="<?php echo htmlspecialchars($student['dob']); ?>"
                                        class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-500 cursor-not-allowed"
                                        readonly>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Stream / Batch</label>
                                    <input type="text" value="<?php echo htmlspecialchars($student['stream'] . ' - ' . $student['batch']); ?>"
                                        class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-500 cursor-not-allowed"
                                        readonly>
                                </div>
                            </div>
                            <p class="text-xs text-slate-400 mt-3 italic">* Contact administration to update these details.</p>
                        </div>

                        <!-- Editable Info -->
                        <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100">
                            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center border-b border-slate-100 pb-3">
                                <i class="fas fa-edit text-indigo-500 mr-2"></i> Editable Information
                            </h3>

                            <div class="space-y-4">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Student Phone</label>
                                        <input type="text" name="student_phone" value="<?php echo htmlspecialchars($student['student_phone']); ?>"
                                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                                            placeholder="07xxxxxxxx">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Parent Phone</label>
                                        <input type="text" name="parent_phone" value="<?php echo htmlspecialchars($student['parent_phone']); ?>"
                                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                                            placeholder="07xxxxxxxx">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">School</label>
                                    <input type="text" name="school" value="<?php echo htmlspecialchars($student['school']); ?>"
                                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                                        placeholder="Enter current school">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Address</label>
                                    <textarea name="address" rows="3"
                                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition resize-none"
                                        placeholder="Enter your address"><?php echo htmlspecialchars($student['address']); ?></textarea>
                                </div>
                                
                                <!-- Email Readonly (Usually unique ID) -->
                                 <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email Address</label>
                                    <input type="email" value="<?php echo htmlspecialchars($student['email']); ?>" readonly
                                        class="w-full px-4 py-2 border border-slate-200 bg-slate-50 text-slate-600 rounded-lg outline-none cursor-not-allowed">
                                </div>

                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-2">
                            <a href="st_profile.php" class="px-6 py-2.5 rounded-xl text-slate-600 font-bold hover:bg-slate-100 transition">Cancel</a>
                            <button type="submit"
                                class="px-8 py-2.5 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 shadow-md hover:shadow-lg shadow-indigo-500/30 transition transform hover:-translate-y-0.5">
                                Save Changes
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </main>
    </div>

    <!-- Static Footer -->
    <div class="flex-none bg-white border-t border-gray-200">
        <?php include('../includes/footer.php'); ?>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>