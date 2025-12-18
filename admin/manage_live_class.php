<?php
// admin/manage_live_class.php
session_start();
include('includes/auth.php');
include('db_con.php');

$message = "";

// 1. Approve / Reject / Delete Actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == 'approve') {
        $conn->query("UPDATE live_classes SET status = 1 WHERE live_id = $id");
        $message = "Class Approved Successfully!";
    } elseif ($action == 'reject') {
        $conn->query("UPDATE live_classes SET status = 2 WHERE live_id = $id");
        $message = "Class Rejected!";
    } elseif ($action == 'delete') {
        $conn->query("DELETE FROM live_classes WHERE live_id = $id");
        $message = "Class Deleted!";
    }
}

// 2. Admin Direct Add (Auto Approved)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_add_class'])) {
    $title = $_POST['title'];
    $class_id = $_POST['class_id'];
    $type = $_POST['type']; // Live or Recording
    $link = $_POST['link'];
    $start_time = $_POST['start_time'];

    // Admin adds -> Status 1 (Approved)
    $sql = "INSERT INTO live_classes (title, class_id, teacher_id, type, class_link, start_time, status) VALUES (?, ?, 0, ?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisss", $title, $class_id, $type, $link, $start_time);
    if($stmt->execute()){
        $message = "Class Added & Approved!";
    } else {
        $message = "Error adding class.";
    }
}

// Get Pending Classes
$pending = $conn->query("SELECT l.*, c.class_name, t.full_name FROM live_classes l 
                         JOIN classes c ON l.class_id = c.class_id 
                         LEFT JOIN teachers t ON l.teacher_id = t.teacher_id 
                         WHERE l.status = 0 ORDER BY l.created_at DESC");

// Get Approved Classes
$approved = $conn->query("SELECT l.*, c.class_name, t.full_name FROM live_classes l 
                          JOIN classes c ON l.class_id = c.class_id 
                          LEFT JOIN teachers t ON l.teacher_id = t.teacher_id 
                          WHERE l.status = 1 ORDER BY l.start_time DESC");

$classes = $conn->query("SELECT * FROM classes");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Live Classes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
    <?php include('includes/sidebar.php'); ?>

    <div class="ml-64 p-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Manage Live Classes & Recordings</h2>

        <?php if($message): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4 font-bold border border-green-200"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-8 shadow-sm">
            <h3 class="text-xl font-bold text-yellow-800 mb-4 flex items-center gap-2"><i class="fas fa-clock"></i> Pending Approvals</h3>
            
            <?php if($pending->num_rows > 0): ?>
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Title</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3">Teacher</th>
                            <th class="px-6 py-3">Class</th>
                            <th class="px-6 py-3">Time</th>
                            <th class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $pending->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold"><?php echo htmlspecialchars($row['title']); ?></td>
                            <td class="px-6 py-4">
                                <?php echo ($row['type'] == 'Live') ? '<span class="text-red-600 font-bold">Live</span>' : '<span class="text-blue-600 font-bold">Rec</span>'; ?>
                            </td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($row['class_name']); ?></td>
                            <td class="px-6 py-4"><?php echo $row['start_time']; ?></td>
                            <td class="px-6 py-4 flex gap-2">
                                <a href="?action=approve&id=<?php echo $row['live_id']; ?>" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 text-xs">Approve</a>
                                <a href="?action=reject&id=<?php echo $row['live_id']; ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs" onclick="return confirm('Reject?')">Reject</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <p class="text-gray-500 italic">No pending classes.</p>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-xl shadow p-6 mb-8 border border-gray-200">
            <h3 class="text-lg font-bold text-gray-700 mb-4">Direct Add (Admin)</h3>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="text-xs font-bold text-gray-600">Title</label>
                    <input type="text" name="title" class="w-full border p-2 rounded text-sm" required>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-600">Class</label>
                    <select name="class_id" class="w-full border p-2 rounded text-sm" required>
                        <?php while($c = $classes->fetch_assoc()) { echo "<option value='{$c['class_id']}'>{$c['class_name']}</option>"; } ?>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-600">Type</label>
                    <select name="type" class="w-full border p-2 rounded text-sm" required>
                        <option value="Live">Live Class</option>
                        <option value="Recording">Recording</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-600">Time</label>
                    <input type="datetime-local" name="start_time" class="w-full border p-2 rounded text-sm" required>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-600">Link</label>
                    <input type="text" name="link" class="w-full border p-2 rounded text-sm" placeholder="URL" required>
                </div>
                <button type="submit" name="admin_add_class" class="bg-indigo-600 text-white px-4 py-2 rounded font-bold hover:bg-indigo-700">Add</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="font-bold text-gray-800">Approved Live Classes & Recordings</h3>
            </div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Class</th>
                        <th class="px-6 py-3">Time</th>
                        <th class="px-6 py-3">Link</th>
                        <th class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $approved->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4"><?php echo htmlspecialchars($row['title']); ?></td>
                        <td class="px-6 py-4">
                            <?php echo ($row['type'] == 'Live') ? '<span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">Live</span>' : '<span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold">Rec</span>'; ?>
                        </td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($row['class_name']); ?></td>
                        <td class="px-6 py-4"><?php echo $row['start_time']; ?></td>
                        <td class="px-6 py-4"><a href="<?php echo $row['class_link']; ?>" target="_blank" class="text-blue-500 underline">Join</a></td>
                        <td class="px-6 py-4">
                            <a href="?action=delete&id=<?php echo $row['live_id']; ?>" onclick="return confirm('Delete?')" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>