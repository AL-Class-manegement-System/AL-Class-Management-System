<?php 
// teacher-profile.php - Redesigned & ID Removed

include('../includes/connection.php'); 
include('../includes/header.php'); 

// 1. Teacher ID ලබා ගැනීම
$teacher_id = 0;
if (isset($_GET['id'])) {
    $teacher_id = intval($_GET['id']);
} elseif (isset($_GET['tid'])) {
    $teacher_id = intval($_GET['tid']);
}

if ($teacher_id === 0) {
    echo "<script>window.location.href='teachers.php';</script>";
    exit();
}

// 2. දත්ත ලබා ගැනීම
$sql = "SELECT * FROM teachers WHERE teacher_id = $teacher_id AND status = 1 LIMIT 1";
$result = mysqli_query($conn, $sql);
$teacher = mysqli_fetch_assoc($result);

if (!$teacher) {
    echo '<div class="min-h-screen flex flex-col items-center justify-center text-center px-6">
            <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-user-slash text-4xl text-red-500"></i>
            </div>
            <h2 class="text-3xl font-bold text-slate-800">Teacher Not Found!</h2>
            <p class="mt-2 text-slate-500">The profile you are looking for does not exist or has been removed.</p>
            <a href="teachers.php" class="mt-6 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Back to Lecturers
            </a>
          </div>';
    include('../includes/footer.php');
    exit();
}

// 3. රූපය සහ දත්ත සකසා ගැනීම
$img = $teacher['image'];
$imagePath = "../assets/images/teachers/" . $img;

if (empty($img) || !file_exists($imagePath)) {
    $imagePath = "https://ui-avatars.com/api/?name=" . urlencode($teacher['full_name']) . "&size=256&background=4f46e5&color=ffffff";
}

// Qualifications ලැයිස්තුව
$qualifications_text = isset($teacher['qualifications']) ? $teacher['qualifications'] : '';
$teacher_qualifications = [];
if (!empty($qualifications_text)) {
    $teacher_qualifications = array_filter(explode("\n", $qualifications_text));
}

$teacher_phone = $teacher['phone'] ?? ''; 
// Email එක නැත්නම් Default එකක්
$teacher_email = !empty($teacher['email']) ? $teacher['email'] : 'info@futureminds.lk';

?>

