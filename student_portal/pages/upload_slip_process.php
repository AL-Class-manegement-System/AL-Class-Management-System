<?php
// student_portal/pages/upload_slip_process.php
session_start();

// --- Path correction: Go back 2 levels to find includes ---
include('../../includes/connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['student_id'])) {

    $student_id = $_SESSION['student_id'];
    $class_id = intval($_POST['class_id']);

    // File Validation
    if (isset($_FILES['slip_file']) && $_FILES['slip_file']['error'] == 0) {

        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $filename = $_FILES['slip_file']['name'];
        $filesize = $_FILES['slip_file']['size'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            die("Error: Invalid file type. Only JPG, PNG, PDF allowed.");
        }

        // Upload Directory (Adjusted path relative to this file)
        $upload_dir = "../../uploads/slips/";

        // Check if directory exists, if not create it
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $new_filename = "slip_" . $student_id . "_" . $class_id . "_" . time() . "." . $ext;
        $destination = $upload_dir . $new_filename;

        if (move_uploaded_file($_FILES['slip_file']['tmp_name'], $destination)) {

            // Check if connection is working
            if ($conn === null) {
                die("Database connection failed.");
            }

            // Insert or Update Enrollment (Status 0 = Pending)
            $check_sql = "SELECT * FROM enrollments WHERE student_id = ? AND class_id = ?";
            $stmt_check = $conn->prepare($check_sql);
            $stmt_check->bind_param("ii", $student_id, $class_id);
            $stmt_check->execute();
            $res_check = $stmt_check->get_result();

            if ($res_check->num_rows > 0) {
                // Update existing record
                $update_sql = "UPDATE enrollments SET slip_image = ?, status = 0, enrolled_at = NOW(), payment_method = 'Bank Slip' WHERE student_id = ? AND class_id = ?";
                $stmt_up = $conn->prepare($update_sql);
                $stmt_up->bind_param("sii", $new_filename, $student_id, $class_id);
                $stmt_up->execute();
            } else {
                // Insert New Record
                $insert_sql = "INSERT INTO enrollments (student_id, class_id, status, enrolled_at, slip_image, payment_method) VALUES (?, ?, 0, NOW(), ?, 'Bank Slip')";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("iis", $student_id, $class_id, $new_filename);
                $stmt->execute();
            }

            // Redirect back with success message
            header("Location: my_classes.php?msg=slip_uploaded");
            exit();

        } else {
            die("Error: Failed to move uploaded file. Check folder permissions.");
        }

    } else {
        die("Error: No file uploaded or upload error code: " . $_FILES['slip_file']['error']);
    }
} else {
    // If accessed directly without POST
    header("Location: my_classes.php");
    exit();
}
?>