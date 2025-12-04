<?php 
include 'db_con.php'; 

// ID එක හරහා Teacher ගේ විස්තර ලබා ගැනීම
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM teachers WHERE teacher_id = $id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $teacher = $result->fetch_assoc();
    } else {
        header("Location: teachers.php");
        exit();
    }
} else {
    header("Location: teachers.php");
    exit();
}

// Update Logic
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $subject = $_POST['subject'];
    $desc = $_POST['desc'];
    
    // Photo Update Logic
    $new_image = $_FILES['image']['name'];
    $old_image = $_POST['old_image'];
    $final_image = $old_image; // Default to old image

    if ($new_image != '') {
        $target_dir = "../assets/images/teachers/";
        $file_ext = strtolower(pathinfo($new_image, PATHINFO_EXTENSION));
        $unique_name = time() . "_" . uniqid() . "." . $file_ext;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $unique_name)) {
            $final_image = $unique_name;
            // පරණ ෆොටෝ එක මැකීම (අවශ්‍ය නම්)
            if (!empty($old_image) && file_exists($target_dir . $old_image)) {
                unlink($target_dir . $old_image);
            }
        }
    }

    // Database Update
    $stmt = $conn->prepare("UPDATE teachers SET full_name=?, subject=?, description=?, image=? WHERE teacher_id=?");
    $stmt->bind_param("ssssi", $name, $subject, $desc, $final_image, $id);

    if ($stmt->execute()) {
        $msg = "Teacher details updated successfully!";
        $msg_type = "success";
        // Update වූ දත්ත නැවත ලබා ගැනීම
        $teacher['full_name'] = $name;
        $teacher['subject'] = $subject;
        $teacher['description'] = $desc;
        $teacher['image'] = $final_image;
    } else {
        $msg = "Error updating details: " . $conn->error;
        $msg_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Teacher - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <?php include('includes/sidebar.php'); ?>

    <div class="ml-64 flex flex-col min-h-screen">
        
        <header class="bg-white shadow-sm border-b border-gray-200 h-20 flex items-center justify-between px-8 sticky top-0 z-30">
            <h2 class="text-2xl font-bold text-gray-800">Edit Teacher</h2>
            <a href="teachers.php" class="text-gray-500 hover:text-indigo-600 font-medium text-sm flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </header>

        <main class="p-8 flex justify-center">
            <div class="w-full max-w-3xl">

                <?php if(isset($msg)): ?>
                <div class="<?php echo ($msg_type == 'success') ? 'bg-green-100 text-green-700 border-green-400' : 'bg-red-100 text-red-700 border-red-400'; ?> border px-4 py-3 rounded relative mb-6">
                    <span class="block sm:inline"><?php echo $msg; ?></span>
                </div>
                <?php endif; ?>

                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-indigo-600 px-8 py-4 flex justify-between items-center">
                        <h3 class="text-white font-semibold text-lg">Update Information</h3>
                        <span class="bg-indigo-500 text-indigo-100 text-xs px-2 py-1 rounded">ID: <?php echo $teacher['teacher_number']; ?></span>
                    </div>
                    
                    <form method="POST" action="" enctype="multipart/form-data" class="p-8 space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($teacher['full_name']); ?>" required
                                    class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Teaching Subject</label>
                                <select name="subject" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition cursor-pointer">
                                    <?php 
                                    $subjects = ["Combined Maths", "Physics", "Chemistry", "Biology", "ICT", "Technology", "Commerce", "Arts", "English"];
                                    foreach($subjects as $sub) {
                                        $selected = ($teacher['subject'] == $sub) ? 'selected' : '';
                                        echo "<option value='$sub' $selected>$sub</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                                <div class="flex items-center gap-4">
                                    <?php 
                                    $imgShow = (!empty($teacher['image'])) ? "../assets/images/teachers/".$teacher['image'] : "https://ui-avatars.com/api/?name=".$teacher['full_name']; 
                                    ?>
                                    <img src="<?php echo $imgShow; ?>" class="w-12 h-12 rounded-full object-cover border">
                                    <input type="file" name="image" accept="image/*" 
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition"/>
                                </div>
                                <input type="hidden" name="old_image" value="<?php echo $teacher['image']; ?>">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="desc" rows="4" required
                                class="w-full p-4 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"><?php echo htmlspecialchars($teacher['description']); ?></textarea>
                        </div>

                        <div class="flex items-center justify-end pt-4 border-t border-gray-100 gap-3">
                            <a href="teachers.php" class="px-6 py-3 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-gray-300 transition">Cancel</a>
                            <button type="submit" name="update" class="px-8 py-3 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition transform hover:-translate-y-0.5">
                                <i class="fas fa-check-circle mr-2"></i> Update Teacher
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </main>
    </div>

</body>
</html>