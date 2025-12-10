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
    // දත්ත නොමැති නම් error
    echo '<div class="min-h-screen pt-40 container mx-auto px-6 text-center text-red-500">
            <h2 class="text-3xl font-bold">Error: Teacher Not Found!</h2>
            <a href="teachers.php" class="mt-6 inline-block text-indigo-600 hover:text-indigo-800 font-medium">← Back to Teachers</a>
          </div>';
    include('../includes/footer.php');
    exit();
}

// 4. Image Path validation
$img = $teacher['image'];
$imagePath = "../assets/images/teachers/$img";

if (empty($img) || !file_exists($imagePath)) {
    $imagePath = "https://ui-avatars.com/api/?name=" . urlencode($teacher['full_name']) . "&size=256&background=4f46e5&color=ffffff";
}

// 5. Dummy Data (ඔබේ DB එකේ නැති data සඳහා)
// මෙම දත්ත Database එකේ අදාළ columns/tables වලට එකතු කළ යුතුය.
$teacher_expertise = ["A/L Theory Specialist", "Past Paper Revision", "Paper Marking Examiner"];
$teacher_qualifications = [
    ['title' => 'Ph.D. in Applied Mathematics', 'institution' => 'University of Colombo', 'year' => '2015'],
    ['title' => 'BSc (Hons) in Mathematics', 'institution' => 'University of Peradeniya', 'year' => '2010'],
];

// 6. ගුරුවරයාගේ ගොනු (Timetables/Posters) ලබා ගැනීම
$files = [];
$files_sql = "SELECT * FROM teacher_files WHERE teacher_id = $teacher_id ORDER BY file_type, uploaded_on DESC";
// $files_result = mysqli_query($conn, $files_sql); // මෙම query එක ක්‍රියාත්මක කරන්න
// if ($files_result) {
//     while ($file_row = mysqli_fetch_assoc($files_result)) {
//         $files[] = $file_row;
//     }
// }

// Dummy File Data (Database එකේ teacher_files table එක නැත්නම් මේක පෙන්වයි)
if (empty($files)) {
    $files = [
        ['file_type' => 'Timetable', 'file_name' => 'A/L 2026 Batch Schedule.pdf', 'file_path' => '#', 'link_text' => 'View Timetable'],
        ['file_type' => 'Poster', 'file_name' => 'New Class Enrollment Poster.jpg', 'file_path' => '#', 'link_text' => 'Download Poster'],
        ['file_type' => 'Resource', 'file_name' => 'Revision Questions Set.zip', 'file_path' => '#', 'link_text' => 'Get Resource'],
    ];
}


// ගුරුවරයාට email සහ phone number columns තිබේ යැයි උපකල්පනය කරමු
$teacher_email = $teacher['email'] ?? 'contact@example.com'; 
$teacher_phone = $teacher['phone'] ?? '+94 77 XXX XXXX'; 

?>

<div class="min-h-screen bg-gray-50/50 pb-20">
    
    <div class="relative pt-32 pb-24 px-6 bg-gradient-to-r from-indigo-600 to-purple-600 border-b border-gray-100">
        <div class="container mx-auto text-center">
            <a href="teachers.php" class="inline-flex items-center text-white/80 hover:text-white font-medium mb-4">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Teachers List
            </a>
            <h1 class="text-5xl font-extrabold text-white">
                <?php echo $teacher['full_name']; ?>
            </h1>
            <p class="text-xl font-medium text-indigo-200 mt-2">
                <?php echo $teacher['subject']; ?> Lecturer
            </p>
        </div>
    </div>

    <div class="container mx-auto px-6 mt-[-100px]">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-8">
                
                <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12 border border-gray-100">
                    <div class="flex flex-col md:flex-row items-start gap-8">
                        
                        <div class="flex-shrink-0 w-48 h-48 rounded-full overflow-hidden shadow-lg border-4 border-white ring-4 ring-indigo-200/50">
                            <img src="<?php echo $imagePath; ?>" 
                                 alt="<?php echo $teacher['full_name']; ?>"
                                 class="w-full h-full object-cover">
                        </div>
                        
                        <div>
                            <h2 class="text-3xl font-bold text-slate-900 mb-2">
                                <?php echo $teacher['full_name']; ?>
                            </h2>
                            <p class="text-xl font-semibold text-indigo-600 mb-4">
                                <?php echo $teacher['subject']; ?>
                            </p>
                            
                            <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-100">
                                <?php foreach($teacher_expertise as $expertise) { ?>
                                    <span class="px-3 py-1 text-xs font-medium text-purple-700 bg-purple-100 rounded-full">
                                        <?php echo $expertise; ?>
                                    </span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-lg p-8 md:p-10 border border-gray-100">
                    <h3 class="text-2xl font-bold text-slate-800 mb-4 border-b pb-2">
                        About <?php echo explode(' ', $teacher['full_name'])[0]; ?>
                    </h3>
                    <div class="text-gray-600 leading-relaxed space-y-4">
                        <?php echo nl2br($teacher['description']); ?>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-lg p-8 md:p-10 border border-gray-100">
                    <h3 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-2">
                        Education & Qualifications
                    </h3>
                    <div class="space-y-6">
                        <?php foreach($teacher_qualifications as $q) { ?>
                            <div class="flex items-start">
                                <svg class="w-6 h-6 mr-4 text-indigo-500 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
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
                    <h3 class="text-xl font-bold text-indigo-700 mb-4 border-b pb-2">
                        Get in Touch
                    </h3>
                    <div class="space-y-4">
                        
                        <div class="flex items-center p-3 bg-indigo-50 rounded-lg">
                            <svg class="w-5 h-5 mr-3 text-indigo-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                            <div>
                                <p class="text-xs text-gray-500">Email Address</p>
                                <a href="mailto:<?php echo $teacher_email; ?>" class="font-medium text-slate-800 hover:text-indigo-600 text-sm"><?php echo $teacher_email; ?></a>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-3 bg-indigo-50 rounded-lg">
                            <svg class="w-5 h-5 mr-3 text-indigo-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74A1 1 0 0118 16.847V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>
                            <div>
                                <p class="text-xs text-gray-500">Phone Number</p>
                                <span class="font-medium text-slate-800 text-sm"><?php echo $teacher_phone; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
                    <h3 class="text-xl font-bold text-slate-800 mb-4 border-b pb-2">
                        Timetables & Resources
                    </h3>
                    
                    <div class="space-y-4">
                        <?php if (!empty($files)) { ?>
                            <?php foreach ($files as $file_row) { ?>
                                <div class="p-4 border rounded-xl bg-gray-50 flex items-center justify-between">
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
                                       class="flex items-center text-sm font-medium text-green-600 hover:text-green-800 transition-colors duration-200 p-2 rounded-full"
                                       download>
                                        <?php echo $file_row['link_text'] ?? 'Download'; ?>
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </a>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <p class="text-gray-500 text-center text-sm py-4">No timetables or resources uploaded yet.</p>
                        <?php } ?>
                    </div>
                </div>

            </div>
            
        </div>
        
    </div>
</div>

<?php include('../includes/footer.php'); ?>