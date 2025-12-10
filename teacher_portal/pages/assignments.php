<?php
// Path variable for correct asset linking
$path = "../";

// Include the header
include $path . "include/head.php"; // Handles session_start()

// Include the database connection file
include "../../includes/connection.php"; //

// --- Security Check ---
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../../log/login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$page_message = null;
$assignments = [];
$error_message = null;

// --- Handle Assignment Submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_assignment'])) {
    $class_id = filter_input(INPUT_POST, 'class_id', FILTER_SANITIZE_NUMBER_INT);
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $due_date = filter_input(INPUT_POST, 'due_date', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

    if (empty($class_id) || empty($title) || empty($due_date)) {
        $page_message = ['type' => 'error', 'text' => 'Please fill all required fields (Class, Title, Due Date).'];
    } else {
        // SQL to insert the new assignment (UNCOMMENTED and CORRECTED)
        $insert_sql = "INSERT INTO assignments (teacher_id, class_id, title, description, due_date, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
        $insert_stmt = mysqli_prepare($conn, $insert_sql);
        
        // Correct bind_param types: i (teacher_id), i (class_id), s (title), s (description), s (due_date)
        if ($insert_stmt) {
            mysqli_stmt_bind_param($insert_stmt, "iisss", $teacher_id, $class_id, $title, $description, $due_date);
            
            if (mysqli_stmt_execute($insert_stmt)) {
                $page_message = ['type' => 'success', 'text' => 'Assignment "' . htmlspecialchars($title) . '" successfully posted.'];
            } else {
                $page_message = ['type' => 'error', 'text' => 'Failed to post assignment: ' . mysqli_stmt_error($insert_stmt)];
            }
            mysqli_stmt_close($insert_stmt);
        } else {
            $page_message = ['type' => 'error', 'text' => 'Failed to prepare insert statement: ' . mysqli_error($conn)];
        }
    }
}

// --- Fetch Classes for the Teacher (for the dropdown) ---
$classes_sql = "
    SELECT c.class_id, c.class_name, c.stream
    FROM classes c
    INNER JOIN teachers t ON c.teacher_name = t.full_name
    WHERE t.teacher_id = ?
    ORDER BY c.class_name
";
$classes_stmt = mysqli_prepare($conn, $classes_sql);
mysqli_stmt_bind_param($classes_stmt, "i", $teacher_id);
mysqli_stmt_execute($classes_stmt);
$classes_result = mysqli_stmt_get_result($classes_stmt);
$teacher_classes = mysqli_fetch_all($classes_result, MYSQLI_ASSOC);
mysqli_stmt_close($classes_stmt);

// --- Fetch Assignments (UNCOMMENTED) ---
if (!empty($teacher_classes)) {
    $assignment_sql = "
        SELECT a.title, a.due_date, a.description, c.class_name 
        FROM assignments a
        INNER JOIN classes c ON a.class_id = c.class_id
        WHERE a.teacher_id = ?
        ORDER BY a.due_date DESC
    ";
    $assign_stmt = mysqli_prepare($conn, $assignment_sql);
    
    if ($assign_stmt) {
        mysqli_stmt_bind_param($assign_stmt, "i", $teacher_id);
        mysqli_stmt_execute($assign_stmt);
        $assign_result = mysqli_stmt_get_result($assign_stmt);
        
        if ($assign_result) {
            $assignments = mysqli_fetch_all($assign_result, MYSQLI_ASSOC);
        } else {
            $error_message = "Error fetching assignments: " . mysqli_error($conn);
        }
        mysqli_stmt_close($assign_stmt);
    } else {
        $error_message = "Error preparing assignment fetch statement: " . mysqli_error($conn);
    }
}

// Dummy Assignments for display (REMOVED)

?>

<div class="flex">
    <?php include $path . "include/sidebar.php"; ?>

    <main id="main-content" class="p-4 sm:ml-64 pt-20 w-full min-h-screen">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-md">
            
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Manage Assignments</h1>
            
            <?php if ($page_message): ?>
                <div class="p-4 mb-4 text-sm text-<?php echo $page_message['type'] == 'error' ? 'red' : ($page_message['type'] == 'success' ? 'green' : 'blue'); ?>-800 rounded-lg bg-<?php echo $page_message['type'] == 'error' ? 'red' : ($page_message['type'] == 'success' ? 'green' : 'blue'); ?>-50" role="alert">
                    <span class="font-medium"><?php echo htmlspecialchars($page_message['text']); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                    <span class="font-medium">Database Error!</span> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <div class="p-4 bg-gray-50 border border-gray-300 rounded-lg mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Post New Assignment</h2>
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <input type="hidden" name="add_assignment" value="1">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="class_id" class="block mb-2 text-sm font-medium text-gray-900">Select Class (Required)</label>
                            <select id="class_id" name="class_id" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                <option value="">-- Choose Class --</option>
                                <?php foreach ($teacher_classes as $class): ?>
                                    <option value="<?php echo htmlspecialchars($class['class_id']); ?>">
                                        <?php echo htmlspecialchars($class['class_name'] . " (" . $class['stream'] . ")"); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="due_date" class="block mb-2 text-sm font-medium text-gray-900">Due Date (Required)</label>
                            <input type="date" id="due_date" name="due_date" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="title" class="block mb-2 text-sm font-medium text-gray-900">Assignment Title (Required)</label>
                        <input type="text" id="title" name="title" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-900">Description / Instructions</label>
                        <textarea id="description" name="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Provide detailed instructions or attach file links here..."></textarea>
                    </div>

                    <button type="submit" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Post Assignment
                    </button>
                </form>
            </div>
            
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Posted Assignments</h2>
            
            <?php if (empty($assignments)): ?>
                 <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50" role="alert">
                    <span class="font-medium">No Assignments Posted!</span> Use the form above to create a new assignment.
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($assignments as $assignment): ?>
                        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($assignment['title']); ?></h3>
                                    <p class="text-sm text-gray-500 mb-2">Class: <span class="font-medium text-gray-700"><?php echo htmlspecialchars($assignment['class_name']); ?></span></p>
                                    <p class="text-sm text-red-500">Due: <?php echo htmlspecialchars($assignment['due_date']); ?></p>
                                </div>
                                <div class="flex space-x-2">
                                    <button class="text-sm text-blue-600 hover:text-blue-800">View Submissions</button>
                                    <button class="text-sm text-red-600 hover:text-red-800">Delete</button>
                                </div>
                            </div>
                            <p class="text-gray-700 mt-2 text-sm"><?php echo htmlspecialchars($assignment['description']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include $path . "include/footer.php"; ?>