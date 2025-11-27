<?php
// db_con.php ගොනුව නිවැරදි path එකෙන් include කරගන්න
include 'db_con.php'; 

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    
    // Image Upload Logic
    // Folder එක නැත්නම් හදන්න (assets ලෙස නම වෙනස් කළා)
    if (!file_exists('../assets/images/teachers/')) {
        mkdir('../assets/images/teachers/', 0777, true);
    }

    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    
    // Unique name එකක් දෙනවා
    $new_image_name = time() . "_" . basename($image);
    $target = "../assets/images/teachers/" . $new_image_name;

    $sql = "INSERT INTO teachers (full_name, subject, description, image) VALUES ('$name', '$subject', '$desc', '$new_image_name')";

    if (mysqli_query($conn, $sql)) {
        if (move_uploaded_file($image_tmp, $target)) {
            echo "<script>alert('Teacher added successfully'); window.location='teachers.php';</script>";
        } else {
            echo "<script>alert('Teacher added but image upload failed');</script>";
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Teacher - Future Minds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <?php include('includes/sidebar.php'); ?>

    <div class="ml-64 flex flex-col min-h-screen">
        <header class="bg-white shadow-sm py-4 px-8 flex items-center sticky top-0 z-40">
            <h2 class="text-2xl font-bold text-gray-800">Add New Teacher</h2>
        </header>

        <main class="p-8 flex justify-center">
            <div class="w-full max-w-2xl bg-white rounded-xl shadow-lg p-8">
                <form method="POST" action="" enctype="multipart/form-data" class="space-y-6">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teacher Name</label>
                        <input type="text" name="name" placeholder="Ex: Mr. Kamal Perera" required
                            class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                        <select name="subject" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="" disabled selected>Select Subject</option>
                            <option value="Combined Maths">Combined Maths</option>
                            <option value="Physics">Physics</option>
                            <option value="Chemistry">Chemistry</option>
                            <option value="Biology">Biology</option>
                            <option value="ICT">ICT</option>
                            <option value="Technology">Technology</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="desc" placeholder="Short bio about the teacher..." rows="4"
                            class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                        <div class="flex items-center justify-center w-full">
                            <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3"></i>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                    <p class="text-xs text-gray-500">JPG, PNG (MAX. 2MB)</p>
                                </div>
                                <input id="dropzone-file" name="image" type="file" class="hidden" required />
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <a href="teachers.php" class="w-1/3 py-3 text-center rounded-lg border border-gray-300 text-gray-700 font-bold hover:bg-gray-50 transition">Cancel</a>
                        <button type="submit" name="submit" class="w-2/3 py-3 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/30">
                            Save Teacher
                        </button>
                    </div>

                </form>
            </div>
        </main>
    </div>
</body>
</html>