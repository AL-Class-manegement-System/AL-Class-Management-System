<?php 

// 1. Connection සහ Header ගොනු ඇතුළත් කිරීම
include('../includes/connection.php'); 
include('../includes/header.php'); 

// 2. Query Parameter එකෙන් Teacher ID එක ලබා ගැනීම
$teacher_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($teacher_id === 0) {
    header('Location: teachers.php');
    exit();
}

// 3. Database එකෙන් අදාළ ගුරුවරයාගේ දත්ත ලබා ගැනීම
$sql = "SELECT * FROM teachers WHERE teacher_id = $teacher_id AND status = 1 LIMIT 1";
$result = mysqli_query($conn, $sql);
$teacher = mysqli_fetch_assoc($result);

if (!$teacher) {
    // දත්ත නොමැති නම් error පෙන්වීම
    echo '<div class="min-h-screen pt-40 container mx-auto px-6 text-center text-red-500">
            <h2 class="text-3xl font-bold">Error: Teacher Not Found!</h2>
            <p class="mt-4">The profile you are looking for does not exist.</p>
            <a href="teachers.php" class="mt-6 inline-block text-indigo-600 hover:text-indigo-800 font-medium">← Back to Teachers</a>
          </div>';
    include('../includes/footer.php');
    exit();
}

// 4. Image Path validation
$img = $teacher['image'];
$imagePath = "../assets/images/teachers/$img";

if (empty($img) || !file_exists($imagePath)) {
    // Default avatar එකක් භාවිත කිරීම
    $imagePath = "https://ui-avatars.com/api/?name=" . urlencode($teacher['full_name']) . "&size=256&background=4f46e5&color=ffffff";
}

// 5. Dummy Data (Database එකේ අදාළ columns/tables නොමැති නම් පෙන්වීමට)

// **Expertise Badges (Database එකේ skills/expertise column එකක් ලෙස තිබිය හැක)**
$teacher_expertise = [
    "A/L Theory Specialist", 
    "Past Paper Revision", 
    "Paper Marking Examiner",
    "10+ Years Experience"
];

// **Qualifications Data (වෙනම table එකක තිබිය හැක)**
$teacher_qualifications = [
    ['title' => 'Ph.D. in Applied Mathematics', 'institution' => 'University of Colombo', 'year' => '2015'],
    ['title' => 'BSc (Hons) in Mathematics', 'institution' => 'University of Peradeniya', 'year' => '2010'],
    ['title' => 'Certified Master Trainer', 'institution' => 'National Institute of Education', 'year' => '2018'],
];

// **File Data (teacher_files table එකෙන් ලබා ගත යුතුය)**
$files = [];
$files_sql = "SELECT * FROM teacher_files WHERE teacher_id = $teacher_id ORDER BY file_type, uploaded_on DESC";
// $files_result = mysqli_query($conn, $files_sql); 
// ... (Database fetching logic goes here) ...

// Dummy File Data for display
if (empty($files)) {
    $files = [
        ['file_type' => 'Timetable', 'file_name' => 'A/L 2026 Batch Schedule.pdf', 'file_path' => '#', 'link_text' => 'View Timetable'],
        ['file_type' => 'Poster', 'file_name' => 'New Class Enrollment Poster.jpg', 'file_path' => '#', 'link_text' => 'Download Poster'],
        ['file_type' => 'Resource', 'file_name' => 'Revision Questions Set.zip', 'file_path' => '#', 'link_text' => 'Get Resource'],
    ];
}

// Contact Info (teachers table එකේ email සහ phone columns තිබිය යුතුය)
$teacher_email = $teacher['email'] ?? 'contact@example.com'; 
$teacher_phone = $teacher['phone'] ?? '+94 77 XXX XXXX'; 

?>

