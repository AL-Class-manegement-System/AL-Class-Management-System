<?php
// admin/approve_exams.php
session_start();
include('includes/auth.php');
include('db_con.php');

// Approve Logic
if (isset($_GET['approve_id'])) {
    $id = intval($_GET['approve_id']);
    $conn->query("UPDATE online_exams SET approval_status = 'Approved' WHERE exam_id = $id");
    header("Location: approve_exams.php?msg=Exam Approved");
}

// Reject Logic
if (isset($_GET['reject_id'])) {
    $id = intval($_GET['reject_id']);
    $conn->query("UPDATE online_exams SET approval_status = 'Rejected' WHERE exam_id = $id");
    header("Location: approve_exams.php?msg=Exam Rejected");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Exams</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex">
        <?php include('includes/sidebar.php'); ?>
        <div class="ml-64 flex-1 p-8">
            <h1 class="text-3xl font-bold mb-6">✅ Exam Approvals</h1>

            <?php if(isset($_GET['msg'])) echo "<div class='bg-green-100 text-green-700 p-3 rounded mb-4'>".$_GET['msg']."</div>"; ?>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-6 text-left">Title</th>
                            <th class="py-3 px-6 text-left">Stream</th>
                            <th class="py-3 px-6 text-left">Teacher ID</th>
                            <th class="py-3 px-6 text-left">Status</th>
                            <th class="py-3 px-6 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php
                        $sql = "SELECT * FROM online_exams ORDER BY created_at DESC";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()):
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-6"><?php echo htmlspecialchars($row['title']); ?></td>
                            <td class="py-3 px-6"><?php echo htmlspecialchars($row['stream']); ?></td>
                            <td class="py-3 px-6"><?php echo htmlspecialchars($row['teacher_id']); ?></td>
                            <td class="py-3 px-6">
                                <span class="px-2 py-1 rounded text-xs font-bold 
                                    <?php echo ($row['approval_status']=='Pending') ? 'bg-yellow-100 text-yellow-800' : 
                                          (($row['approval_status']=='Approved') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                    <?php echo $row['approval_status']; ?>
                                </span>
                            </td>
                            <td class="py-3 px-6">
                                <?php if($row['approval_status'] == 'Pending'): ?>
                                    <a href="?approve_id=<?php echo $row['exam_id']; ?>" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 mr-2">Approve</a>
                                    <a href="?reject_id=<?php echo $row['exam_id']; ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" onclick="return confirm('Reject this exam?')">Reject</a>
                                <?php else: ?>
                                    <span class="text-gray-400">Processed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>