<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../../includes/connection.php');

// Teacher Auth Check
$teacher_db_id = $_SESSION['teacher_db_id'] ?? null;
if (!$teacher_db_id) {
    header("Location: ../../log/login.php");
    exit();
}

// Fetch Teacher Info
$t_sql = "SELECT full_name FROM teachers WHERE teacher_id = ?";
$t_stmt = $conn->prepare($t_sql);
$t_stmt->bind_param("i", $teacher_db_id);
$t_stmt->execute();
$t_res = $t_stmt->get_result();
$teacher_data = $t_res->fetch_assoc();
$teacher_name = $teacher_data['full_name'];

// Fetch Classes
$class_sql = "SELECT class_id, class_name FROM classes WHERE teacher_name = ?";
$class_stmt = $conn->prepare($class_sql);
$class_stmt->bind_param("s", $teacher_name);
$class_stmt->execute();
$classes_result = $class_stmt->get_result();

$message = "";
$msg_type = "";

// Handle Messages
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'success') {
        $message = "Lesson added successfully!";
        $msg_type = "green";
    } elseif ($_GET['msg'] == 'error') {
        $message = "Error adding lesson. Please try again.";
        $msg_type = "red";
    } elseif ($_GET['msg'] == 'upload_error') {
        $message = "Image upload failed.";
        $msg_type = "red";
    }
}

// Handle Form Submission
if $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_lesson']) {
    $title = trim($_POST['title']);
    $class_id = intval($_POST['class_id']);
    $video_url = trim($_POST['video_url']);
    $description = trim($_POST['description']);
    $cover_image = null;

    // Handle Cover Image Upload
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['cover_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $upload_dir = "../../uploads/lesson_covers/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $new_filename = "lesson_" . time() . "_" . uniqid() . "." . $ext;
            $target_file = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $target_file)) {
                $cover_image = $new_filename;
            } else {
                header("Location: add_past_lesson.php?msg=upload_error");
                exit();
            }
        }
    }

    // Insert into DB
    $sql = "INSERT INTO past_lessons (title, class_id, teacher_id, video_url, cover_image, description, created_at, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siisss", $title, $class_id, $teacher_db_id, $video_url, $cover_image, $description);

    if ($stmt->execute()) {
        header("Location: add_past_lesson.php?msg=success");
        exit();
    } else {
        header("Location: add_past_lesson.php?msg=error");
        exit();
    }
}

include("../include/head.php");
include("../include/sidebar.php");
?>

<div class="p-4 sm:ml-64 pt-20 pb-20">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Add Past Lesson</h2>

        <?php if ($message): ?>
            <div class="p-4 mb-4 text-sm rounded-lg flex items-center gap-2 <?php echo $msg_type == 'green' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                <i class="fas <?php echo $msg_type == 'green' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Title -->
                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Lesson Title</label>
                    <input type="text" name="title"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" required
                        placeholder="Ex: Mechanics - Motion under Gravity">
                </div>

                <!-- Class Selection -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Select Class</label>
                    <select name="class_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" required>
                        <option value="">-- Choose Class --</option>
                        <?php while ($row = $classes_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['class_id']; ?>">
                                <?php echo htmlspecialchars($row['class_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Video URL -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Video URL (YouTube/Zoom)</label>
                    <input type="url" name="video_url"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" required
                        placeholder="https://www.youtube.com/watch?v=...">
                </div>

                <!-- Cover Image -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Cover Image (Optional)</label>
                    <input type="file" name="cover_image"
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    <p class="mt-1 text-xs text-gray-500">Allowed: JPG, PNG, WEBP.</p>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Description / Notes</label>
                    <textarea name="description" rows="3"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 resize-none"
                        placeholder="Brief description about the lesson..."></textarea>
                </div>

            </div>

            <button type="submit" name="add_lesson"
                class="mt-6 text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 transition flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Add Lesson
            </button>
        </form>
    </div>

    <!-- Lesson History -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Recently Added Lessons</h3>
        </div>
        <div class="p-6">
            <?php
            $his_sql = "SELECT pl.*, c.class_name FROM past_lessons pl 
                        JOIN classes c ON pl.class_id = c.class_id 
                        WHERE pl.teacher_id = ? ORDER BY pl.created_at DESC LIMIT 10";
            $his_stmt = $conn->prepare($his_sql);
            $his_stmt->bind_param("i", $teacher_db_id);
            $his_stmt->execute();
            $res = $his_stmt->get_result();
            ?>

            <?php if ($res->num_rows > 0): ?>
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Lesson</th>
                                <th scope="col" class="px-6 py-3">Class</th>
                                <th scope="col" class="px-6 py-3">Added On</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $res->fetch_assoc()): ?>
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <?php if ($row['cover_image']): ?>
                                                <img class="w-10 h-10 rounded object-cover" src="../../uploads/lesson_covers/<?php echo $row['cover_image']; ?>" alt="Cover">
                                            <?php else: ?>
                                                <div class="w-10 h-10 rounded bg-indigo-100 flex items-center justify-center text-indigo-600">
                                                    <i class="fas fa-video"></i>
                                                </div>
                                            <?php endif; ?>
                                            <span><?php echo htmlspecialchars($row['title']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['class_name']); ?></td>
                                    <td class="px-6 py-4"><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                                    <td class="px-6 py-4">
                                        <?php if ($row['status'] == 1): ?>
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded border border-green-200">Active</span>
                                        <?php else: ?>
                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded border border-red-200">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="<?php echo htmlspecialchars($row['video_url']); ?>" target="_blank" class="font-medium text-blue-600 hover:underline">View</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-center py-4">No lessons added yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include("../include/footer.php"); ?>
