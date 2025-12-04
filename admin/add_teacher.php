<?php
include 'db_con.php';

if (isset($_POST['submit'])) {

    // 1. Data ලබා ගැනීම 
    // (bind_param භාවිතා කරන නිසා mysqli_real_escape_string අනවශ්‍යයි)
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
        $last_number = intval(substr($last_id, 6)); // අංක කොටස වෙන් කර ගැනීම
        $new_number = $last_number + 1;
        $teacher_number = "{$prefix}" . str_pad($new_number, 3, "0", STR_PAD_LEFT);
    } else {
        $teacher_number = "{$prefix}001"; // පළමු Teacher
    }

    // B. Random Password එකක් සැකසීම
    $auto_password = rand(100000, 999999);

    // ==========================================

    // 3. Image Upload Logic
    $target_dir = "../assets/images/teachers/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $new_image_name = "";
    $uploadOk = 1;

    // Image Upload කිරීම
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

    // 4. Database Insert (Using bind_param)
    if ($uploadOk == 1) {
        $status = 1; // bind_param සඳහා variable එකක් ලෙස තැබිය යුතුය

        // ? ලකුණු යොදා Query එක Prepare කිරීම
        $stmt = $conn->prepare("INSERT INTO teachers (teacher_number, password, full_name, subject, description, image, status) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if ($stmt) {
            // Parameters Bind කිරීම
            // s = string (පළමු 6), i = integer (status එක)
            // පිළිවෙල: teacher_number, password, full_name, subject, description, image, status
            $stmt->bind_param("ssssssi", $teacher_number, $auto_password, $name, $subject, $desc, $new_image_name, $status);

            // Execute කිරීම
            if ($stmt->execute()) {
                $msg = "Teacher Added Successfully! <br> <b>ID: $teacher_number</b> <br> <b>Password: $auto_password</b> <br> (Please note this down)";
                $msg_type = "success";
            } else {
                $msg = "Database Error: " . $stmt->error;
                $msg_type = "error";
            }
            $stmt->close(); // Statement එක close කිරීම
        } else {
            $msg = "Prepare Error: " . $conn->error;
            $msg_type = "error";
        }
    }
}
