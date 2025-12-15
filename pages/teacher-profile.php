<?php
// pages/teacher-profile.php
include('../includes/connection.php');
include('../includes/header.php');

// 1. Get Teacher ID safely
if (isset($_GET['id'])) {
    $teacher_id = intval($_GET['id']);
} else {
    echo "<script>window.location.href='teachers.php';</script>";
    exit();
}

// 2. Fetch Teacher Data
$sql = "SELECT * FROM teachers WHERE teacher_id = $teacher_id AND status = 1 LIMIT 1";
$result = mysqli_query($conn, $sql);
$teacher = mysqli_fetch_assoc($result);

// Redirect if not found
if (!$teacher) {
    echo "<script>alert('Teacher profile not found.'); window.location.href='teachers.php';</script>";
    exit();
}

// 3. Prepare Data
$img = $teacher['image'];
$imagePath = "../assets/images/teachers/" . $img;

// Fallback image
if (empty($img) || !file_exists($imagePath)) {
    $imagePath = "https://ui-avatars.com/api/?name=" . urlencode($teacher['full_name']) . "&size=512&background=4f46e5&color=ffffff&bold=true";
}

// Prepare Qualifications
$qualifications = [];
if (!empty($teacher['qualifications'])) {
    // Split by new line
    $qualifications = array_filter(explode("\n", $teacher['qualifications']));
}

$teacher_phone = $teacher['phone'] ?? '';
$teacher_email = !empty($teacher['email']) ? $teacher['email'] : 'info@futureminds.lk';
?>

<title><?php echo htmlspecialchars($teacher['full_name']); ?> - Profile | Future Minds</title>

<div class="bg-gray-50 min-h-screen font-sans pb-20">

    <div class="relative h-[280px] lg:h-[350px] bg-slate-900 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-900 via-purple-900 to-slate-900 opacity-95"></div>
        <div
            class="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-500 rounded-full mix-blend-overlay filter blur-[100px] opacity-20 animate-pulse">
        </div>
        <div
            class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-purple-500 rounded-full mix-blend-overlay filter blur-[80px] opacity-20">
        </div>

        <div class="absolute top-28 left-6 lg:left-12 z-20">
            <a href="teachers.php"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md border border-white/20 rounded-full text-white hover:bg-white/20 transition-all duration-300 group">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                <span class="text-sm font-medium">Back to Teachers</span>
            </a>
        </div>
    </div>

    <div class="container mx-auto px-4 lg:px-8 -mt-24 lg:-mt-32 relative z-10">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <div class="lg:col-span-4 xl:col-span-3">
                <div
                    class="bg-white rounded-3xl shadow-xl shadow-slate-200 border border-slate-100 p-6 text-center sticky top-24">

                    <div class="relative w-40 h-40 mx-auto -mt-20 mb-6">
                        <div class="absolute inset-0 bg-indigo-600 rounded-full blur opacity-20 translate-y-2"></div>
                        <img src="<?php echo $imagePath; ?>"
                            alt="<?php echo htmlspecialchars($teacher['full_name']); ?>"
                            class="w-full h-full object-cover rounded-full border-4 border-white shadow-lg relative z-10">

                        <div class="absolute bottom-2 right-2 z-20 bg-green-500 text-white w-8 h-8 rounded-full flex items-center justify-center border-2 border-white shadow-sm"
                            title="Verified Lecturer">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                    </div>

                    <h1 class="text-2xl font-bold text-slate-800 mb-1 leading-tight">
                        <?php echo htmlspecialchars($teacher['full_name']); ?>
                    </h1>
                    <span
                        class="inline-block px-3 py-1 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-full uppercase tracking-wide mb-6 border border-indigo-100">
                        <?php echo htmlspecialchars($teacher['subject']); ?>
                    </span>

                    <div class="space-y-3">
                        <?php if (!empty($teacher_phone)): ?>
                            <a href="tel:<?php echo $teacher_phone; ?>"
                                class="flex items-center justify-center gap-3 w-full py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/20 group">
                                <i class="fas fa-phone-alt group-hover:rotate-12 transition-transform"></i> Call Now
                            </a>
                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $teacher_phone); ?>"
                                target="_blank"
                                class="flex items-center justify-center gap-3 w-full py-3 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition shadow-lg shadow-green-500/20">
                                <i class="fab fa-whatsapp text-lg"></i> WhatsApp
                            </a>
                        <?php endif; ?>

                        <a href="mailto:<?php echo $teacher_email; ?>"
                            class="flex items-center justify-center gap-3 w-full py-3 bg-slate-50 border border-slate-200 text-slate-700 rounded-xl font-semibold hover:bg-slate-100 transition">
                            <i class="far fa-envelope"></i> Email
                        </a>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100 grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-slate-400 text-xs uppercase font-bold tracking-wider mb-1">Students</div>
                            <div class="text-slate-800 font-bold text-lg">500+</div>
                        </div>
                        <div class="text-center">
                            <div class="text-slate-400 text-xs uppercase font-bold tracking-wider mb-1">Rating</div>
                            <div class="text-slate-800 font-bold text-lg flex items-center justify-center gap-1">
                                4.9 <i class="fas fa-star text-amber-400 text-xs"></i>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="lg:col-span-8 xl:col-span-9 space-y-6">

                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-lg">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800">About Me</h2>
                    </div>
                    <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed text-justify">
                        <?php echo nl2br(htmlspecialchars($teacher['description'])); ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xl">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 font-medium">Experience</p>
                            <p class="text-lg font-bold text-slate-800">10+ Years</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-xl">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 font-medium">Results</p>
                            <p class="text-lg font-bold text-slate-800">Best Island Ranks</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-full bg-pink-50 text-pink-500 flex items-center justify-center text-xl">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 font-medium">Dedication</p>
                            <p class="text-lg font-bold text-slate-800">100%</p>
                        </div>
                    </div>
                </div>

                <?php if (!empty($qualifications)): ?>
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-lg">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <h2 class="text-xl font-bold text-slate-800">Qualifications</h2>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <?php foreach ($qualifications as $qual):
                                if (trim($qual) != ''): ?>
                                    <div
                                        class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 border border-slate-100 hover:border-purple-200 transition-colors">
                                        <i class="fas fa-check-circle text-purple-500 mt-1"></i>
                                        <span class="text-slate-700 font-medium text-sm leading-snug">
                                            <?php echo htmlspecialchars(trim($qual)); ?>
                                        </span>
                                    </div>
                                <?php endif; endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div
                    class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-800 to-slate-900 p-8 md:p-10 text-white shadow-xl">
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                    <div
                        class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-3xl">
                    </div>

                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="text-center md:text-left">
                            <h3 class="text-2xl font-bold mb-2">Ready to start learning?</h3>
                            <p class="text-slate-300">Join my classes today and start your journey to success.</p>
                        </div>
                        <a href="../log/registration.php"
                            class="whitespace-nowrap px-8 py-3.5 bg-white text-slate-900 rounded-xl font-bold hover:bg-indigo-50 transition shadow-lg flex items-center gap-2">
                            Register Now <i class="fas fa-arrow-right text-indigo-600"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>