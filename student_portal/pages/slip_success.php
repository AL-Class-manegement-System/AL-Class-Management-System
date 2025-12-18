<?php
// student_portal/pages/slip_success.php
include('../includes/student_header.php');
?>

<div class="flex-1 flex flex-col h-screen overflow-y-auto bg-gray-50 items-center justify-center p-4">

    <div class="bg-white p-8 rounded-3xl shadow-xl w-full max-w-md text-center border border-gray-100 relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-green-400 to-emerald-600"></div>
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-green-50 rounded-full opacity-50 blur-2xl"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-green-50 rounded-full opacity-50 blur-2xl"></div>

        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
            <i class="fas fa-check text-4xl text-green-600"></i>
        </div>

        <h1 class="text-2xl font-bold text-gray-800 mb-2">Slip Uploaded Successfully!</h1>
        <p class="text-gray-500 text-sm mb-8 leading-relaxed">
            Thank you! Your payment slip has been submitted. <br>
            Please wait for <b>Admin Approval</b>. Once approved, the class will be automatically added to your dashboard.
        </p>

        <div class="space-y-3 relative z-10">
            <a href="my_classes.php" class="block w-full py-3 bg-gray-900 text-white rounded-xl font-bold hover:bg-black transition shadow-lg transform active:scale-[0.98]">
                Go to My Classes
            </a>
            <a href="index.php" class="block w-full py-3 bg-white text-gray-600 border border-gray-200 rounded-xl font-bold hover:bg-gray-50 transition">
                Back to Dashboard
            </a>
        </div>

    </div>

</div>

<?php include('../includes/footer.php'); ?>