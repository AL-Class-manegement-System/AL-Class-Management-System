<?php
// student_portal/pages/live_class.php
session_start();
include('../includes/student_header.php');
include('../../includes/connection.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: ../../log/login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// 1. සිසුවා Active වී ඇති පන්ති වල IDs ලබා ගැනීම
$enrolled_ids = [];
$e_sql = "SELECT class_id FROM enrollments WHERE student_id = ? AND status = 1";
$stmt = $conn->prepare($e_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$res = $stmt->get_result();
while($r = $res->fetch_assoc()){ $enrolled_ids[] = $r['class_id']; }

$live_classes = [];

if (!empty($enrolled_ids)) {
    $ids_str = implode(',', $enrolled_ids);
    
    // 2. අදාළ පන්ති වල Approved Live Classes/Recordings ලබා ගැනීම
    $sql = "SELECT l.*, c.class_name, c.subject 
            FROM live_classes l 
            JOIN classes c ON l.class_id = c.class_id 
            WHERE l.class_id IN ($ids_str) AND l.status = 1 
            ORDER BY l.start_time DESC";
            
    $live_classes = $conn->query($sql);
}
?>

<div class="flex-1 flex flex-col h-screen overflow-y-auto bg-gray-50">
    <main class="p-4 md:p-8">
        <h1 class="text-3xl font-bold text-slate-800 mb-2">Live Classes & Recordings 🔴</h1>
        <p class="text-slate-500 mb-8">Join live sessions or watch past recordings.</p>

        <?php if (!empty($live_classes) && $live_classes->num_rows > 0): ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php while ($row = $live_classes->fetch_assoc()): 
                    $is_live = ($row['type'] == 'Live');
                    $card_color = $is_live ? 'border-red-200' : 'border-blue-200';
                    $bg_badge = $is_live ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700';
                    $icon = $is_live ? 'fa-video' : 'fa-play-circle';
                ?>
                <div class="bg-white rounded-xl shadow-sm border <?php echo $card_color; ?> p-6 hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-4">
                        <span class="<?php echo $bg_badge; ?> px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                            <?php echo $is_live ? 'Live Session' : 'Recording'; ?>
                        </span>
                        <span class="text-xs text-slate-400 font-medium">
                            <?php echo date('M d, h:i A', strtotime($row['start_time'])); ?>
                        </span>
                    </div>
                    
                    <h3 class="text-lg font-bold text-slate-800 mb-1"><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p class="text-sm text-slate-500 mb-4"><?php echo htmlspecialchars($row['subject']) . " - " . htmlspecialchars($row['class_name']); ?></p>
                    
                    <a href="<?php echo htmlspecialchars($row['class_link']); ?>" target="_blank" 
                       class="block w-full text-center py-3 rounded-lg font-bold text-white transition 
                       <?php echo $is_live ? 'bg-red-500 hover:bg-red-600 shadow-red-200' : 'bg-blue-500 hover:bg-blue-600 shadow-blue-200'; ?> shadow-lg">
                        <i class="fas <?php echo $icon; ?> mr-2"></i> 
                        <?php echo $is_live ? 'Join Now' : 'Watch Recording'; ?>
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-20 bg-white rounded-xl border border-dashed border-gray-300">
                <i class="fas fa-video-slash text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No active live classes or recordings available for your enrolled subjects.</p>
            </div>
        <?php endif; ?>
    </main>
</div>
<?php include('../includes/footer.php'); ?>