<div class="min-h-screen bg-gray-50/50 pb-20">
    
    <div class="relative pt-32 pb-48 px-6 bg-gradient-to-br from-indigo-700 to-purple-800 overflow-hidden">
        
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" fill="none" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <defs><pattern id="patt" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M-3 13L15 -5M-5 5L13 -3M0 10L10 0" stroke="#ffffff" stroke-width="0.5"/></pattern></defs>
                <rect width="100%" height="100%" fill="url(#patt)"/>
            </svg>
        </div>
        
        <div class="container mx-auto relative z-10 text-center">
            <a href="teachers.php" class="inline-flex items-center text-indigo-200 hover:text-white font-medium mb-6 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                All Lecturers
            </a>
            
            <h1 class="text-5xl md:text-6xl font-extrabold text-white mb-2">
                <?php echo $teacher['full_name']; ?>
            </h1>
            <p class="text-2xl font-medium text-purple-300">
                <?php echo $teacher['subject']; ?> Lecturer
            </p>
        </div>
    </div>

    <div class="container mx-auto px-6 mt-[-160px] relative z-20">
        
        <div class="bg-white rounded-3xl shadow-2xl p-6 md:p-12 border border-gray-100 mb-8">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                
                <div class="flex-shrink-0 w-48 h-48 rounded-full overflow-hidden shadow-xl border-4 border-white ring-4 ring-indigo-300/50">
                    <img src="<?php echo $imagePath; ?>" 
                         alt="<?php echo $teacher['full_name']; ?>"
                         class="w-full h-full object-cover">
                </div>
                
                <div class="flex-grow text-center md:text-left">
                    <h2 class="text-3xl font-extrabold text-slate-900 mb-2 mt-4 md:mt-0">
                        <?php echo $teacher['full_name']; ?>
                    </h2>
                    <p class="text-xl font-semibold text-indigo-600 mb-4">
                        <?php echo $teacher['subject']; ?> Specialist
                    </p>
                    
                    <div class="flex flex-wrap justify-center md:justify-start gap-2 mt-4 pt-4 border-t border-gray-100">
                        <?php foreach($teacher_expertise as $expertise) { ?>
                            <span class="px-3 py-1 text-xs font-medium text-purple-700 bg-purple-100 rounded-full shadow-sm">
                                <?php echo $expertise; ?>
                            </span>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-8">
                
                <div class="bg-white rounded-3xl shadow-lg p-8 md:p-10 border border-gray-100">
                    <h3 class="text-2xl font-bold text-slate-800 mb-4 border-b pb-2 text-indigo-600">
                        Biography & Philosophy
                    </h3>
                    <div class="text-gray-600 leading-relaxed space-y-4">
                        <?php echo nl2br($teacher['description']); ?>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-lg p-8 md:p-10 border border-gray-100">
                    <h3 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-2 text-indigo-600">
                        Academic Qualifications
                    </h3>
                    <div class="space-y-6">
                        <?php foreach($teacher_qualifications as $q) { ?>
                            <div class="flex items-start bg-gray-50 p-4 rounded-xl border border-gray-100 shadow-sm">
                                <svg class="w-6 h-6 mr-4 text-purple-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.206 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.832 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.832 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.168 18 16.5 18s-3.332.477-4.5 1.253"></path></svg>
                                <div>
                                    <p class="font-semibold text-slate-800 text-lg"><?php echo $q['title']; ?></p>
                                    <p class="text-sm text-gray-600"><?php echo $q['institution']; ?> (<?php echo $q['year']; ?>)</p>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <div class="lg:col-span-1 space-y-8">
                
                <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100 sticky top-4">
                    <h3 class="text-xl font-bold text-slate-800 mb-4 border-b pb-2 text-indigo-600">
                        Connect with <?php echo explode(' ', $teacher['full_name'])[0]; ?>
                    </h3>
                    <div class="space-y-4">
                        
                        <a href="mailto:<?php echo $teacher_email; ?>" class="flex items-center p-4 bg-indigo-50 hover:bg-indigo-100 transition duration-200 rounded-xl border border-indigo-200">
                            <svg class="w-6 h-6 mr-4 text-indigo-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                            <div>
                                <p class="text-xs text-gray-500">Email Address</p>
                                <span class="font-medium text-slate-800 text-sm"><?php echo $teacher_email; ?></span>
                            </div>
                        </a>
                        
                        <div class="flex items-center p-4 bg-purple-50 rounded-xl border border-purple-200">
                            <svg class="w-6 h-6 mr-4 text-purple-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74A1 1 0 0118 16.847V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>
                            <div>
                                <p class="text-xs text-gray-500">Phone Number</p>
                                <span class="font-medium text-slate-800 text-sm"><?php echo $teacher_phone; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
                    <h3 class="text-xl font-bold text-slate-800 mb-4 border-b pb-2 text-indigo-600">
                        Class Schedules & Downloads
                    </h3>
                    
                    <div class="space-y-4">
                        <?php if (!empty($files)) { ?>
                            <?php foreach ($files as $file_row) { ?>
                                <div class="p-4 border rounded-xl bg-gray-50 flex items-center justify-between transition-shadow hover:shadow-md">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-700">
                                            <?php echo ucfirst($file_row['file_type']); ?>
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <?php echo htmlspecialchars($file_row['file_name']); ?>
                                        </p>
                                    </div>
                                    
                                    <a href="<?php echo $file_row['file_path']; ?>" 
                                       target="_blank" 
                                       class="flex items-center text-sm font-medium text-green-600 hover:text-green-800 transition-colors duration-200 p-2 rounded-full hover:bg-green-100"
                                       download>
                                        <?php echo $file_row['link_text'] ?? 'Download'; ?>
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </a>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <p class="text-gray-500 text-center text-sm py-4">No schedules or resources are available for download.</p>
                        <?php } ?>
                    </div>
                </div>

            </div>
            
        </div>
        
    </div>
</div>

<?php include('../includes/footer.php'); ?>