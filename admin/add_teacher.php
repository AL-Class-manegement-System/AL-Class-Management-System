<?php 
include 'db_con.php'; 

if (isset($_POST['submit'])) {
    
    // 1. Data ලබා ගැනීම
    $name = $_POST['name'];
    $subject = $_POST['subject'];
    $desc = $_POST['desc'];
    
    // ==========================================
    // 2. AUTO GENERATE ID & PASSWORD
    // ==========================================
    
    // A. Teacher ID එක සැකසීම (TC + Year + Serial)
    $current_year = date("Y");
    $prefix = "TC{$current_year}";
    
    // අන්තිම Teacher ID එක ලබා ගැනීම
    $id_query = "SELECT teacher_number FROM teachers WHERE teacher_number LIKE '$prefix%' ORDER BY teacher_id DESC LIMIT 1";
    $id_result = $conn->query($id_query);

    if ($id_result && $id_result->num_rows > 0) {
        $row = $id_result->fetch_assoc();
        $last_id = $row['teacher_number'];
        $last_number = intval(substr($last_id, 6)); 
        $new_number = $last_number + 1;
        $teacher_number = "{$prefix}" . str_pad($new_number, 3, "0", STR_PAD_LEFT);
    } else {
        $teacher_number = "{$prefix}001"; 
    }

    // B. Random Password එකක් සැකසීම
    $auto_password = rand(100000, 999999); 
    
    // ==========================================
    
    // 3. Image Upload Logic
    $target_dir = "../assets/images/teachers/";
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

    $new_image_name = "";
    $uploadOk = 1;

    if (!empty($_FILES["image"]["name"])) {
        $file_ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $allowed_types)) {
            $new_image_name = time() . "_" . uniqid() . "." . $file_ext;
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $new_image_name)) {
                $msg = "Error uploading image.";
                $msg_type = "error";
                $uploadOk = 0;
            }
        } else {
            $msg = "Invalid file type.";
            $msg_type = "error";
            $uploadOk = 0;
        }
    } else {
        $msg = "Please select an image.";
        $msg_type = "error";
        $uploadOk = 0;
    }

    // 4. Database Insert
    if ($uploadOk == 1) {
        $status = 1; 

        $stmt = $conn->prepare("INSERT INTO teachers (teacher_number, password, full_name, subject, description, image, status) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param("ssssssi", $teacher_number, $auto_password, $name, $subject, $desc, $new_image_name, $status);

            if ($stmt->execute()) {
                // සාර්ථක වූ විට ID සහ Password පෙන්වන පණිවිඩය
                $msg = "Teacher Added Successfully! <br> <b>ID: $teacher_number</b> <br> <b>Password: $auto_password</b> <br> (Please write this down)";
                $msg_type = "success";
            } else {
                $msg = "Database Error: " . $stmt->error;
                $msg_type = "error";
            }
            $stmt->close(); 
        } else {
            $msg = "Prepare Error: " . $conn->error;
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <?php include('includes/sidebar.php'); ?>

    <div class="ml-64 flex flex-col min-h-screen">
        
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
                <div class="<?php echo ($msg_type == 'success') ? 'bg-green-100 text-green-700 border-green-400' : 'bg-red-100 text-red-700 border-red-400'; ?> border px-4 py-3 rounded relative mb-6">
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
                                <input type="text" name="name" placeholder="Ex: Mr. Amal Perera" required
                                    class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Teaching Subject</label>
                                <select name="subject" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition cursor-pointer">
                                    <option value="" disabled selected>Select Subject</option>
                                    <option value="Combined Maths">Combined Maths</option>
                                    <option value="Physics">Physics</option>
                                    <option value="Chemistry">Chemistry</option>
                                    <option value="Biology">Biology</option>
                                    <option value="ICT">ICT</option>
                                    <option value="Technology">Technology</option>
                                    <option value="Commerce">Commerce</option>
                                    <option value="Arts">Arts</option>
                                    <option value="English">English</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                                <input type="file" name="image" accept="image/*" required 
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition cursor-pointer bg-gray-50 rounded-lg border border-gray-200"/>
                                <p class="text-xs text-gray-400 mt-1">Recommended: Square image (1:1)</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description / Qualifications</label>
                            <textarea name="desc" placeholder="B.Sc (Hons) University of Colombo..." rows="4" required
                                class="w-full p-4 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"></textarea>
                        </div>

                        <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                            <button type="submit" name="submit" class="px-8 py-3 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition transform hover:-translate-y-0.5">
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