<?php
// student_portal/pages/enroll_class.php
// Compact Version: Split Screen Design (Same Layout, Smaller Size)
include('../includes/student_header.php');

// Class ID පරීක්ෂාව
if (!isset($_GET['class_id'])) {
    echo "<script>window.location.href='my_classes.php';</script>";
    exit();
}

$class_id = intval($_GET['class_id']);
$student_id = $_SESSION['student_id'];

// පන්තියේ දත්ත ගැනීම
$sql = "SELECT * FROM classes WHERE class_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();

if (!$class) {
    echo "<div class='p-10 text-center text-red-500 font-bold'>Class not found.</div>";
    include('../includes/footer.php');
    exit();
}
?>

<div class="flex-1 flex flex-col h-screen overflow-y-auto bg-gray-50 items-center justify-center p-4">

    <div class="w-full max-w-3xl bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row min-h-[450px]">

        <div class="w-full md:w-5/12 bg-indigo-600 p-6 text-white flex flex-col relative justify-between">
            
            <div>
                <h1 class="text-xl font-bold mb-1">Enrollment</h1>
                <p class="text-indigo-200 text-xs mb-6">Confirm details to join.</p>

                <div class="space-y-4"> <div>
                        <p class="text-[10px] font-bold text-indigo-300 uppercase tracking-wider mb-0.5">Subject</p>
                        <p class="text-lg font-bold leading-tight"><?php echo htmlspecialchars($class['subject']); ?></p>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold text-indigo-300 uppercase tracking-wider mb-0.5">Class Name</p>
                        <p class="text-sm font-medium text-indigo-50 leading-tight"><?php echo htmlspecialchars($class['class_name']); ?></p>
                        <p class="text-xs text-indigo-300"><?php echo htmlspecialchars($class['teacher_name']); ?></p>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold text-indigo-300 uppercase tracking-wider mb-0.5">Fee</p>
                        <p class="text-2xl font-bold">LKR <?php echo number_format($class['fee'], 2); ?></p>
                    </div>
                </div>
            </div>

            <div class="mt-6 md:mt-0">
                <a href="my_classes.php" class="inline-flex items-center text-xs font-medium text-indigo-200 hover:text-white transition">
                    <i class="fas fa-arrow-left mr-1"></i> Cancel
                </a>
            </div>

            <div class="absolute -bottom-10 -right-10 w-24 h-24 bg-indigo-500 rounded-full opacity-50 blur-2xl"></div>
        </div>

        <div class="w-full md:w-7/12 p-6 bg-white relative flex flex-col">
            
            <h2 class="text-lg font-bold text-gray-800 mb-4">Select Payment Method</h2>

            <div class="flex gap-6 border-b border-gray-200 mb-4 shrink-0">
                <button onclick="switchTab('online')" id="tab-online" class="pb-2 text-xs font-bold border-b-2 border-indigo-600 text-indigo-600 transition-colors focus:outline-none">
                    Online Payment
                </button>
                <button onclick="switchTab('slip')" id="tab-slip" class="pb-2 text-xs font-bold border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                    Bank Slip
                </button>
            </div>

            <div class="flex-1 relative">
                
                <div id="view-online" class="h-full flex flex-col justify-between animate-fade-in">
                    <div>
                        <p class="text-gray-500 text-xs mb-4">Pay securely using Credit/Debit Card via PayHere.</p>
                        
                        <div class="py-2 mb-2">
                            <img src="https://www.payhere.lk/downloads/images/payhere_short_banner.png" alt="PayHere" class="h-6 object-contain">
                        </div>

                        <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-100 flex items-start gap-3">
                            <i class="fas fa-shield-alt text-indigo-500 mt-0.5 text-lg"></i>
                            <div>
                                <p class="text-xs font-bold text-indigo-800">Secure Payment</p>
                                <p class="text-[10px] text-indigo-600 mt-0.5 leading-relaxed">
                                    Your transaction is secured with 128-bit SSL encryption. 
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3">
                        <button onclick="payOnline(<?php echo $class_id; ?>)" 
                            class="w-full bg-indigo-600 text-white py-2.5 rounded-lg font-bold text-sm hover:bg-indigo-700 shadow-md transition transform active:scale-[0.98]">
                            Pay LKR <?php echo number_format($class['fee'], 2); ?> Now
                        </button>
                    </div>
                </div>

                <div id="view-slip" class="h-full flex flex-col hidden animate-fade-in">
                    
                    <div class="shrink-0">
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-xs text-gray-700 space-y-1 mb-3">
                            <div class="flex justify-between border-b border-gray-200 pb-1">
                                <span class="text-gray-500">Bank</span>
                                <span class="font-bold">Commercial Bank</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-200 pb-1">
                                <span class="text-gray-500">Account No</span>
                                <span class="font-bold">1234567890</span>
                            </div>
                            <div class="flex justify-between pt-1">
                                <span class="text-gray-500">Name</span>
                                <span class="font-bold">Future Minds</span>
                            </div>
                        </div>
                    </div>

                    <form action="upload_slip_process.php" method="POST" enctype="multipart/form-data" class="flex-1 flex flex-col justify-between">
                        <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                        
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-700">Upload Receipt</label>
                            
                            <div class="flex items-center justify-center w-full mb-1">
                                <label for="slip_file_input" class="flex flex-col items-center justify-center w-full h-20 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                    <div class="flex flex-col items-center justify-center pt-2 pb-3">
                                        <i class="fas fa-cloud-upload-alt text-gray-400 text-lg mb-1"></i>
                                        <p class="text-[10px] text-gray-500">Click to upload (JPG/PDF)</p>
                                    </div>
                                    <input id="slip_file_input" type="file" name="slip_file" required accept=".jpg,.jpeg,.png,.pdf" class="hidden" onchange="showFileName(this)" />
                                </label>
                            </div>
                            <p id="file-name-display" class="text-[10px] text-center text-green-600 font-semibold h-3"></p>
                        </div>

                        <button type="submit" class="w-full bg-gray-800 text-white py-2.5 rounded-lg font-bold text-sm hover:bg-gray-900 transition shadow-md mt-2">
                            Submit Slip
                        </button>
                    </form>
                </div>

            </div>

        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>

