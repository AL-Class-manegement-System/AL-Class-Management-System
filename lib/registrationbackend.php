<?php
session_start();
require_once '../includes/connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. Data ලබා ගැනීම සහ පිරිසිදු කිරීම
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $nic = isset($_POST['nic']) ? trim($_POST['nic']) : '';
    $dob = isset($_POST['date_of_birth']) ? trim($_POST['date_of_birth']) : '';
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
    $school = isset($_POST['school']) ? trim($_POST['school']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $student_phone = isset($_POST['student_phone']) ? trim($_POST['student_phone']) : '';
    $parent_phone = isset($_POST['parent_phone']) ? trim($_POST['parent_phone']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $stream = isset($_POST['stream']) ? trim($_POST['stream']) : '';
    $batch = isset($_POST['batch']) ? trim($_POST['batch']) : '';

    // 2. Validation (NIC සහ Email අනිවාර්ය කර ඇත)
    if (empty($full_name) || empty($nic) || empty($dob) || empty($gender) || empty($parent_phone) || empty($email) || empty($stream) || empty($batch)) {
        header("Location: ../log/registration.php?error=" . urlencode('Please fill all required fields'));
        exit();
    }

    // 3. Student Registration Number Auto Generate කිරීම
    $current_year = date("Y");
    $prefix = "ST{$current_year}";

    $id_query = "SELECT reg_number FROM students WHERE reg_number LIKE '$prefix%' ORDER BY student_id DESC LIMIT 1";
    $result = $conn->query($id_query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_reg = $row['reg_number'];
        $last_number = intval(substr($last_reg, 6)); 
        $new_number = $last_number + 1;
        $reg_number = "{$prefix}" . str_pad($new_number, 3, "0", STR_PAD_LEFT);
    } else {
        $reg_number = "{$prefix}001"; 
    }

    // 4. Photo Upload කිරීම
    $photo_name = ""; 

    if (isset($_FILES['photo']) && isset($_FILES['photo']['error']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        
        // Corrected spelling: 'assets' instead of 'assests'
        $target_dir = "../assets/images/students/";

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
        $new_filename = "{$reg_number}.{$file_extension}"; 
        $target_file = "{$target_dir}{$new_filename}";

        $allowed_types = ['jpg', 'jpeg', 'png'];
        $imginfo = @getimagesize($_FILES['photo']['tmp_name']);
        
        if ($imginfo !== false && in_array($imginfo['mime'], ['image/jpeg', 'image/png']) && in_array($file_extension, $allowed_types)) {
             if ($_FILES['photo']['size'] <= 2 * 1024 * 1024) {
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                    $photo_name = $new_filename;
                } else {
                    header("Location: ../log/registration.php?error=" . urlencode('Image upload failed'));
                    exit();
                }
             } else {
                header("Location: ../log/registration.php?error=" . urlencode('Image too large (max 2MB)'));
                exit();
             }
        } else {
            header("Location: ../log/registration.php?error=" . urlencode('Only JPG/JPEG/PNG valid image files are allowed'));
            exit();
        }
    }

    // 5. Database Insertion (Removed qr_code to match DB)
    $sql = "INSERT INTO students (
                reg_number, full_name, nic, dob, gender, school, address, 
                student_phone, parent_phone, email, stream, batch, photo
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";
            
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        $conn->close();
        header("Location: ../log/registration.php?error=" . urlencode("Database Error: " . $conn->error));
        exit();
    }

    // s = string (13 parameters)
    $stmt->bind_param('sssssssssssss', $reg_number, $full_name, $nic, $dob, $gender, $school, $address, $student_phone, $parent_phone, $email, $stream, $batch, $photo_name);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: ../log/registration.php?success=Student Registered Successfully! Reg No: {$reg_number}");
        exit();
    } else {
        $error_msg = $stmt->error;
        $stmt->close();
        $conn->close();
        // Check for duplicate entry error
        if (strpos($error_msg, 'Duplicate entry') !== false) {
             header("Location: ../log/registration.php?error=" . urlencode("Registration Number or NIC already exists!"));
        } else {
             header("Location: ../log/registration.php?error=" . urlencode("Save Failed: " . $error_msg));
        }
        exit();
    }

} else {
    header("Location: ../log/registration.php");
    exit();
}
?>