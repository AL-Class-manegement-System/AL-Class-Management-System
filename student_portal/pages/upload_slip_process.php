<?php
// student_portal/pages/upload_slip_process.php
session_start();
include('../../includes/connection.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: ../../log/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_SESSION['student_id'];
    $class_id = intval($_POST['class_id']);

    // පන්තියේ ගාස්තුව සොයා ගැනීම
    $class_res = $conn->query("SELECT fee FROM classes WHERE class_id = $class_id");
    if ($class_res->num_rows == 0) {
        die("Error: Class not found.");
    }
    $class_data = $class_res->fetch_assoc();
    $amount = $class_data['fee'];
    $month = date('F');
    $year = date('Y');

    // File Upload Logic
    if (isset($_FILES['slip_file']) && $_FILES['slip_file']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'pdf']; // PDF සහ Images වලට අවසර දීම
        $filename = $_FILES['slip_file']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            echo "<script>alert('Error: Only JPG, PNG, PDF files are allowed.'); window.history.back();</script>";
            exit();
        }

        // ගොනුවේ නම Unique කිරීම
        $new_filename = "slip_" . $student_id . "_" . $class_id . "_" . time() . "." . $ext;
        $upload_dir = "../../uploads/slips/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES['slip_file']['tmp_name'], $upload_dir . $new_filename)) {
            // Database Insert (Status = pending, Method = Slip)
            $stmt = $conn->prepare("INSERT INTO payments (student_id, class_id, month, year, amount, payment_status, method, slip_image, payment_type, paid_date) 
                                    VALUES (?, ?, ?, ?, ?, 'pending', 'Slip', ?, 'Full', NOW())");
            $stmt->bind_param("iissds", $student_id, $class_id, $month, $year, $amount, $new_filename);

            if ($stmt->execute()) {
                echo "<script>
                        alert('Slip Uploaded Successfully! Please wait for Admin Approval.'); 
                        window.location.href='my_classes.php';
                      </script>";
            } else {
                echo "Database Error: " . $stmt->error;
            }
        } else {
            echo "<script>alert('File Upload Error.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Please select a file.'); window.history.back();</script>";
    }
}
?>