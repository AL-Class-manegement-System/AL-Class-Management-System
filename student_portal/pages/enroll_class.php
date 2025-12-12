<?php
// student_portal/pages/enroll_class.php
include('../includes/student_header.php');
require_once '../../includes/connection.php';

// 1. Get Class Details
if (!isset($_GET['class_id'])) {
    echo "<script>alert('Invalid Class!'); window.location.href='my_classes.php';</script>";
    exit();
}

$class_id = intval($_GET['class_id']);
$student_id = $student['student_id'];

// Check if already enrolled
$check_sql = "SELECT * FROM enrollments WHERE student_id = ? AND class_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ii", $student_id, $class_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo "<script>alert('You are already enrolled!'); window.location.href='my_classes.php';</script>";
    exit();
}

// Fetch Class Fee & Name
$class_sql = "SELECT class_name, subject, fee, teacher_name FROM classes WHERE class_id = ?";
$stmt = $conn->prepare($class_sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();

// 2. Handle Payment Submission
if (isset($_POST['submit_payment'])) {
    $entered_reg_no = $_POST['reg_number'];
    $payment_method = $_POST['payment_method'];
    $amount = $class['fee'];
    $month = date('F'); // Current Month
    $year = date('Y');

    if ($payment_method == 'Bank Slip') {
        // --- Bank Slip Upload Logic ---
        if (isset($_FILES['slip_image']) && $_FILES['slip_image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
            $ext = strtolower(pathinfo($_FILES['slip_image']['name'], PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_name = "slip_" . $student_id . "_" . time() . "." . $ext;
                $upload_dir = "../../uploads/slips/";
                
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                
                if (move_uploaded_file($_FILES['slip_image']['tmp_name'], $upload_dir . $new_name)) {
                    // Insert into payments table (Pending Status)
                    $sql = "INSERT INTO payments (student_id, class_id, month, year, amount, payment_status, method, payment_type, slip_image) 
                            VALUES (?, ?, ?, ?, ?, 'pending', 'Bank Slip', 'Registration', ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iisids", $student_id, $class_id, $month, $year, $amount, $new_name);
                    
                    if ($stmt->execute()) {
                        echo "<script>alert('Payment Slip Uploaded! Please wait for Admin approval.'); window.location.href='my_classes.php';</script>";
                    } else {
                        echo "<script>alert('Database Error: " . $conn->error . "');</script>";
                    }
                } else {
                    echo "<script>alert('Failed to upload image.');</script>";
                }
            } else {
                echo "<script>alert('Invalid file type! Only JPG, PNG, PDF allowed.');</script>";
            }
        } else {
            echo "<script>alert('Please upload the payment slip.');</script>";
        }
    }
}
?>

<div class="flex-1 flex flex-col h-screen overflow-y-auto bg-gray-50">
    <main class="p-8 max-w-3xl mx-auto w-full">
        
        <h1 class="text-3xl font-bold text-slate-800 mb-6">Enrollment & Payment</h1>

        <div class="bg-white p-8 rounded-2xl shadow-lg border border-indigo-100">
            
            <div class="mb-6 p-4 bg-indigo-50 rounded-xl border border-indigo-200">
                <h2 class="text-xl font-bold text-indigo-700"><?php echo htmlspecialchars($class['class_name']); ?></h2>
                <p class="text-gray-600"><?php echo htmlspecialchars($class['subject']); ?> | <?php echo htmlspecialchars($class['teacher_name']); ?></p>
                <div class="mt-2 text-2xl font-bold text-slate-800">LKR <?php echo number_format($class['fee'], 2); ?></div>
            </div>

            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Student Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($student['full_name']); ?>" readonly 
                            class="w-full p-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Student ID</label>
                        <input type="text" name="reg_number" value="<?php echo htmlspecialchars($student['reg_number']); ?>" required 
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white" placeholder="Confirm your ID">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Payment Method</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="Bank Slip" class="peer sr-only" checked onchange="togglePayment('slip')">
                            <div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:bg-gray-50 transition text-center">
                                <i class="fas fa-file-invoice-dollar text-2xl mb-2 text-indigo-600"></i>
                                <div class="font-bold text-gray-700">Bank Deposit</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="Online" class="peer sr-only" onchange="togglePayment('online')">
                            <div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:bg-gray-50 transition text-center">
                                <i class="fas fa-credit-card text-2xl mb-2 text-green-600"></i>
                                <div class="font-bold text-gray-700">Pay Online</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="slip-section" class="border-t pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Payment Slip / Screenshot</label>
                    <input type="file" name="slip_image" accept=".jpg,.jpeg,.png,.pdf" 
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-gray-500 mt-1">Bank: BOC | Acc: 12345678 | Branch: Colombo</p>
                    
                    <button type="submit" name="submit_payment" class="mt-6 w-full bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 transition">
                        Submit Enrollment
                    </button>
                </div>

                <div id="online-section" class="hidden border-t pt-4 text-center">
                    <p class="text-gray-600 mb-4">You will be redirected to PayHere secure gateway.</p>
                    <button type="button" onclick="payWithPayHere()" class="w-full bg-green-600 text-white py-3 rounded-xl font-bold hover:bg-green-700 transition">
                        Pay LKR <?php echo $class['fee']; ?> Now
                    </button>
                </div>

            </form>
        </div>
    </main>
</div>

<script>
function togglePayment(method) {
    if (method === 'slip') {
        document.getElementById('slip-section').classList.remove('hidden');
        document.getElementById('online-section').classList.add('hidden');
    } else {
        document.getElementById('slip-section').classList.add('hidden');
        document.getElementById('online-section').classList.remove('hidden');
    }
}

// PayHere Integration Logic (Basic)
function payWithPayHere() {
    // You can integrate the PayHere JS SDK or form submission here
    alert("PayHere Integration will be triggered here.\nOrder ID: Enr_<?php echo $student_id . '_' . $class_id; ?>\nAmount: <?php echo $class['fee']; ?>");
    // Redirect or submit PayHere form
}
</script>

<?php include('../includes/footer.php'); ?>