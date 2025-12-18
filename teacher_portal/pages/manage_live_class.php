<?php
// teacher_portal/pages/manage_live_class.php
session_start();
include('../../includes/connection.php');

// ගුරුවරයා ලොග් වී ඇත්දැයි බැලීම
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../../log/login.php");
    exit();
}

$teacher_db_id = $_SESSION['teacher_db_id'];
$teacher_name = "";

// ගුරුවරයාගේ නම ගැනීම
$t_sql = "SELECT full_name FROM teachers WHERE teacher_id = ?";
$stmt = $conn->prepare($t_sql);
$stmt->bind_param("i", $teacher_db_id);
$stmt->execute();
$res = $stmt->get_result();
if($r = $res->fetch_assoc()){ $teacher_name = $r['full_name']; }

// පන්ති ලිස්ට් එක ගැනීම
$class_sql = "SELECT class_id, class_name FROM classes WHERE teacher_name = ?";
$c_stmt = $conn->prepare($class_sql);
$c_stmt->bind_param("s", $teacher_name);
$c_stmt->execute();
$classes_result = $c_stmt->get_result();

$message = "";
$msg_type = "";

// Form Submit කිරීම
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_class'])) {
    $title = trim($_POST['title']);
    $class_id = intval($_POST['class_id']);
    $type = $_POST['type']; // Live or Recording
    $link = trim($_POST['link']);
    $start_time = $_POST['start_time'];

    // Status 0 (Pending) ලෙස ඇතුළත් කිරීම
    $sql = "INSERT INTO live_classes (title, class_id, teacher_id, type, class_link, start_time, status) VALUES (?, ?, ?, ?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siisss", $title, $class_id, $teacher_db_id, $type, $link, $start_time);

    if ($stmt->execute()) {
        $message = "Class added successfully! Waiting for Admin Approval.";
        $msg_type = "green";
    } else {
        $message = "Error: " . $conn->error;
        $msg_type = "red";
    }
}

include("../include/head.php");
include("../include/sidebar.php");
?>

<div class="p-4 sm:ml-64 pb-20">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Add Live Class / Recording</h2>

        <?php if ($message): ?>
            <div class="p-4 mb-4 text-sm rounded-lg <?php echo $msg_type == 'green' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Title</label>
                    <input type="text" name="title" class="bg-gray-50 border border-gray-300 text-sm rounded-lg w-full p-2.5" placeholder="Ex: Unit 5 Revision" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Select Class</label>
                    <select name="class_id" class="bg-gray-50 border border-gray-300 text-sm rounded-lg w-full p-2.5" required>
                        <option value="">-- Choose Class --</option>
                        <?php while ($row = $classes_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['class_id']; ?>"><?php echo htmlspecialchars($row['class_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Session Type</label>
                    <select name="type" class="bg-gray-50 border border-gray-300 text-sm rounded-lg w-full p-2.5" required>
                        <option value="Live">🔴 Live Zoom/Class</option>
                        <option value="Recording">📹 Recording</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Date & Time</label>
                    <input type="datetime-local" name="start_time" class="bg-gray-50 border border-gray-300 text-sm rounded-lg w-full p-2.5" required>
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Link (Zoom / YouTube)</label>
                    <input type="url" name="link" class="bg-gray-50 border border-gray-300 text-sm rounded-lg w-full p-2.5" placeholder="https://..." required>
                </div>
            </div>
            <button type="submit" name="add_class" class="mt-4 text-white bg-indigo-600 hover:bg-indigo-700 font-medium rounded-lg text-sm px-5 py-2.5">
                Add & Request Approval
            </button>
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">My Classes History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $h_sql = "SELECT * FROM live_classes WHERE teacher_id = ? ORDER BY created_at DESC";
                    $h_stmt = $conn->prepare($h_sql);
                    $h_stmt->bind_param("i", $teacher_db_id);
                    $h_stmt->execute();
                    $res_h = $h_stmt->get_result();

                    while($row = $res_h->fetch_assoc()):
                        $status = "";
                        if($row['status'] == 1) $status = '<span class="text-green-600 bg-green-100 px-2 py-1 rounded text-xs font-bold">Approved</span>';
                        elseif($row['status'] == 2) $status = '<span class="text-red-600 bg-red-100 px-2 py-1 rounded text-xs font-bold">Rejected</span>';
                        else $status = '<span class="text-yellow-600 bg-yellow-100 px-2 py-1 rounded text-xs font-bold">Pending</span>';
                        
                        $type_badge = ($row['type'] == 'Live') ? '🔴 Live' : '📹 Recording';
                    ?>
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($row['title']); ?></td>
                        <td class="px-6 py-4"><?php echo $type_badge; ?></td>
                        <td class="px-6 py-4"><?php echo date('Y-m-d H:i', strtotime($row['start_time'])); ?></td>
                        <td class="px-6 py-4"><?php echo $status; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include("../include/footer.php"); ?>