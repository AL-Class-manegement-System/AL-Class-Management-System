<?php
session_start();

include '../includes/connection.php';


if ($_SERVER["REQUEST_METHOD"]=="post") {
        $full_name = $_POST['full_name'];
        $dob =  $_POST['date_of_birth'];
        $gender = $_POST['gender'];
        $school = $_POST['school'];
        $address =  $_POST['address'];
        $student_phone =  $_POST['student_phone'];
        $parent_phone =  $_POST['parent_phone'];
        $stream =  $_POST['stream'];
        $batch = $_POST['batch'];

        // 3. Student Registration Number එක Auto Generate කිරීම (ST + Year + ID)
        // උදාහරණ: ST2025001
        $current_year = date("Y");
        $prefix = "ST{$current_year}";

        // දැනට අන්තිමට තියෙන ID එක ගන්න
        $id_query = "SELECT reg_number FROM students WHERE reg_number LIKE '$prefix%' ORDER BY student_id DESC LIMIT 1";
        $result = $conn->query($id_query);

        if ($result->num_rows > 0) {
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

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            // ෆොටෝ සේව් වන තැන (assests/images/students/)
            $target_dir = "../assests/images/students/";

            // ෆෝල්ඩරය නැත්නම් සාදන්න
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_extension = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
            // ෆයිල් එකේ නම Reg No එකට මාරු කිරීම (උදා: ST2025001.jpg)
            $new_filename = "{$reg_number}.{$file_extension}"; 
            $target_file = "{$target_dir}{$new_filename}";

            // Image වර්ග පරීක්ෂා කිරීම
            $allowed_types = ['jpg', 'jpeg', 'png'];
            if (in_array(strtolower($file_extension), $allowed_types)) {
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                    $photo_name = $new_filename;
                } else {
                    header("Location: registration.php?error=Image upload failed");
                    exit();
                }
            } else {
                header("Location: registration.php?error=Only JPG, JPEG, PNG allowed");
                exit();
            }
        }

        // 5. QR Code (Optional - දැනට Reg Number එකම දාමු)
        $qr_code = $reg_number; 

        // 6. Database එකට Data ඇතුළත් කිරීම (Insert Query)
        $sql = "INSERT INTO students (
                    reg_number, full_name, dob, gender, school, address, 
                    student_phone, parent_phone, stream, batch, photo, qr_code
                ) VALUES (
                    '$reg_number', '$full_name', '$dob', '$gender', '$school', '$address', 
                    '$student_phone', '$parent_phone', '$stream', '$batch', '$photo_name', '$qr_code'
                )";

        if ($conn->query($sql) === TRUE) {
            // සාර්ථක නම් Success Message එකක් සමඟ නැවත යවන්න
            header("Location: registration.php?success=Student Registered Successfully! Reg No: {$reg_number}");
            exit();
        } else {
            // Error එකක් ආවොත්
            header("Location: registration.php?error=" . urlencode($conn->error));
            exit();
        }
        // break; // No need for break here since there's no switch

} else {
    // කෙලින්ම මෙම ෆයිල් එකට ආවොත් ආපසු registration එකට යවන්න
    header("Location: registration.php");
    exit();
}

$conn->close();
?>