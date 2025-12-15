<?php
// student_portal/pages/upload_slip_process.php
session_start();

// 1. Connection ෆයිල් එක නිවැරදිව සම්බන්ධ කරගැනීම
// (ඔබේ ෆෝල්ඩර ව්‍යුහය අනුව මෙය වෙනස් විය හැක, නමුත් සාමාන්‍යයෙන් පියවර 2ක් ආපස්සට යාම හරි)
if (file_exists('../../includes/connection.php')) {
    include('../../includes/connection.php');
} elseif (file_exists('../includes/connection.php')) {
    include('../includes/connection.php');
} else {
    die("Error: Connection file not found. Check the path.");
}

// 2. Login වී නැත්නම් එලියට දැමීම
if (!isset($_SESSION['student_id'])) {
    die("<div style='color:red; text-align:center; margin-top:50px;'>
            <h2>Access Denied!</h2>
            <p>You are not logged in.</p>
            <a href='../../log/login.php'>Go to Login</a>
         </div>");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $student_id = $_SESSION['student_id'];
    $class_id = intval($_POST['class_id']); // Class ID එක පිරිසිදු කිරීම

    // --- DEBUGGING: දෝෂය සොයා ගැනීමට ---
    // echo "Student ID: " . $student_id . "<br>";
    // echo "Class ID: " . $class_id . "<br>";
    // ------------------------------------

    // 3. ශිෂ්‍යයා සැබවින්ම Database එකේ සිටීදැයි පරීක්ෂා කිරීම (Validation)
    $check_st = $conn->query("SELECT student_id FROM students WHERE student_id = $student_id");
    if ($check_st->num_rows == 0) {
        session_destroy(); // වැරදි Session එක මකා දැමීම
        die("<div style='color:red; text-align:center; margin-top:50px; font-family:sans-serif;'>
                <h1>Account Error!</h1>
                <p>Your Student ID <b>($student_id)</b> does not exist in the database.</p>
                <p>Please log out and register/login again.</p>
                <a href='../../log/login.php' style='background:red; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Login Again</a>
             </div>");
    }

    // 4. පන්තියේ විස්තර ලබා ගැනීම
    $class_res = $conn->query("SELECT fee FROM classes WHERE class_id = $class_id");
    if ($class_res->num_rows == 0) {
        die("Error: Invalid Class ID ($class_id). The class does not exist.");
    }
    $class_data = $class_res->fetch_assoc();
    $amount = $class_data['fee'];
    $month = date('F');
    $year = date('Y');

    // 5. ෆයිල් එක Upload කිරීම
    if (isset($_FILES['slip_file']) && $_FILES['slip_file']['error'] == 0) {

        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $filename = $_FILES['slip_file']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            die("Error: Invalid file type. Only JPG, PNG, PDF allowed.");
        }

        $new_filename = "slip_" . $student_id . "_" . $class_id . "_" . time() . "." . $ext;
        $upload_dir = "../../uploads/slips/";

        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        if (move_uploaded_file($_FILES['slip_file']['tmp_name'], $upload_dir . $new_filename)) {

            // 6. Database එකට දත්ත ඇතුලත් කිරීම (Try-Catch සහිතව)
            try {
                $stmt = $conn->prepare("INSERT INTO payments (student_id, class_id, month, year, amount, payment_status, method, slip_image, payment_type, paid_date) 
                                        VALUES (?, ?, ?, ?, ?, 'pending', 'Bank Slip', ?, 'Full', NOW())");

                if ($stmt === false) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }

                $stmt->bind_param("iissdss", $student_id, $class_id, $month, $year, $amount, $new_filename);

                if ($stmt->execute()) {
                    // සාර්ථකයි!
                    echo "<script>
                            alert('Slip Uploaded Successfully! Please wait for Admin Approval.'); 
                            window.location.href='my_classes.php';
                          </script>";
                } else {
                    throw new Exception("Execute failed: " . $stmt->error);
                }

            } catch (mysqli_sql_exception $e) {
                // මෙතනදී තමයි අර Error එක අල්ලන්නේ
                die("<div style='color:red; padding:20px; font-family:monospace;'>
                        <h3>Database Error Occurred:</h3>
                        <p>" . $e->getMessage() . "</p>
                        <p><b>Solution:</b> Ensure your Student ID exists. Try logging out and back in.</p>
                     </div>");
            } catch (Exception $e) {
                die("Error: " . $e->getMessage());
            }

        } else {
            die("Error: Failed to move uploaded file. Check folder permissions.");
        }
    } else {
        die("Error: No file uploaded. Error Code: " . $_FILES['slip_file']['error']);
    }
} else {
    header("Location: my_classes.php");
    exit();
}
?>