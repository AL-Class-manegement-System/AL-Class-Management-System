<?php
session_start();
require_once '../includes/connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ==========================================
    // 1. Data ලබා ගැනීම සහ පිරිසිදු කිරීම
    // ==========================================
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

    // ==========================================
    // 2. VALIDATION SECTION
    // ==========================================

    // A. Required Fields Check
    if (empty($full_name) || empty($nic) || empty($dob) || empty($gender) || empty($parent_phone) || empty($email) || empty($stream) || empty($batch)) {
        header("Location: ../log/registration.php?error=" . urlencode('Please fill all required fields'));
        exit();
    }

    // B. NIC Validation
    if (!preg_match('/^([0-9]{9}[vVxX]|[0-9]{12})$/', $nic)) {
        header("Location: ../log/registration.php?error=" . urlencode('Invalid NIC Format (Ex: 123456789V or 200012345678)'));
        exit();
    }

    // C. Parent Phone Validation
    if (!preg_match('/^[0-9]{10}$/', $parent_phone)) {
        header("Location: ../log/registration.php?error=" . urlencode('Invalid Parent Phone Number (Must be 10 digits)'));
        exit();
    }

    // D. Student Phone Validation
    if (!empty($student_phone) && !preg_match('/^[0-9]{10}$/', $student_phone)) {
        header("Location: ../log/registration.php?error=" . urlencode('Invalid Student Phone Number'));
        exit();
    }

    // E. Email Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../log/registration.php?error=" . urlencode('Invalid Email Address'));
        exit();
    }

    // ==========================================
    // 3. ID GENERATE
    // ==========================================

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

    // ==========================================
    // 4. PHOTO HANDLING (BLOB UPDATE)
    // ==========================================
    
    $imageData = null; // ෆොටෝ එකේ Data තියාගන්න variable එක

    if (isset($_FILES['photo']) && isset($_FILES['photo']['error']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        
        // Image එකක්දැයි පරීක්ෂා කිරීම
        $imginfo = @getimagesize($_FILES['photo']['tmp_name']);
        
        if ($imginfo !== false) {
             // File Size Check (Max 2MB)
             if ($_FILES['photo']['size'] <= 2 * 1024 * 1024) {
                // Folder එකකට නොදා, කෙලින්ම Data කියවා ගන්නවා
                $imageData = file_get_contents($_FILES['photo']['tmp_name']);
             } else {
                header("Location: ../log/registration.php?error=" . urlencode('Image too large (max 2MB)'));
                exit();
             }
        } else {
            header("Location: ../log/registration.php?error=" . urlencode('Invalid image file'));
            exit();
        }
    }

    // ==========================================
    // 5. DATABASE INSERTION
    // ==========================================
    
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

    // 'null' භාවිතා කරන්නේ අපි BLOB එක වෙනම send_long_data වලින් යවන නිසා
    $null = NULL;

    // Bind parameters: 12 strings ('s') and 1 blob ('b')
    $stmt->bind_param('ssssssssssssb', 
        $reg_number, $full_name, $nic, $dob, $gender, $school, $address, 
        $student_phone, $parent_phone, $email, $stream, $batch, $null
    );

    // Photo Data යැවීම (Index 12 යනු 13 වන ස්ථානයයි - Photo Column එක)
    if ($imageData !== null) {
        $stmt->send_long_data(12, $imageData);
    }

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        // Success Message
        header("Location: ../log/registration.php?success=Student Registered Successfully! Reg No: {$reg_number}");
        exit();
    } else {
        $error_msg = $stmt->error;
        $stmt->close();
        $conn->close();
        
        // Duplicate Check
        if (strpos($error_msg, 'Duplicate entry') !== false) {
             header("Location: ../log/registration.php?error=" . urlencode("Error: Registration Number or NIC already exists!"));
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