<?php
session_start();


require_once '../../includes/connection.php';


$student_id = $_SESSION['student_id'];
$full_name = $_SESSION['full_name'];

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$sql = "SELECT * FROM students WHERE reg_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$student = $stmt->get_result();

if ($student->num_rows === 1) {
    $student = $student->fetch_assoc();

    $photo_url = $student['photo'];
    $image_path = "../../assets/images/students/" . $photo_url;
    if (!empty($photo_url) && file_exists($image_path)) {
        // ෆොටෝ එක තියෙනවා නම් ඒක ගන්න
        $profile_pic = $image_path;
    } else {
        // ෆොටෝ එකක් නැත්නම්, නමේ අකුරු වලින් හැදෙන පින්තූරයක් (Default) ගන්න
        $profile_pic = "https://ui-avatars.com/api/?name=" . urlencode($student['full_name']) . "&background=6366f1&color=fff";
    }

} else {
    // Handle case where student is not found
    header("Location: ../../log/login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script src="../js/dashboard.js" defer></script>


</head>


<body class="bg-gray-100 font-sans">

    <div class="flex h-screen overflow-hidden relative">

        <div id="sidebarOverlay" onclick="toggleSidebar()"
            class="fixed inset-0 bg-black/50 z-20 hidden transition-opacity duration-300 opacity-0 md:hidden backdrop-blur-sm">
        </div>

        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 text-white flex flex-col shadow-2xl transition-transform duration-300 transform -translate-x-full md:translate-x-0 md:static md:inset-auto md:transform-none">

            <div class="h-20 flex items-center justify-between px-6 border-b border-slate-700 bg-slate-950">
                <div class="flex items-center gap-3">
                    <i class="fas fa-graduation-cap text-2xl text-primary"></i>
                    <span class="text-lg font-bold tracking-wide">Student Portal</span>
                </div>
                <button onclick="toggleSidebar()"
                    class="md:hidden text-slate-400 hover:text-white focus:outline-none transition-colors p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto custom-scrollbar">
                <a href="../"
                    class="flex items-center px-4 py-3 bg-primary text-white rounded-xl transition-all shadow-lg shadow-indigo-500/30 group">
                    <i class="fas fa-th-large w-6 text-center"></i>
                    <span class="ms-3 font-medium">Dashboard</span>
                </a>

                <a href="#"
                    class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all group">
                    <i class="fas fa-book w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="ms-3 font-medium">My Classes</span>
                </a>

                <a href="#"
                    class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all group">
                    <i class="fas fa-clipboard-list w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="ms-3 font-medium">Exam Results</span>
                </a>

                <a href="#"
                    class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all group">
                    <i class="fas fa-calendar-alt w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="ms-3 font-medium">Time Table</span>
                </a>

                <a href="../pages/live_class.php"
                    class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all group">
                    <i class="fas fa-video w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="ms-3 font-medium">Ongoing Classes</span>
                </a>

                <a href="#"
                    class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all group">
                    <i class="fas fa-history w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="ms-3 font-medium">Past Lessons</span>
                </a>

                <a href="#"
                    class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all group">
                    <i class="fas fa-box-open w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="ms-3 font-medium">Study Packs</span>
                </a>
            </nav>

            <div class="p-4 border-t border-slate-800 bg-slate-950">
                <a href="../../lib/logout.php"
                    class="flex items-center px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-xl transition-all group">
                    <i class="fas fa-sign-out-alt w-6 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="ms-3 font-medium">Logout</span>
                </a>
            </div>

        </aside>



        <!-- //header -->
        <div class="flex-1 flex flex-col h-screen overflow-y-auto w-full relative">

            <header
                class="mx-4 mb-0 mt-0 mb-6 rounded-2xl h-20 bg-white shadow-sm flex items-center justify-between px-6 sticky top-4 z-10 border border-gray-100">

                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()"
                        class="md:hidden text-slate-500 hover:text-primary focus:outline-none p-2 -ml-2 rounded-lg hover:bg-slate-50 transition-colors">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>

                    <h2 class="text-2xl font-bold text-slate-800 hidden sm:block">Dashboard</h2>
                </div>

                <div class="flex items-center gap-4 md:gap-6">
                    <button
                        class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-500 hover:text-primary transition relative border border-slate-200 hover:border-primary/30">
                        <i class="fas fa-bell"></i>
                        <span class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                    </button>

                    <div class="flex items-center gap-3 pl-4 border-l border-gray-100">
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-bold text-slate-700">
                                <?php echo isset($student['full_name']) ? htmlspecialchars($student['full_name']) : 'Student'; ?>
                            </p>
                            <p class="text-xs text-slate-500">
                                <?php echo isset($student['reg_number']) ? htmlspecialchars($student['reg_number']) : ''; ?>
                            </p>
                        </div>

                        <div class="relative">
                            <button onclick="toggleDropdown()"
                                class="flex items-center focus:outline-none ring-2 ring-transparent hover:ring-primary/20 rounded-full transition-all">
                                <img src="<?php echo isset($profile_pic) ? $profile_pic : '../../assets/images/user2.jpg'; ?>"
                                    class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm"
                                    alt="Profile">
                            </button>

                            <div id="userDropdown"
                                class="hidden absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl py-2 z-50 ring-1 ring-black ring-opacity-5 transition-all duration-200 ease-out origin-top-right transform animate-fade-in-down border border-gray-100">

                                <div class="px-4 py-3 border-b border-gray-100 md:hidden bg-gray-50">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        <?php echo isset($student['full_name']) ? htmlspecialchars($student['full_name']) : 'Student'; ?>
                                    </p>
                                    <p class="text-xs text-gray-500 truncate">
                                        <?php echo isset($student['email']) ? htmlspecialchars($student['email']) : ''; ?>
                                    </p>
                                </div>

                                <div class="py-1">
                                    <a href="../pages/st_profile.php"
                                        class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                        <i class="fas fa-user mr-3 w-4 text-gray-400"></i> Your Profile
                                    </a>
                                    <a href="#"
                                        class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                        <i class="fas fa-cog mr-3 w-4 text-gray-400"></i> Settings
                                    </a>
                                </div>

                                <div class="border-t border-gray-100 my-1"></div>

                                <a href="../../lib/logout.php"
                                    class="flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors rounded-b-xl">
                                    <i class="fas fa-sign-out-alt mr-3 w-4"></i> Sign out
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>