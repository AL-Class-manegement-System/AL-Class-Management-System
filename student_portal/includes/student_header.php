<?php
// student_portal/includes/student_header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../includes/connection.php';

// Login Check
if (!isset($_SESSION['student_id'])) {
    header("Location: ../../log/login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$full_name = $_SESSION['full_name'];

// Fetch Student Data (Prepared Statement භාවිතයෙන්)
$sql = "SELECT * FROM students WHERE reg_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$student_res = $stmt->get_result();

if ($student_res->num_rows === 1) {
    $student = $student_res->fetch_assoc();

    // Profile Pic Logic
    $photo_url = $student['photo'];
    $image_path = "../../assets/images/students/" . $photo_url;

    if (!empty($photo_url) && file_exists($image_path)) {
        $profile_pic = $image_path;
    } else {
        $profile_pic = "https://ui-avatars.com/api/?name=" . urlencode($student['full_name']) . "&background=6366f1&color=fff";
    }
} else {
    header("Location: ../../log/login.php");
    exit();
}

// ==========================================
// ACTIVE PAGE LOGIC
// ==========================================
// දැනට සිටින පිටුවේ නම ලබා ගැනීම
$current_page = basename($_SERVER['PHP_SELF']);

// Active Link එකට අදාළ CSS Class
function getActiveClass($page_name, $current_page)
{
    // do_exam.php හෝ submit_exam.php වැනි පිටු වල සිටින විටත් Exam Center එක Active කිරීමට
    if ($page_name === 'exam_center.php' && ($current_page === 'do_exam.php' || $current_page === 'submit_exam.php')) {
        return 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30';
    }
    
    if ($page_name === $current_page) {
        return 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30';
    } else {
        return 'text-slate-400 hover:text-white hover:bg-slate-800';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | Future Minds</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script src="../js/dashboard.js" defer></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#1E293B',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Custom Scrollbar for Sidebar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #1e293b; 
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #475569; 
            border-radius: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #64748b; 
        }
    </style>
</head>

<body class="bg-gray-50 text-slate-800">

    <div class="flex h-screen overflow-hidden relative">

        <div id="sidebarOverlay" onclick="toggleSidebar()"
            class="fixed inset-0 bg-slate-900/50 z-20 hidden transition-opacity duration-300 opacity-0 md:hidden backdrop-blur-sm">
        </div>

        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 text-white flex flex-col shadow-2xl transition-transform duration-300 transform -translate-x-full md:translate-x-0 md:static md:inset-auto md:transform-none border-r border-slate-800">

            <div class="flex items-center justify-center h-20 border-b border-gray-800">
                <div class="text-2xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-university text-indigo-500"></i>
                    <span>Future Minds</span>
                </div>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto custom-scrollbar">

                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 mt-2">Menu</p>

                <a href="index.php"
                    class="flex items-center px-4 py-3 rounded-xl transition-all group <?php echo getActiveClass('index.php', $current_page); ?>">
                    <i class="fas fa-th-large w-6 text-center"></i>
                    <span class="ms-3 font-medium">Dashboard</span>
                </a>

                <a href="my_classes.php"
                    class="flex items-center px-4 py-3 rounded-xl transition-all group <?php echo getActiveClass('my_classes.php', $current_page); ?>">
                    <i class="fas fa-book w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="ms-3 font-medium">My Classes</span>
                </a>

                <a href="live_class.php"
                    class="flex items-center px-4 py-3 rounded-xl transition-all group <?php echo getActiveClass('live_class.php', $current_page); ?>">
                    <i class="fas fa-video w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="ms-3 font-medium">Ongoing Classes</span>
                </a>

                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 mt-6">Academics</p>
                
                <a href="exam_center.php"
                    class="flex items-center px-4 py-3 rounded-xl transition-all group <?php echo getActiveClass('exam_center.php', $current_page); ?>">
                    <i class="fas fa-file-alt w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="ms-3 font-medium">Exam Center</span>
                </a>

                <a href="exam_results.php"
                    class="flex items-center px-4 py-3 rounded-xl transition-all group <?php echo getActiveClass('exam_results.php', $current_page); ?>">
                    <i class="fas fa-clipboard-list w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="ms-3 font-medium">Exam Results</span>
                </a>

                <a href="time_table.php"
                    class="flex items-center px-4 py-3 rounded-xl transition-all group <?php echo getActiveClass('time_table.php', $current_page); ?>">
                    <i class="fas fa-calendar-alt w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="ms-3 font-medium">Time Table</span>
                </a>

                <a href="past_lessons.php"
                    class="flex items-center px-4 py-3 rounded-xl transition-all group <?php echo getActiveClass('past_lessons.php', $current_page); ?>">
                    <i class="fas fa-history w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="ms-3 font-medium">Past Lessons</span>
                </a>

                <a href="study_packs.php"
                    class="flex items-center px-4 py-3 rounded-xl transition-all group <?php echo getActiveClass('study_packs.php', $current_page); ?>">
                    <i class="fas fa-box-open w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="ms-3 font-medium">Study Packs</span>
                </a>
            </nav>

            <div class="p-4 border-t border-slate-800 bg-slate-950">
                <a href="../../lib/logout.php"
                    class="flex items-center px-4 py-3 text-red-400 hover:bg-red-500/10 hover:text-red-300 rounded-xl transition-all group">
                    <i class="fas fa-sign-out-alt w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="ms-3 font-medium">Logout</span>
                </a>
            </div>

        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-y-auto w-full relative bg-gray-50 static">

            <header
                class="h-20 bg-white shadow-sm border-b border-gray-200 flex items-center justify-between px-6 sticky top-0 z-20">

                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()"
                        class="md:hidden text-slate-500 hover:text-indigo-600 focus:outline-none p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <h2 class="text-xl font-bold text-purple-800 hidden sm:block">
                        <?php
                        if ($current_page == 'index.php')
                            echo 'Dashboard';
                        elseif ($current_page == 'my_classes.php')
                            echo 'My Classes';
                        elseif ($current_page == 'live_class.php')
                            echo 'Live Session';
                        elseif ($current_page == 'time_table.php')
                            echo 'Time Table';
                        elseif ($current_page == 'st_profile.php')
                            echo 'Student Profile';
                        elseif ($current_page == 'study_packs.php')
                            echo 'Study Materials';
                        elseif ($current_page == 'exam_center.php' || $current_page == 'do_exam.php')
                            echo 'Exam Center';
                        else
                            echo 'Student Portal';
                        ?>
                    </h2>
                </div>

                <div class="flex items-center gap-4 md:gap-6">
                    <button
                        class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition relative border border-gray-200">
                        <i class="far fa-bell text-lg"></i>
                        <span class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                    </button>

                    <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-bold text-slate-700 leading-tight">
                                <?php echo htmlspecialchars($student['full_name']); ?>
                            </p>
                            <p class="text-xs text-slate-500 font-medium">
                                <?php echo htmlspecialchars($student['reg_number']); ?>
                            </p>
                        </div>

                        <div class="relative">
                            <button onclick="toggleDropdown()" class="flex items-center focus:outline-none group">
                                <img src="<?php echo $profile_pic; ?>"
                                    class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm ring-2 ring-transparent group-hover:ring-indigo-100 transition-all"
                                    alt="Profile">
                            </button>

                            <div id="userDropdown"
                                class="hidden absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl py-2 z-50 ring-1 ring-black ring-opacity-5 border border-gray-100 transform origin-top-right transition-all">

                                <div class="px-4 py-3 border-b border-gray-100 md:hidden bg-gray-50">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        <?php echo htmlspecialchars($student['full_name']); ?>
                                    </p>
                                    <p class="text-xs text-gray-500 truncate">
                                        <?php echo htmlspecialchars($student['reg_number']); ?>
                                    </p>
                                </div>

                                <div class="py-1">
                                    <a href="../pages/st_profile.php"
                                        class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                        <i class="far fa-user mr-3 w-4 text-gray-400"></i> Your Profile
                                    </a>
                                    <a href="#"
                                        class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                        <i class="fas fa-cog mr-3 w-4 text-gray-400"></i> Settings
                                    </a>
                                </div>

                                <div class="border-t border-gray-100 my-1"></div>

                                <a href="../../lib/logout.php"
                                    class="flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 hover:text-red-700">
                                    <i class="fas fa-sign-out-alt mr-3 w-4"></i> Sign out
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>