<div class="bg-slate-50 min-h-screen font-poppins">
    
    <div class="relative bg-gradient-to-r from-slate-900 to-indigo-900 h-[400px]">
        <div class="absolute inset-0 bg-[url('../assets/patterns/grid.png')] opacity-10"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-slate-900/50"></div>
        
        <div class="container mx-auto px-6 h-full flex flex-col justify-center relative z-10 pb-20">
            <a href="teachers.php" class="inline-flex items-center text-slate-300 hover:text-white transition w-fit mb-6 group">
                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center mr-3 group-hover:bg-indigo-500 transition">
                    <i class="fas fa-arrow-left text-sm"></i>
                </div>
                <span>Back to All Lecturers</span>
            </a>
        </div>
    </div>

    <div class="container mx-auto px-6 -mt-40 relative z-20 pb-20">
        
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden mb-8 border border-slate-100">
            <div class="p-8 md:p-10 flex flex-col md:flex-row gap-8 md:gap-12 items-center md:items-start">
                
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                    <div class="relative w-48 h-48 md:w-56 md:h-56 rounded-full border-4 border-white shadow-2xl overflow-hidden">
                        <img src="<?php echo $imagePath; ?>" 
                             alt="<?php echo htmlspecialchars($teacher['full_name']); ?>"
                             class="w-full h-full object-cover transform transition duration-500 group-hover:scale-110">
                    </div>
                    <div class="absolute bottom-4 right-4 w-6 h-6 bg-green-500 border-4 border-white rounded-full" title="Active Status"></div>
                </div>

                <div class="flex-1 text-center md:text-left pt-2">
                    <h1 class="text-3xl md:text-5xl font-bold text-slate-800 mb-2">
                        <?php echo htmlspecialchars($teacher['full_name']); ?>
                    </h1>
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-6">
                        <span class="px-4 py-1.5 rounded-full bg-indigo-50 text-indigo-700 font-semibold text-sm border border-indigo-100">
                            <?php echo htmlspecialchars($teacher['subject']); ?> Department
                        </span>
                        </div>

                    <div class="flex flex-wrap justify-center md:justify-start gap-4">
                        <?php if(!empty($teacher_phone)): ?>
                        <a href="tel:<?php echo $teacher_phone; ?>" class="flex items-center gap-3 px-6 py-3 bg-slate-900 text-white rounded-xl hover:bg-indigo-600 transition shadow-lg shadow-slate-200 hover:shadow-indigo-200">
                            <i class="fas fa-phone-alt"></i>
                            <span class="font-medium"><?php echo htmlspecialchars($teacher_phone); ?></span>
                        </a>
                        <?php endif; ?>
                        
                        <a href="mailto:<?php echo $teacher_email; ?>" class="flex items-center gap-3 px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 transition">
                            <i class="far fa-envelope text-lg"></i>
                            <span class="font-medium">Email Me</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-8 space-y-8">
                
                <div class="bg-white rounded-3xl p-8 md:p-10 shadow-lg border border-slate-100">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                            <i class="fas fa-user-tie text-xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800">About Me</h2>
                    </div>
                    <div class="prose prose-lg text-slate-600 leading-relaxed text-justify">
                        <?php echo nl2br(htmlspecialchars($teacher['description'])); ?>
                    </div>
                </div>

                <?php if (!empty($teacher_qualifications)): ?>
                <div class="bg-white rounded-3xl p-8 md:p-10 shadow-lg border border-slate-100">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                            <i class="fas fa-graduation-cap text-xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800">Education & Qualifications</h2>
                    </div>
                    
                    <div class="grid gap-4">
                        <?php foreach($teacher_qualifications as $qual): 
                            if(trim($qual) != ''): ?>
                            <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-purple-200 transition duration-300">
                                <div class="mt-1 w-2 h-2 rounded-full bg-purple-500 shrink-0"></div>
                                <span class="text-slate-700 font-medium text-lg leading-tight">
                                    <?php echo htmlspecialchars(trim($qual)); ?>
                                </span>
                            </div>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <div class="lg:col-span-4 space-y-8">
                
                <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl p-8 text-white shadow-xl">
                    <h3 class="font-semibold text-indigo-100 mb-6 uppercase tracking-wider text-sm">Overview</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <span class="block text-3xl font-bold mb-1">10+</span>
                            <span class="text-indigo-200 text-sm">Years Exp.</span>
                        </div>
                        <div>
                            <span class="block text-3xl font-bold mb-1">100%</span>
                            <span class="text-indigo-200 text-sm">Commitment</span>
                        </div>
                        <div class="col-span-2 pt-4 border-t border-white/10">
                            <p class="text-sm text-indigo-100 leading-relaxed">
                                "Education is the most powerful weapon which you can use to change the world."
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-lg border border-slate-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-slate-800">Class Materials</h3>
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">NEW</span>
                    </div>
                    
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                            <i class="fas fa-folder-open text-2xl"></i>
                        </div>
                        <h4 class="text-slate-800 font-medium mb-1">No Files Yet</h4>
                        <p class="text-sm text-slate-500 mb-4">Study materials will be uploaded shortly.</p>
                        <button class="w-full py-2.5 rounded-xl border-2 border-dashed border-indigo-200 text-indigo-600 font-medium hover:bg-indigo-50 transition text-sm">
                            Check Student Portal
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-lg border border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Share Profile</h3>
                    <div class="flex gap-2">
                        <button class="flex-1 py-2 rounded-lg bg-[#1877F2] text-white hover:opacity-90 transition">
                            <i class="fab fa-facebook-f"></i>
                        </button>
                        <button class="flex-1 py-2 rounded-lg bg-[#25D366] text-white hover:opacity-90 transition">
                            <i class="fab fa-whatsapp"></i>
                        </button>
                        <button class="flex-1 py-2 rounded-lg bg-slate-800 text-white hover:opacity-90 transition" onclick="navigator.clipboard.writeText(window.location.href); alert('Link Copied!');">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>