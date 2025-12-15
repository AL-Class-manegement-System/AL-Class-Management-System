<?php
// student_portal/pages/enroll_class.php
include('../includes/student_header.php');

if (!isset($_GET['class_id'])) {
    echo "<script>window.location.href='my_classes.php';</script>";
    exit();
}

$class_id = intval($_GET['class_id']);
$student_id = $_SESSION['student_id'];

// Fetch Class Details
$sql = "SELECT * FROM classes WHERE class_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$result = $stmt->get_result();
$class = $result->fetch_assoc();

if (!$class) {
    echo "<div class='p-10 text-center'>Class not found.</div>";
    include('../includes/footer.php');
    exit();
}
?>

<div class="flex-1 flex flex-col h-screen overflow-y-auto bg-gray-50">
    <main class="p-6 md:p-12">
        <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden">
            <div class="bg-indigo-600 p-8 text-white text-center">
                <h1 class="text-3xl font-bold mb-2">Enroll in Class</h1>
                <p class="opacity-90">Confirm your details and proceed to payment</p>
            </div>

            <div class="p-8 md:p-12">
                <div
                    class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6 mb-8 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800"><?php echo htmlspecialchars($class['subject']); ?>
                        </h2>
                        <p class="text-slate-600"><?php echo htmlspecialchars($class['class_name']); ?> |
                            <?php echo htmlspecialchars($class['teacher_name']); ?>
                        </p>
                        <div
                            class="mt-2 inline-flex items-center px-3 py-1 rounded-full bg-white text-indigo-600 text-sm font-bold shadow-sm">
                            <?php echo htmlspecialchars($class['day']); ?> @
                            <?php echo htmlspecialchars($class['time']); ?>
                        </div>
                    </div>
                    <div class="text-center md:text-right">
                        <div class="text-sm text-slate-500 uppercase tracking-wider font-semibold">Class Fee</div>
                        <div class="text-3xl font-bold text-indigo-600">LKR
                            <?php echo number_format($class['fee'], 2); ?>
                        </div>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-slate-800 mb-6">Select Payment Method</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div
                        class="border-2 border-indigo-100 rounded-2xl p-6 hover:border-indigo-500 transition-all cursor-pointer relative group bg-white h-full flex flex-col justify-between">
                        <div>
                            <div class="absolute top-4 right-4 text-indigo-500"><i
                                    class="fas fa-credit-card text-2xl"></i></div>
                            <h4 class="font-bold text-lg text-slate-800 mb-2">Pay Online</h4>
                            <p class="text-sm text-slate-500 mb-6">Pay securely using Visa, MasterCard, or Genie.
                                Activation is instant.</p>
                        </div>
                        <button onclick="payOnline(<?php echo $class_id; ?>)"
                            class="w-full py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                            Pay Now & Enroll
                        </button>
                    </div>

                    <div class="border-2 border-gray-100 rounded-2xl p-6 hover:border-green-500 transition-all cursor-pointer relative group bg-white h-full flex flex-col justify-between"
                        id="slipCard">
                        <div>
                            <div class="absolute top-4 right-4 text-green-500"><i
                                    class="fas fa-file-invoice text-2xl"></i></div>
                            <h4 class="font-bold text-lg text-slate-800 mb-2">Upload Bank Slip</h4>
                            <p class="text-sm text-slate-500 mb-4">Transfer to the account below and upload the receipt.
                                (Manual Approval)</p>

                            <div class="bg-gray-50 p-3 rounded-lg text-sm text-slate-600 mb-4 border border-gray-200">
                                <p><strong>Bank:</strong> Commercial Bank</p>
                                <p><strong>Acc No:</strong> 1234567890</p>
                                <p><strong>Name:</strong> Future Minds</p>
                                <p><strong>Branch:</strong> Nugegoda</p>
                            </div>
                        </div>

                        <button onclick="toggleSlipForm()"
                            class="w-full py-3 bg-white border-2 border-green-500 text-green-600 rounded-xl font-bold hover:bg-green-50 transition"
                            id="btnToggleSlip">
                            Upload Slip
                        </button>

                        <form action="upload_slip_process.php" method="POST" enctype="multipart/form-data"
                            class="hidden mt-4" id="slipForm">
                            <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">

                            <label class="block mb-2 text-sm font-medium text-slate-600">Select Image/PDF</label>
                            <input type="file" name="slip_file" required accept=".jpg,.jpeg,.png,.pdf"
                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 mb-4 cursor-pointer">

                            <button type="submit"
                                class="w-full py-3 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition shadow-lg shadow-green-200">
                                Submit Slip
                            </button>
                            <button type="button" onclick="toggleSlipForm()"
                                class="w-full mt-2 py-2 text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>

<script>
    // 1. Toggle Slip Form Logic
    function toggleSlipForm() {
        const form = document.getElementById('slipForm');
        const btn = document.getElementById('btnToggleSlip');

        if (form.classList.contains('hidden')) {
            form.classList.remove('hidden');
            btn.classList.add('hidden');
        } else {
            form.classList.add('hidden');
            btn.classList.remove('hidden');
        }
    }

    // 2. PayHere Logic (Existing)
    function payOnline(classId) {
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        btn.disabled = true;

        $.ajax({
            url: 'get_payhere_hash.php',
            type: 'POST',
            data: { class_id: classId },
            dataType: 'json',
            success: function (data) {
                if (data.error) {
                    alert(data.error);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    return;
                }

                payhere.onCompleted = function onCompleted(orderId) {
                    window.location.href = "payment_success.php?order_id=" + orderId;
                };

                payhere.onDismissed = function onDismissed() {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                };

                payhere.onError = function onError(error) {
                    alert("Payment Error: " + error);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                };

                payhere.startPayment(data);
            },
            error: function () {
                alert("System Error: Could not initiate payment.");
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
    }
</script>

<?php include('../includes/footer.php'); ?>