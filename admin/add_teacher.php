<?php 
// කලින් තිබුන include lines වලට අමතරව connection එක include කරන්න
include('../includes/connection.php'); 
include('../includes/header.php'); 
?>


<?php
// 1. Database Connection එක සම්බන්ධ කරගැනීම
include 'db_con.php'; 

// Form එක Submit කළාම වැඩ කරන කොටස
if (isset($_POST['submit'])) {
    
    // Input Data ලබාගැනීම සහ පිරිසිදු කිරීම (Security)
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    
    // 2. Image Upload Logic එක
    $target_dir = "../assets/images/teachers/";
    
    // ෆෝල්ඩරය නැත්නම් අලුතින් සාදන්න
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // පින්තූරයක් තෝරාගෙන ඇත්නම් පමණක්
    if (!empty($_FILES["image"]["name"])) {
        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $file_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        
        // පින්තූරයට අලුත් නමක් දීම (Timestamp එකක් එකතු කර) - Duplicate වළක්වන්න
        $new_image_name = time() . "_" . uniqid() . "." . $file_ext;
        $target_file = $target_dir . $new_image_name;

        // පින්තූරය upload කිරීම
        if (move_uploaded_file($image_tmp, $target_file)) {
            // පින්තූරය upload වුනා නම් Database එකට දත්ත යවන්න
            $sql = "INSERT INTO teachers (full_name, subject, description, image, status) VALUES ('$name', '$subject', '$desc', '$new_image_name', 1)";
            
            if (mysqli_query($conn, $sql)) {
                $msg = "Teacher added successfully!";
                $msg_type = "success";
            } else {
                $msg = "Database Error: " . mysqli_error($conn);
                $msg_type = "error";
            }
        } else {
            $msg = "Sorry, there was an error uploading the file.";
            $msg_type = "error";
        }
    } else {
        // පින්තූරයක් නැත්නම් නිකන්ම විස්තර ටික දාන්න (Optional)
        $sql = "INSERT INTO teachers (full_name, subject, description, status) VALUES ('$name', '$subject', '$desc', 1)";
        if (mysqli_query($conn, $sql)) {
            $msg = "Teacher added successfully (No Image)!";
            $msg_type = "success";
        } else {
            $msg = "Error: " . mysqli_error($conn);
            $msg_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Teacher - Admin Panel</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <?php include('includes/sidebar.php'); ?>

    <div class="ml-64 flex flex-col min-h-screen transition-all duration-300">
        
        <header class="bg-white shadow-sm border-b border-gray-200 h-20 flex items-center justify-between px-8 sticky top-0 z-30">
            <h2 class="text-2xl font-bold text-gray-800">Add New Teacher</h2>
            <div class="flex items-center gap-4">
                <a href="teachers.php" class="text-gray-500 hover:text-indigo-600 font-medium text-sm flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </header>

        <main class="p-8 flex justify-center">
            <div class="w-full max-w-3xl">

                <?php if(isset($msg)): ?>
                <div class="<?php echo ($msg_type == 'success') ? 'bg-green-100 text-green-700 border-green-400' : 'bg-red-100 text-red-700 border-red-400'; ?> border px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline"><?php echo $msg; ?></span>
                </div>
                <?php endif; ?>

                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-indigo-600 px-8 py-4">
                        <h3 class="text-white font-semibold text-lg">Teacher Details</h3>
                        <p class="text-indigo-200 text-xs">Fill in the information to add a new lecturer</p>
                    </div>
                    
                    <form method="POST" action="" enctype="multipart/form-data" class="p-8 space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <input type="text" name="name" placeholder="Ex: Mr. Amal Perera" required
                                        class="w-full pl-10 pr-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Teaching Subject</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <select name="subject" required class="w-full pl-10 pr-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none">
                                        <option value="" disabled selected>Select Subject</option>
                                        <option value="Combined Maths">Combined Maths</option>
                                        <option value="Physics">Physics</option>
                                        <option value="Chemistry">Chemistry</option>
                                        <option value="Biology">Biology</option>
                                        <option value="ICT">ICT</option>
                                        <option value="Technology">Technology</option>
                                        <option value="Accounting">Accounting</option>
                                        <option value="Business Studies">Business Studies</option>
                                        <option value="Economics">Economics</option>
                                        <option value="Arts">Arts</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 pointer-events-none">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                                <label class="flex flex-col w-full h-[48px] border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center justify-center h-full gap-2">
                                        <i class="fas fa-cloud-upload-alt text-gray-400"></i>
                                        <p class="text-sm text-gray-500" id="file-label">Choose Image</p>
                                    </div>
                                    <input type="file" name="image" class="hidden" accept="image/*" onchange="document.getElementById('file-label').textContent = this.files[0].name" required />
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description / Bio</label>
                            <textarea name="desc" placeholder="Write a short description about the teacher's experience and qualifications..." rows="4"
                                class="w-full p-4 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
                            <button type="reset" class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-600 font-medium hover:bg-gray-50 transition-colors">
                                Reset
                            </button>
                            <button type="submit" name="submit" class="px-8 py-2.5 rounded-lg bg-indigo-600 text-white font-medium shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 hover:shadow-indigo-500/50 transform hover:-translate-y-0.5 transition-all">
                                <i class="fas fa-save mr-2"></i> Save Teacher
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </main>
    </div>

</body>
</html>