<script>
    // Tab Logic
    function switchTab(tab) {
        const viewOnline = document.getElementById('view-online');
        const viewSlip = document.getElementById('view-slip');
        const tabOnline = document.getElementById('tab-online');
        const tabSlip = document.getElementById('tab-slip');

        if (tab === 'online') {
            viewOnline.classList.remove('hidden');
            viewSlip.classList.add('hidden');
            tabOnline.classList.add('border-indigo-600', 'text-indigo-600');
            tabOnline.classList.remove('border-transparent', 'text-gray-400');
            tabSlip.classList.add('border-transparent', 'text-gray-400');
            tabSlip.classList.remove('border-indigo-600', 'text-indigo-600');
        } else {
            viewOnline.classList.add('hidden');
            viewSlip.classList.remove('hidden');
            tabSlip.classList.add('border-indigo-600', 'text-indigo-600');
            tabSlip.classList.remove('border-transparent', 'text-gray-400');
            tabOnline.classList.add('border-transparent', 'text-gray-400');
            tabOnline.classList.remove('border-indigo-600', 'text-indigo-600');
        }
    }

    // Slip File Name
    function showFileName(input) {
        const display = document.getElementById('file-name-display');
        if (input.files && input.files[0]) {
            display.textContent = "Selected: " + input.files[0].name;
        } else {
            display.textContent = "";
        }
    }

    // PayHere Logic
    function payOnline(classId) {
        $.ajax({
            url: 'get_payhere_hash.php',
            type: 'POST',
            data: { class_id: classId },
            dataType: 'json',
            success: function (data) {
                if (data.error) {
                    alert("Error: " + data.error);
                    return;
                }
                
                payhere.onCompleted = function (orderId) {
                    window.location.href = "payment_success.php?order_id=" + orderId;
                };
                payhere.onDismissed = function () {
                    console.log("Payment dismissed");
                };
                payhere.onError = function (error) {
                    alert("Payment Failed: " + error);
                };

                payhere.startPayment(data);
            },
            error: function (xhr, status, error) {
                let errorMsg = "System Error: ";
                if(xhr.status === 404) {
                    errorMsg += "get_payhere_hash.php not found.";
                } else if(status === 'parsererror') {
                    errorMsg += "Invalid JSON response.";
                } else {
                    errorMsg += error;
                }
                console.error("Full Error:", xhr.responseText);
                alert(errorMsg);
            }
        });
    }
</script>

<style>
    .animate-fade-in { animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>

</body>
</html>