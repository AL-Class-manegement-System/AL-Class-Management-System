<?php
session_start();

include '../includes/connection.php';


if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $dob = isset($_POST['date_of_birth']) ? trim($_POST['date_of_birth']) : '';
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
    $school = isset($_POST['school']) ? trim($_POST['school']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $student_phone = isset($_POST['student_phone']) ? trim($_POST['student_phone']) : '';
    $parent_phone = isset($_POST['parent_phone']) ? trim($_POST['parent_phone']) : '';
    $stream = isset($_POST['stream']) ? trim($_POST['stream']) : '';
    $batch = isset($_POST['batch']) ? trim($_POST['batch']) : '';


    if (empty($full_name) || empty($dob) || empty($gender) || empty($parent_phone) || empty($stream) || empty($batch)) {
        header("Location: ../log/registration.php?error=" . urlencode('Please fill all required fields'));
        exit();
    }

        // 3. Student Registration Number එක Auto Generate කිරීම (ST + Year + ID)
        // උදාහරණ: ST2025001
        $current_year = date("Y");
        $prefix = "ST{$current_year}";

        // දැනට අන්තිමට තියෙන ID එක ගන්න
        $id_query = "SELECT reg_number FROM students WHERE reg_number LIKE '$prefix%' ORDER BY student_id DESC LIMIT 1";
        $result = $conn->query($id_query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $last_reg = $row['reg_number'];
            // අන්තිම ඉලක්කම් 3 වෙන් කර ගැනීම
            $last_number = intval(substr($last_reg, 6)); 
            $new_number = $last_number + 1;
            // අලුත් නම්බර් එක හැදීම (001, 002 ආදී ලෙස)
            $reg_number = "{$prefix}" . str_pad($new_number, 3, "0", STR_PAD_LEFT);
        } else {
            // මේ අවුරුද්දේ පළමු ශිෂ්‍යයා නම්
            $reg_number = "{$prefix}001"; 
        }

        // 4. Photo Upload කිරීම (Image Handling)
        $photo_name = ""; // ෆොටෝ එකක් නැත්නම් හිස්ව තියන්න

        if (isset($_FILES['photo']) && isset($_FILES['photo']['error']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            // ෆොටෝ සේව් වන තැන (assests/images/students/)
            $target_dir = "../assests/images/students/";

            // ෆෝල්ඩරය නැත්නම් සාදන්න
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_extension = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
            // ෆයිල් එකේ නම Reg No එකට මාරු කිරීම (උදා: ST2025001.jpg)
            $new_filename = "{$reg_number}.{$file_extension}"; 
            $target_file = "{$target_dir}{$new_filename}";

            // Image checks: extension, mime type and size (<= 2MB)
            $allowed_types = ['jpg', 'jpeg', 'png'];
            $imginfo = @getimagesize($_FILES['photo']['tmp_name']);
            $is_valid_image = $imginfo !== false && in_array($imginfo['mime'], ['image/jpeg', 'image/png']) && in_array($file_extension, $allowed_types);

            if (!$is_valid_image) {
                header("Location: ../log/registration.php?error=" . urlencode('Only JPG/JPEG/PNG valid image files are allowed'));
                exit();
            }

            if ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
                header("Location: ../log/registration.php?error=" . urlencode('Image too large (max 2MB)'));
                exit();
            }

            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $photo_name = $new_filename;
            } else {
                header("Location: ../log/registration.php?error=" . urlencode('Image upload failed'));
                exit();
            }
        }

        // 5. QR Code (Optional - දැනට Reg Number එකම දාමු)
        $qr_code = $reg_number; 

        // 6. Database 
       
        $sql = "INSERT INTO students (
                    reg_number, full_name, dob, gender, school, address, 
                    student_phone, parent_phone, stream, batch, photo, qr_code
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $conn->close();
            header("Location: ../log/registration.php?error=" . urlencode($conn->error));
            exit();
        }

        $stmt->bind_param('ssssssssssss', $reg_number, $full_name, $dob, $gender, $school, $address, $student_phone, $parent_phone, $stream, $batch, $photo_name, $qr_code);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: ../log/registration.php?success=Student Registered Successfully! Reg No: {$reg_number}");
            exit();
        } else {
            $stmt->close();
            $conn->close();
            header("Location: ../log/registration.php?error=" . urlencode($stmt->error ?: $conn->error));
            exit();
        }
        // break; // No need for break here since there's no switch

} else {
    // කෙලින්ම මෙම ෆයිල් එකට ආවොත් ආපසු registration එකට යවන්න
    header("Location:../log/registration.php");
    exit();
}

<<<<<<< HEAD
// $conn->close();

?>
=======
$conn->close();
>>>>>>> main
