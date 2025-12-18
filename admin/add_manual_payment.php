<?php
// admin/add_manual_payment.php
session_start();
include('includes/auth.php');
include('db_con.php');

// --- AJAX HANDLER: ශිෂ්‍යයා තෝරාගත් විට විස්තර ලබා ගැනීම ---
if (isset($_POST['action']) && $_POST['action'] == 'fetch_student_data') {
    header('Content-Type: application/json');

    $student_id = intval($_POST['student_id']);

    // 1. ශිෂ්‍යයාගේ Stream එක ලබා ගැනීම
    $stu_query = $conn->query("SELECT stream FROM students WHERE student_id = $student_id");

    $stream = '';
    if ($stu_query->num_rows > 0) {
        $student = $stu_query->fetch_assoc();
        $stream = trim($student['stream']); // උදා: Maths, Bio
    }

    // 2. Stream එකට අදාළ පන්ති (Classes) ලබා ගැනීම
    // Stream names ගැටළුව විසඳීම (Mapping)
    if (!empty($stream)) {

        // Stream Mapping Array (Student Stream => Class Stream)
        $stream_map = [
            'Maths' => 'Physical Science',
            'Bio' => 'Bio Science',
            'Tech' => 'Technology',
            'Art' => 'Arts',
            'Commerce' => 'Commerce'
        ];

        // Short Name හෝ Long Name දෙකෙන් ඕනෑම එකකට ගැලපෙන පන්ති සෙවීම
        $mapped_stream = isset($stream_map[$stream]) ? $stream_map[$stream] : $stream;

        // SQL: Stream එක කෙලින්ම ගැලපෙන හෝ Map වූ නමට ගැලපෙන පන්ති
        $class_sql = "SELECT class_id, class_name, subject, fee, teacher_name 
                      FROM classes 
                      WHERE status = 1 
                      AND (stream = '$stream' OR stream = '$mapped_stream')";

    } else {
        // Stream එකක් සොයාගත නොහැකි නම් සියලු පන්ති
        $class_sql = "SELECT class_id, class_name, subject, fee, teacher_name FROM classes WHERE status = 1";
    }

    $class_res = $conn->query($class_sql);

    $classes = [];
    if ($class_res) {
        while ($row = $class_res->fetch_assoc()) {
            $classes[] = $row;
        }
    }

    // JSON ලෙස Data යැවීම
    echo json_encode(['stream' => $stream, 'classes' => $classes]);
    exit;
}

// --- FORM SUBMIT: මුදල් ගෙවීම ඇතුළත් කිරීම ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['action'])) {
    $student_id = $_POST['student_id'];
    $class_id = $_POST['class_id'];
    $amount = $_POST['amount'];
    $month = date('F'); // වත්මන් මාසය
    $year = date('Y');  // වත්මන් වර්ෂය

    if (!empty($student_id) && !empty($class_id)) {
        // 1. Payments Table එකට ඇතුළත් කිරීම
        $stmt = $conn->prepare("INSERT INTO payments (student_id, class_id, month, year, amount, payment_status, method, payment_type, paid_date) VALUES (?, ?, ?, ?, ?, 'paid', 'Cash', 'Full', NOW())");
        $stmt->bind_param("iisds", $student_id, $class_id, $month, $year, $amount);

        if ($stmt->execute()) {
            // 2. Enroll Table එක Update කිරීම
            $check_enroll = $conn->query("SELECT * FROM enrollments WHERE student_id = $student_id AND class_id = $class_id");

            if ($check_enroll->num_rows == 0) {
                // අලුත් Enrollment එකක්
                $enroll_sql = "INSERT INTO enrollments (student_id, class_id, status, joined_date, payment_method) VALUES (?, ?, 1, NOW(), 'Cash')";
                $stmt2 = $conn->prepare($enroll_sql);
                $stmt2->bind_param("ii", $student_id, $class_id);
                $stmt2->execute();
            } else {
                // දැනටමත් ඉන්නවා නම් Active කිරීම
                $conn->query("UPDATE enrollments SET status = 1 WHERE student_id = $student_id AND class_id = $class_id");
            }

            echo "<script>alert('Payment Added & Student Enrolled Successfully!'); window.location.href='payments.php';</script>";
        } else {
            echo "<script>alert('Error adding payment: " . $stmt->error . "');</script>";
        }
    } else {
        echo "<script>alert('Please select Student and Class!');</script>";
    }
}

