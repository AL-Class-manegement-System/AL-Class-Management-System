<?php
// student_portal/pages/upload_slip_process.php
session_start();
include('../../includes/connection.php');

// 1. Session & Login Check (ශිෂ්‍යයා ලොග් වී ඇත්දැයි බැලීම)
if (!isset($_SESSION['student_id'])) {
    header("Location: ../../log/login.php");
    exit();
}

// 2. Request Method Check (POST හරහා පැමිණි ඉල්ලීමක් දැයි බැලීම)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: my_classes.php");
    exit();
}

// =======================================================================
// වැදගත්: Session එකෙන් එන Reg Number එකෙන් Database ID එක සොයා ගැනීම
// =======================================================================
// Session එකේ ඇත්තේ STxxxx වැනි අංකයකි (String), නමුත් Payments table එකට ඕනේ ID එක (Integer)
$student_reg_no = $_SESSION['student_id']; 

$st_sql = "SELECT student_id FROM students WHERE reg_number = ?";
$st_stmt = $conn->prepare($st_sql);
$st_stmt->bind_param("s", $student_reg_no);
$st_stmt->execute();
$st_res = $st_stmt->get_result();

if ($st_res->num_rows > 0) {
    $row = $st_res->fetch_assoc();
    $student_db_id = $row['student_id']; // මෙය තමයි Integer ID එක
} else {
    echo "<script>alert('Error: Student profile not found.'); window.history.back();</script>";
    exit();
}
$st_stmt->close();

// 3. Class ID Check
$class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
if ($class_id == 0) {
    echo "<script>alert('Invalid Class ID.'); window.location.href='my_classes.php';</script>";
    exit();
}

// 4. Class Fee Check (ගාස්තුව ලබා ගැනීම)
$class_sql = "SELECT fee FROM classes WHERE class_id = ?";
$stmt = $conn->prepare($class_sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$class_res = $stmt->get_result();

if ($class_res->num_rows == 0) {
    echo "<script>alert('Class not found.'); window.location.href='my_classes.php';</script>";
    exit();
}

$class_data = $class_res->fetch_assoc();
$amount = $class_data['fee'];
$month = date('F'); // වත්මන් මාසය
$year = date('Y');

// 5. File Upload Logic
if (isset($_FILES['slip_file']) && $_FILES['slip_file']['error'] === UPLOAD_ERR_OK) {
    
    $file_tmp = $_FILES['slip_file']['tmp_name'];
    $file_name = $_FILES['slip_file']['name'];
    $file_size = $_FILES['slip_file']['size'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Allowed Extensions
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

    // ගොනු වර්ගය පරීක්ෂා කිරීම
    if (!in_array($file_ext, $allowed)) {
        echo "<script>alert('Error: Only JPG, PNG, or PDF files are allowed.'); window.history.back();</script>";
        exit();
    }

    // ගොනු ප්‍රමාණය පරීක්ෂා කිරීම (Max 5MB)
    if ($file_size > 5 * 1024 * 1024) {
        echo "<script>alert('Error: File size must be less than 5MB.'); window.history.back();</script>";
        exit();
    }

    // Unique Filename එකක් සෑදීම
    $new_filename = "slip_" . $student_db_id . "_" . $class_id . "_" . time() . "." . $file_ext;
    
    // Upload Path (අපි කලින් කතා කරගත් පරිදි uploads/slips/ ෆෝල්ඩරයට)
    $upload_dir = "../../uploads/slips/";

    // ෆෝල්ඩරය නොමැති නම් සෑදීම
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            die("Failed to create upload directory. Please check permissions.");
        }
    }

    // ගොනුව Upload කිරීම
    if (move_uploaded_file($file_tmp, $upload_dir . $new_filename)) {
        
        // 6. Database Insert
        $status = 'pending';
        $method = 'Slip';
        $payment_type = 'Full';
        
        $ins_sql = "INSERT INTO payments (student_id, class_id, month, year, amount, payment_status, method, slip_image, payment_type, paid_date) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt_ins = $conn->prepare($ins_sql);
        
        if ($stmt_ins) {
            // $student_db_id (Integer) භාවිතා කරයි
            $stmt_ins->bind_param("iissdssss", $student_db_id, $class_id, $month, $year, $amount, $status, $method, $new_filename, $payment_type);
            
            if ($stmt_ins->execute()) {
                // සාර්ථකයි! අලුත් Success Page එකට යොමු කිරීම
                header("Location: slip_success.php");
                exit();
            } else {
                // Database දෝෂයක් නම්
                echo "<script>alert('Database Error: " . addslashes($stmt_ins->error) . "'); window.history.back();</script>";
            }
            $stmt_ins->close();
        } else {
            echo "<script>alert('Database Prepare Error.'); window.history.back();</script>";
        }

    } else {
        echo "<script>alert('Failed to upload file to server.'); window.history.back();</script>";
    }

} else {
    // ගොනුව තෝරා නොමැති විට හෝ Upload දෝෂයක් ඇති විට
    $error_code = $_FILES['slip_file']['error'];
    if ($error_code == UPLOAD_ERR_NO_FILE) {
         echo "<script>alert('Please select a slip file to upload.'); window.history.back();</script>";
    } else {
         echo "<script>alert('Upload Error Code: $error_code'); window.history.back();</script>";
    }
}
?>