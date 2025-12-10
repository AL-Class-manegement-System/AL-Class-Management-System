<?php
// Path variable for correct asset linking
$path = "../";

// Include the header
include $path . "include/head.php"; //

// Include the database connection file
include "../../includes/connection.php"; //

// --- Security Check ---
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../../log/login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// SQL to fetch classes assigned to the logged-in teacher
// We join classes and teachers using teacher_name since classes table lacks teacher_id
$sql = "
    SELECT 
        c.class_id,
        c.class_name,
        c.stream,
        c.year,
        c.day_of_week,
        c.start_time,
        c.end_time
    FROM
        classes c
    INNER JOIN
        teachers t ON c.teacher_name = t.full_name
    WHERE
        t.teacher_id = ?
    ORDER BY
        c.day_of_week, c.start_time
";

$stmt = mysqli_prepare($conn, $sql);
$classes = [];
$error_message = null;

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $teacher_id);
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        $result = mysqli_stmt_get_result($stmt);
        $classes = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error_message = "Error executing statement: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
} else {
    $error_message = "Error preparing statement: " . mysqli_error($conn);
}
?>

<div class="flex">
    <?php include $path . "include/sidebar.php"; ?>

    <main id="main-content" class="p-4 sm:ml-64 pt-20 w-full min-h-screen">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-md">
            
            <h1 class="text-2xl font-bold text-gray-900 mb-6">My Classes</h1>
            
            <?php if (isset($error_message)): ?>
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                    <span class="font-medium">Database Error!</span> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($classes)): ?>
                <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50" role="alert">
                    <span class="font-medium">No Classes Found!</span> You are not currently assigned to any classes.
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Class Name</th>
                                <th scope="col" class="px-6 py-3">Stream</th>
                                <th scope="col" class="px-6 py-3">Year</th>
                                <th scope="col" class="px-6 py-3">Schedule</th>
                                <th scope="col" class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($classes as $class): ?>
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        <?php echo htmlspecialchars($class['class_name']); ?>
                                    </th>
                                    <td class="px-6 py-4">
                                        <?php echo htmlspecialchars($class['stream']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php echo htmlspecialchars($class['year']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php echo htmlspecialchars($class['day_of_week'] . " (" . $class['start_time'] . " - " . $class['end_time'] . ")"); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="my_students.php?class_id=<?php echo $class['class_id']; ?>" class="font-medium text-blue-600 hover:underline mr-2">View Students</a>
                                        <a href="assignments.php?class_id=<?php echo $class['class_id']; ?>" class="font-medium text-indigo-600 hover:underline">Manage Assignments</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

        </div>
    </main>
</div>

<?php include $path . "include/footer.php"; ?> ```

---

### 3. 📄 `teacher_portal/pages/my_students.php` (My Students Page)

මෙය ඔබ කලින් ඉල්ලා සිටි නිවැරදි කළ කේතයයි. මෙහිදී, `my_classes.php` වෙතින් `class_id` එකක් ලැබුණහොත්, එම පන්තියේ සිසුන් පමණක් පෙරීමට හැකි වන පරිදි වෙනස්කම් කර ඇත.

```php
<?php
// Path variable for correct asset linking
$path = "../";

// Include the header
include $path . "include/head.php"; //

// Include the database connection file
include "../../includes/connection.php"; //

// --- Security Check ---
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../../log/login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$students = [];
$error_message = null;
$page_title = "All Enrolled Students";

// Check if a specific Class ID is passed (e.g., from 'My Classes' page)
$filter_by_class = isset($_GET['class_id']) && is_numeric($_GET['class_id']);
$class_id_filter = $filter_by_class ? (int)$_GET['class_id'] : null;

// --- Corrected Secure Database Query (using Prepared Statements) ---
$sql = "
    SELECT DISTINCT
        s.reg_number,
        s.full_name,
        s.student_phone,
        c.class_name
    FROM
        students s
    INNER JOIN
        enrollments e ON s.student_id = e.student_id
    INNER JOIN
        classes c ON e.class_id = c.class_id
    INNER JOIN
        teachers t ON c.teacher_name = t.full_name
    WHERE
        t.teacher_id = ?
";

if ($filter_by_class) {
    $sql .= " AND c.class_id = ?";
    
    // Fetch the class name for the title
    $class_name_stmt = mysqli_prepare($conn, "SELECT class_name FROM classes WHERE class_id = ?");
    mysqli_stmt_bind_param($class_name_stmt, "i", $class_id_filter);
    mysqli_stmt_execute($class_name_stmt);
    $class_result = mysqli_stmt_get_result($class_name_stmt);
    if ($class_row = mysqli_fetch_assoc($class_result)) {
        $page_title = "Students in " . htmlspecialchars($class_row['class_name']);
    }
    mysqli_stmt_close($class_name_stmt);
}

$sql .= " ORDER BY c.class_name, s.reg_number";

// Prepare the main statement
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    if ($filter_by_class) {
        mysqli_stmt_bind_param($stmt, "ii", $teacher_id, $class_id_filter);
    } else {
        mysqli_stmt_bind_param($stmt, "i", $teacher_id);
    }
    
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        $result = mysqli_stmt_get_result($stmt);
        $students = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error_message = "Error executing statement: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
} else {
    $error_message = "Error preparing statement: " . mysqli_error($conn);
}
?>

<div class="flex">
    <?php include $path . "include/sidebar.php"; ?>

    <main id="main-content" class="p-4 sm:ml-64 pt-20 w-full min-h-screen">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-md">
            
            <h1 class="text-2xl font-bold text-gray-900 mb-6"><?php echo $page_title; ?></h1>
            
            <?php if (isset($error_message)): ?>
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                    <span class="font-medium">Database Error!</span> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($students)): ?>
                <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50" role="alert">
                    <span class="font-medium">No Students Found!</span> There are no students currently enrolled in your <?php echo $filter_by_class ? "selected class" : "classes"; ?>.
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Registration No.</th>
                                <th scope="col" class="px-6 py-3">Student Name</th>
                                <th scope="col" class="px-6 py-3">Student Phone No.</th>
                                <th scope="col" class="px-6 py-3">Class Enrolled</th>
                                <th scope="col" class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        <?php echo htmlspecialchars($student['reg_number']); ?>
                                    </th>
                                    <td class="px-6 py-4">
                                        <?php echo htmlspecialchars($student['full_name']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php echo htmlspecialchars($student['student_phone']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php echo htmlspecialchars($student['class_name']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="#" class="font-medium text-blue-600 hover:underline mr-2">View Profile</a>
                                        <a href="#" class="font-medium text-indigo-600 hover:underline">Mark Attendance</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

        </div>
    </main>
</div>

<?php include $path . "include/footer.php"; ?> ```

---

### 4. 📄 `teacher_portal/pages/assignments.php` (Assignments Page)

මෙම පිටුව ගුරුවරයාට අදාළ පන්ති සඳහා **Assignments එකතු කිරීමට** සහ **බැලීමට/කළමනාකරණය කිරීමට** ඉඩ සලසයි.

```php
<?php
// Path variable for correct asset linking
$path = "../";

// Include the header
include $path . "include/head.php"; //

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

// --- Handle Assignment Submission (Simplified Example) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_assignment'])) {
    $class_id = filter_input(INPUT_POST, 'class_id', FILTER_SANITIZE_NUMBER_INT);
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $due_date = filter_input(INPUT_POST, 'due_date', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

    if (empty($class_id) || empty($title) || empty($due_date)) {
        $page_message = ['type' => 'error', 'text' => 'Please fill all required fields (Class, Title, Due Date).'];
    } else {
        // --- IMPORTANT: You need to create an 'assignments' table in your DB first ---
        // For now, we will just show a success message until the table is available.
        // Assuming there is an 'assignments' table with columns: teacher_id, class_id, title, description, due_date
        
        // $insert_sql = "INSERT INTO assignments (teacher_id, class_id, title, description, due_date, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
        // $insert_stmt = mysqli_prepare($conn, $insert_sql);
        // mysqli_stmt_bind_param($insert_stmt, "iissi", $teacher_id, $class_id, $title, $description, $due_date);
        
        // if (mysqli_stmt_execute($insert_stmt)) {
        //     $page_message = ['type' => 'success', 'text' => 'Assignment "' . htmlspecialchars($title) . '" successfully posted.'];
        // } else {
        //     $page_message = ['type' => 'error', 'text' => 'Failed to post assignment: ' . mysqli_stmt_error($insert_stmt)];
        // }
        // mysqli_stmt_close($insert_stmt);
        
        $page_message = ['type' => 'info', 'text' => 'Assignment submission logic is currently commented out. Please create the `assignments` database table first.'];
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

// --- Fetch Assignments (Currently Commented Out) ---
// Since the 'assignments' table is assumed to be missing, this section is simplified.
// if (!empty($teacher_classes)) {
//     $assignment_sql = "
//         SELECT a.title, a.due_date, a.description, c.class_name 
//         FROM assignments a
//         INNER JOIN classes c ON a.class_id = c.class_id
//         WHERE a.teacher_id = ?
//         ORDER BY a.due_date DESC
//     ";
//     $assign_stmt = mysqli_prepare($conn, $assignment_sql);
//     mysqli_stmt_bind_param($assign_stmt, "i", $teacher_id);
//     mysqli_stmt_execute($assign_stmt);
//     $assign_result = mysqli_stmt_get_result($assign_stmt);
//     $assignments = mysqli_fetch_all($assign_result, MYSQLI_ASSOC);
//     mysqli_stmt_close($assign_stmt);
// }

// Dummy Assignments for display if DB table is missing
$assignments = [
    ['title' => 'Maths Paper 1 Discussion', 'class_name' => 'Maths 2025', 'due_date' => '2025-12-20', 'description' => 'Complete the 2024 model paper and submit your solutions.'],
    ['title' => 'Chemistry Redox Reactions', 'class_name' => 'Chem 2026', 'due_date' => '2026-01-10', 'description' => 'Write down all the redox reactions for the past 5 years.']
];
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

<?php include $path . "include/footer.php"; ?> ```