// සිසුන් සහ පන්ති ලැයිස්තුව ලබා ගැනීම (Initial Load)
$students = $conn->query("SELECT student_id, full_name, reg_number FROM students ORDER BY reg_number DESC");
$all_classes = $conn->query("SELECT class_id, class_name, subject, fee, stream FROM classes WHERE status = 1");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Manual Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .select2-container .select2-selection--single {
            height: 50px;
            padding: 10px;
            border-color: #e5e7eb;
            border-radius: 0.5rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 50px;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <div class="flex">
        <?php include('includes/sidebar.php'); ?>
        <div class="ml-64 flex-1 p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">💰 Add Manual Payment (Cash)</h1>

            <div class="bg-white p-8 rounded-xl shadow-md max-w-2xl">
                <form method="POST" action="">

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Select Student</label>
                        <select name="student_id" id="student_id" class="w-full p-3 border rounded-lg" required>
                            <option value="">Search Student...</option>
                            <?php while ($st = $students->fetch_assoc()): ?>
                                <option value="<?php echo $st['student_id']; ?>">
                                    <?php echo $st['reg_number'] . " - " . $st['full_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Student Stream</label>
                        <input type="text" id="stream_display"
                            class="w-full p-3 border rounded-lg bg-gray-100 text-gray-600 font-bold" readonly
                            placeholder="Select a student first">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Select Class</label>
                        <select name="class_id" id="class_id" class="w-full p-3 border rounded-lg bg-white" required>
                            <option value="">Select Class...</option>
                            <?php while ($cl = $all_classes->fetch_assoc()): ?>
                                <option value="<?php echo $cl['class_id']; ?>" data-fee="<?php echo $cl['fee']; ?>">
                                    <?php echo $cl['subject'] . " - " . $cl['class_name'] . " (" . $cl['stream'] . ")"; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Amount (LKR)</label>
                        <input type="number" name="amount" id="amount" class="w-full p-3 border rounded-lg bg-gray-50"
                            placeholder="Class Fee" required>
                    </div>

                    <button type="submit"
                        class="bg-green-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-green-700 transition w-full shadow-lg">
                        <i class="fas fa-check-circle"></i> Confirm Payment & Enroll
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            // Select2 Active කිරීම
            $('#student_id').select2({
                placeholder: "Type Student ID or Name",
                allowClear: true,
                width: '100%'
            });

            // Fee Auto Fill
            $('#class_id').on('change', function () {
                var selectedFee = $(this).find(':selected').data('fee');
                if (selectedFee) {
                    $('#amount').val(selectedFee);
                } else {
                    $('#amount').val('');
                }
            });

            // AJAX: Student තේරූ විට Stream සහ Classes Filter කිරීම
            $('#student_id').on('change', function () {
                var studentID = $(this).val();

                if (studentID) {
                    $.ajax({
                        url: 'add_manual_payment.php',
                        type: 'POST',
                        data: { action: 'fetch_student_data', student_id: studentID },
                        dataType: 'json',
                        success: function (response) {

                            // Stream පෙන්වීම
                            $('#stream_display').val(response.stream ? response.stream : 'No Stream');

                            // Classes Dropdown Update
                            var classDropdown = $('#class_id');
                            classDropdown.empty();
                            classDropdown.append('<option value="">Select Class...</option>');

                            if (response.classes.length > 0) {
                                $.each(response.classes, function (key, cls) {
                                    var optionText = cls.subject + " - " + cls.class_name + " (Rs. " + cls.fee + ")";
                                    classDropdown.append('<option value="' + cls.class_id + '" data-fee="' + cls.fee + '">' + optionText + '</option>');
                                });
                            } else {
                                classDropdown.append('<option value="">No classes found for this stream</option>');
                            }

                            $('#amount').val('');
                        },
                        error: function (xhr, status, error) {
                            console.error("AJAX Error:", error);
                        }
                    });
                } else {
                    $('#stream_display').val('');
                    $('#amount').val('');
                }
            });
        });
    </script>
</body>

</html>