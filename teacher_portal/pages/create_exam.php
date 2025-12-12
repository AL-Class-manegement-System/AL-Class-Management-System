<?php
// teacher_portal/pages/create_exam.php
// Updated: List View + Delete Option Added

include('../include/head.php');
require_once '../../includes/connection.php';

// Security Check
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../../log/login.php");
    exit();
}

$teacher_db_id = $_SESSION['teacher_db_id']; // Login එකේදී set කර ඇති DB ID එක
$msg = "";
$msg_type = "";

// -----------------------------------------
// 1. DELETE EXAM LOGIC
// -----------------------------------------
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    
    // Check if the exam belongs to this teacher (Security)
    $check_sql = "SELECT exam_id FROM online_exams WHERE exam_id = ? AND teacher_id = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("ii", $del_id, $teacher_db_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        // First Delete Questions associated with this exam
        $conn->query("DELETE FROM exam_questions WHERE exam_id = $del_id");
        
        // Then Delete the Exam
        $del_sql = "DELETE FROM online_exams WHERE exam_id = ?";
        $stmt_del = $conn->prepare($del_sql);
        $stmt_del->bind_param("i", $del_id);
        
        if ($stmt_del->execute()) {
            $msg = "Exam deleted successfully!";
            $msg_type = "green";
        } else {
            $msg = "Error deleting exam: " . $conn->error;
            $msg_type = "red";
        }
    } else {
        $msg = "You don't have permission to delete this exam.";
        $msg_type = "red";
    }
}

// -----------------------------------------
// 2. CREATE EXAM LOGIC
// -----------------------------------------
if (isset($_POST['create_exam'])) {
    $title = $_POST['title'];
    $stream = $_POST['stream'];
    $subject = $_POST['subject'];
    $duration = $_POST['duration'];

    // Status එක Default ලෙස 'Pending' වැටේ
    $sql = "INSERT INTO online_exams (teacher_id, title, stream, subject, duration, approval_status, created_at) VALUES (?, ?, ?, ?, ?, 'Pending', NOW())";
    $stmt = $conn->prepare($sql);
    // Note: Assuming 'created_at' column exists or DB handles timestamp. If not, remove created_at from query.
    $stmt->bind_param("isssi", $teacher_db_id, $title, $stream, $subject, $duration);

    if ($stmt->execute()) {
        $exam_id = $stmt->insert_id;
        // ප්‍රශ්න ඇතුළත් කරන පිටුවට යොමු කිරීම
        header("Location: add_questions.php?exam_id=" . $exam_id);
        exit();
    } else {
        $msg = "Error: " . $conn->error;
        $msg_type = "red";
    }
}
?>

<?php include("../include/sidebar.php"); ?>

<div class="p-4 sm:ml-64 pb-20">
    
    <div class="flex items-center justify-between p-4 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm">
        <h2 class="text-xl font-bold text-gray-800">Exam Dashboard</h2>
        <div class="text-sm text-gray-500">Manage your online exams</div>
    </div>

    <?php if($msg): ?>
        <div class="<?php echo ($msg_type == 'green') ? 'bg-green-100 text-green-700 border-green-400' : 'bg-red-100 text-red-700 border-red-400'; ?> border-l-4 p-4 mb-6 rounded shadow-sm flex justify-between items-center">
            <p class="font-bold"><?php echo $msg; ?></p>
            <button onclick="this.parentElement.style.display='none'">&times;</button>
        </div>
    <?php endif; ?>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-8">
        <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Create New Exam</h2>
        
        <form method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Exam Title</label>
                    <input type="text" name="title" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" placeholder="Ex: Unit 1 Test - Motion">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Subject</label>
                    <input type="text" name="subject" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" placeholder="Ex: Physics">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Stream (Target Audience)</label>
                    <select name="stream" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        <option value="Physical Science">Physical Science</option>
                        <option value="Bio Science">Bio Science</option>
                        <option value="Commerce">Commerce</option>
                        <option value="Arts">Arts</option>
                        <option value="Technology">Technology</option>
                        <option value="General">General / All</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Duration (Minutes)</label>
                    <input type="number" name="duration" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" placeholder="60">
                </div>
            </div>
            <button type="submit" name="create_exam" class="mt-6 text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 transition">
                <i class="ph ph-plus-circle mr-2"></i> Next: Add Questions
            </button>
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">My Created Exams</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Exam Title</th>
                        <th scope="col" class="px-6 py-3">Details</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch Exams belonging to logged-in teacher
                    $list_sql = "SELECT * FROM online_exams WHERE teacher_id = ? ORDER BY exam_id DESC";
                    $stmt_list = $conn->prepare($list_sql);
                    $stmt_list->bind_param("i", $teacher_db_id);
                    $stmt_list->execute();
                    $result_list = $stmt_list->get_result();

                    if ($result_list->num_rows > 0) {
                        while ($row = $result_list->fetch_assoc()) {
                            // Status Badge Color
                            $status_color = 'bg-yellow-100 text-yellow-800';
                            if($row['approval_status'] == 'Approved') $status_color = 'bg-green-100 text-green-800';
                            if($row['approval_status'] == 'Rejected') $status_color = 'bg-red-100 text-red-800';
                            
                            echo '<tr class="bg-white border-b hover:bg-gray-50">';
                            
                            // Title & Subject
                            echo '<td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">';
                            echo '<div class="text-base font-semibold">' . htmlspecialchars($row['title']) . '</div>';
                            echo '<div class="font-normal text-gray-500">' . htmlspecialchars($row['subject']) . '</div>';
                            echo '</td>';

                            // Stream & Duration
                            echo '<td class="px-6 py-4">';
                            echo '<div class="">Stream: ' . htmlspecialchars($row['stream']) . '</div>';
                            echo '<div class="text-xs text-gray-500">Duration: ' . $row['duration'] . ' mins</div>';
                            echo '</td>';

                            // Status
                            echo '<td class="px-6 py-4">';
                            echo '<span class="'.$status_color.' text-xs font-medium px-2.5 py-0.5 rounded border border-gray-200">'.htmlspecialchars($row['approval_status']).'</span>';
                            echo '</td>';

                            // Actions
                            echo '<td class="px-6 py-4 text-right space-x-2">';
                            // Edit/Add Questions Link
                            echo '<a href="add_questions.php?exam_id='.$row['exam_id'].'" class="font-medium text-blue-600 hover:underline"><i class="ph ph-pencil-simple"></i> Questions</a>';
                            // Separator
                            echo '<span class="text-gray-300">|</span>';
                            // Delete Link
                            echo '<a href="create_exam.php?delete_id='.$row['exam_id'].'" onclick="return confirm(\'Are you sure you want to delete this exam? This will verify all questions inside it.\');" class="font-medium text-red-600 hover:underline"><i class="ph ph-trash"></i> Delete</a>';
                            echo '</td>';

                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">You haven\'t created any exams yet.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include("../include/footer.php"); ?>