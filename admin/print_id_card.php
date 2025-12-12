<?php
// admin/print_id_card.php
session_start();
include('includes/auth.php');
include('db_con.php');

// Student ID එක ලබා ගැනීම
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Student විස්තර ලබා ගැනීම (Prepared Statement)
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    
    if (!$student) {
        die("Student not found!");
    }
} else {
    die("Invalid Request!");
}

// Profile Picture Path එක සැකසීම
$photo_path = (!empty($student['photo']) && file_exists("../assets/images/students/" . $student['photo'])) 
    ? "../assets/images/students/" . $student['photo'] 
    : "../assets/images/user2.jpg";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print ID Card - <?php echo htmlspecialchars($student['full_name']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background: #e5e7eb; }
        
        /* ID Card Size (Standard CR80: 85.6mm x 54mm) */
        .id-card {
            width: 350px;
            height: 520px; /* Vertical ID Card */
            background: white;
            border-radius: 15px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            background-image: url('../assets/patterns/grid.png'); /* Optional Background Pattern */
        }

        .header-shape {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            height: 130px;
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            position: absolute;
            top: 0;
            width: 100%;
            z-index: 0;
        }

        /* Print Settings */
        @media print {
            body * { visibility: hidden; }
            #card-container, #card-container * { visibility: visible; }
            #card-container {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
            }
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen py-10">

    <div class="mb-8 flex gap-4 no-print">
        <button onclick="window.print()" class="bg-indigo-600 text-white px-6 py-2 rounded-lg shadow hover:bg-indigo-700 transition">
            <i class="fas fa-print mr-2"></i> Print Card
        </button>
        <a href="student.php" class="bg-gray-500 text-white px-6 py-2 rounded-lg shadow hover:bg-gray-600 transition">
            Back
        </a>
    </div>

    <div id="card-container">
        <div class="id-card flex flex-col items-center text-center border border-gray-200">
            
            <div class="header-shape shadow-sm"></div>

            <div class="z-10 mt-6 mb-2">
                <h1 class="text-white text-xl font-bold tracking-wide uppercase">Future Minds</h1>
                <p class="text-indigo-100 text-xs font-light">Higher Education Institute</p>
            </div>

            <div class="z-10 mt-2">
                <div class="w-32 h-32 rounded-full p-1 bg-white shadow-lg">
                    <img src="<?php echo $photo_path; ?>" class="w-full h-full rounded-full object-cover border-2 border-indigo-50">
                </div>
            </div>

            <div class="mt-4 px-6 w-full">
                <h2 class="text-gray-800 text-lg font-bold truncate"><?php echo htmlspecialchars($student['full_name']); ?></h2>
                <div class="mt-1">
                    <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                        <?php echo htmlspecialchars($student['stream']); ?>
                    </span>
                </div>
                
                <div class="mt-4 grid grid-cols-2 gap-2 text-left text-xs text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-100">
                    <div>
                        <p class="font-bold text-gray-400 uppercase text-[10px]">Student ID</p>
                        <p class="font-bold text-gray-800 text-sm"><?php echo htmlspecialchars($student['reg_number']); ?></p>
                    </div>
                    <div>
                        <p class="font-bold text-gray-400 uppercase text-[10px]">Batch</p>
                        <p class="font-bold text-gray-800"><?php echo htmlspecialchars($student['batch']); ?></p>
                    </div>
                </div>
            </div>

            <div class="mt-auto mb-6 w-full flex flex-col items-center gap-2">
                <div id="qrcode" class="p-1 bg-white border border-gray-100 rounded"></div>
                
                <svg id="barcode" class="w-48 h-10"></svg>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var studentID = "<?php echo $student['reg_number']; ?>";

            // 1. Generate QR Code
            new QRCode(document.getElementById("qrcode"), {
                text: studentID,
                width: 70,
                height: 70,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });

            // 2. Generate Barcode
            JsBarcode("#barcode", studentID, {
                format: "CODE128",
                lineColor: "#333",
                width: 1.5,
                height: 30,
                displayValue: true,
                fontSize: 12,
                background: "transparent"
            });
        });
    </script>

</body>
</html>