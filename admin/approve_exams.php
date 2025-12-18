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
    exit();
}

// Reject Logic
if (isset($_GET['reject_id'])) {
    $id = intval($_GET['reject_id']);
    $conn->query("UPDATE online_exams SET approval_status = 'Rejected' WHERE exam_id = $id");
    header("Location: approve_exams.php?msg=Exam Rejected");
    exit();
}

// Delete Logic
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    
    // Exam එක Database එකෙන් ඉවත් කිරීම
    $stmt = $conn->prepare("DELETE FROM online_exams WHERE exam_id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: approve_exams.php?msg=Exam Deleted Successfully");
    } else {
        header("Location: approve_exams.php?error=Error deleting exam");
    }
    exit();
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

            <?php if(isset($_GET['msg'])): ?>
                <div id="alert-box" class="bg-green-100 text-green-700 p-3 rounded mb-4 border border-green-200 flex justify-between items-center transition-opacity duration-500">
                    <span><?php echo htmlspecialchars($_GET['msg']); ?></span>
                    <button onclick="document.getElementById('alert-box').style.display='none'" class="text-green-900 font-bold">&times;</button>
                </div>
            <?php endif; ?>

            <?php if(isset($_GET['error'])): ?>
                <div id="alert-box" class="bg-red-100 text-red-700 p-3 rounded mb-4 border border-red-200 flex justify-between items-center transition-opacity duration-500">
                    <span><?php echo htmlspecialchars($_GET['error']); ?></span>
                    <button onclick="document.getElementById('alert-box').style.display='none'" class="text-red-900 font-bold">&times;</button>
                </div>
            <?php endif; ?>

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
                        
                        if ($result->num_rows > 0):
                            while ($row = $result->fetch_assoc()):
                        ?>
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="py-3 px-6 font-medium"><?php echo htmlspecialchars($row['title']); ?></td>
                            <td class="py-3 px-6"><?php echo htmlspecialchars($row['stream']); ?></td>
                            <td class="py-3 px-6"><?php echo htmlspecialchars($row['teacher_id']); ?></td>
                            <td class="py-3 px-6">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold 
                                    <?php echo ($row['approval_status']=='Pending') ? 'bg-yellow-100 text-yellow-800' : 
                                          (($row['approval_status']=='Approved') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                    <?php echo $row['approval_status']; ?>
                                </span>
                            </td>
                            <td class="py-3 px-6 flex items-center gap-2">
                                <?php if($row['approval_status'] == 'Pending'): ?>
                                    <a href="?approve_id=<?php echo $row['exam_id']; ?>" 
                                       class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600 transition shadow" title="Approve">
                                       <i class="fas fa-check"></i>
                                    </a>
                                    <a href="?reject_id=<?php echo $row['exam_id']; ?>" 
                                       class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600 transition shadow" 
                                       onclick="return confirm('Reject this exam?')" title="Reject">
                                       <i class="fas fa-times"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400 text-sm italic mr-2">Processed</span>
                                <?php endif; ?>

                                <a href="?delete_id=<?php echo $row['exam_id']; ?>" 
                                   class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition shadow" 
                                   onclick="return confirm('⚠️ Are you sure you want to PERMANENTLY DELETE this exam? This action cannot be undone.');" 
                                   title="Delete Permanently">
                                   <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <tr><td colspan="5" class="py-6 text-center text-gray-500">No exams found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // පිටුව ලෝඩ් වී තත්පර 3 කට (3000 ms) පසු මෙම function එක ක්‍රියාත්මක වේ
        setTimeout(function() {
            var alertBox = document.getElementById('alert-box');
            if (alertBox) {
                // Smooth fade out effect එකක් සඳහා (Optional style changes)
                alertBox.style.transition = "opacity 0.5s ease";
                alertBox.style.opacity = "0";
                
                // Opacity එක 0 වූ පසු සම්පූර්ණයෙන්ම ඉවත් කිරීමට තවත් කුඩා වේලාවක් තබයි
                setTimeout(function(){
                    alertBox.style.display = 'none';
                }, 500); 
            }
        }, 3000);
    </script>
</body>
</html>