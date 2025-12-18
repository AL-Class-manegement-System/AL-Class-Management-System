<?php
// student_portal/pages/my_classes.php
// Updated: Shows 'Enrolled' status if Payment is Approved by Admin

include('../includes/student_header.php');

// ==========================================
// 1. Get Student Data & Stream
// ==========================================
$db_student_id = $student['student_id'];
$my_stream_code = $student['stream']; 

// Stream Mapping
$stream_map = [
    'Maths'    => 'Physical Science',
    'Bio'      => 'Bio Science',
    'Tech'     => 'Technology',
    'Art'      => 'Arts',
    'Commerce' => 'Commerce'
];

$filter_stream = isset($stream_map[$my_stream_code]) ? $stream_map[$my_stream_code] : $my_stream_code;

// ==========================================
// 2. Handle Actions (Unenroll)
// ==========================================
$message = "";
$msg_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['class_id'])) {
    
    $class_id = intval($_POST['class_id']);
    $action = $_POST['action'];

    if ($action == 'unenroll') {
        // Enrollments table එකෙන් ඉවත් කිරීම
        $del_sql = "DELETE FROM enrollments WHERE student_id = ? AND class_id = ?";
        $del_stmt = $conn->prepare($del_sql);
        $del_stmt->bind_param("ii", $db_student_id, $class_id);
        
        if ($del_stmt->execute()) {
            $message = "Successfully unenrolled from the class.";
            $msg_type = "red";
        } else {
            $message = "Error unenrolling.";
            $msg_type = "red";
        }
        $del_stmt->close();
    }
}

// ==========================================
// 3. Get IDs of Currently Enrolled Classes (From enrollments table)
// ==========================================
$enrolled_classes = [];
$enr_sql = "SELECT class_id FROM enrollments WHERE student_id = ?";
$enr_stmt = $conn->prepare($enr_sql);
$enr_stmt->bind_param("i", $db_student_id);
$enr_stmt->execute();
$enr_res = $enr_stmt->get_result();
while ($row = $enr_res->fetch_assoc()) {
    $enrolled_classes[] = $row['class_id'];
}
$enr_stmt->close();

// ==========================================
// 4. Get IDs of Paid/Approved Classes (From payments table)
// ==========================================
// Admin විසින් Approve කරන ලද (Paid) පන්ති ද Enrolled ලෙස සැලකීමට මෙය භාවිතා කරයි.
$paid_classes = [];
$paid_sql = "SELECT class_id FROM payments WHERE student_id = ? AND (payment_status = 'paid' OR payment_status = 'approved')";
$paid_stmt = $conn->prepare($paid_sql);
$paid_stmt->bind_param("i", $db_student_id);
$paid_stmt->execute();
$paid_res = $paid_stmt->get_result();
while ($row = $paid_res->fetch_assoc()) {
    $paid_classes[] = $row['class_id'];
}
$paid_stmt->close();

// ==========================================
// 5. Get IDs of Pending Approvals
// ==========================================
$pending_classes = [];
$pen_sql = "SELECT class_id FROM payments WHERE student_id = ? AND payment_status = 'pending'";
$pen_stmt = $conn->prepare($pen_sql);
$pen_stmt->bind_param("i", $db_student_id);
$pen_stmt->execute();
$pen_res = $pen_stmt->get_result();
while ($row = $pen_res->fetch_assoc()) {
    $pending_classes[] = $row['class_id'];
}
$pen_stmt->close();
?>

<div class="flex-1 flex flex-col h-screen overflow-y-auto bg-gray-50">
    <main class="p-4 md:p-8">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">My Classes 📚</h1>
                <p class="text-slate-500 flex items-center gap-2 mt-1">
                    Showing classes for: 
                    <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-1 rounded-full">
                        <?php echo htmlspecialchars($filter_stream); ?> Stream & ICT
                    </span>
                </p>
            </div>
            
            <?php if ($message): 
                $colorClass = ($msg_type == 'green') ? 'bg-green-100 text-green-700 border-green-200' : 
                              (($msg_type == 'yellow') ? 'bg-yellow-100 text-yellow-700 border-yellow-200' : 'bg-red-100 text-red-700 border-red-200');
            ?>
            <div id="msg" class="<?php echo $colorClass; ?> px-4 py-3 rounded-xl text-sm font-medium mt-4 md:mt-0 shadow-sm border flex items-center gap-2 animate-pulse">
                <i class="fas fa-info-circle"></i> <?php echo $message; ?>
            </div>
            <script>
                setTimeout(() => {
                    const msg = document.getElementById('msg');
                    if(msg) { msg.style.transition = "opacity 0.5s"; msg.style.opacity = "0"; setTimeout(() => msg.remove(), 500); }
                }, 3000);
            </script>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 pb-20">

            <?php
            // Filter Classes
            $sql = "SELECT * FROM classes WHERE status = 1 AND (stream = ? OR stream = 'ICT') ORDER BY day ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $filter_stream);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $class_id = $row['class_id'];
                    
                    // Status Checking Logic
                    // 1. Enrollment table එකේ හෝ Payment 'paid' නම් Enrolled ලෙස සලකන්න.
                    $is_enrolled_officially = in_array($class_id, $enrolled_classes);
                    $is_payment_approved = in_array($class_id, $paid_classes);
                    
                    $show_enrolled = $is_enrolled_officially || $is_payment_approved;
                    
                    // 2. Pending Check
                    $is_pending = in_array($class_id, $pending_classes);

                    // Colors
                    $badge_color = 'bg-indigo-100 text-indigo-600'; 
                    $btn_color = 'bg-indigo-600 hover:bg-indigo-700';

                    if($row['stream'] == 'Bio Science') { $badge_color = 'bg-green-100 text-green-600'; $btn_color = 'bg-green-600 hover:bg-green-700'; }
                    if($row['stream'] == 'Commerce') { $badge_color = 'bg-blue-100 text-blue-600'; $btn_color = 'bg-blue-600 hover:bg-blue-700'; }
                    if($row['stream'] == 'Technology') { $badge_color = 'bg-purple-100 text-purple-600'; $btn_color = 'bg-purple-600 hover:bg-purple-700'; }
                    if($row['stream'] == 'Arts') { $badge_color = 'bg-pink-100 text-pink-600'; $btn_color = 'bg-pink-600 hover:bg-pink-700'; }
                    if($row['stream'] == 'ICT') { $badge_color = 'bg-gray-100 text-gray-600'; $btn_color = 'bg-gray-700 hover:bg-gray-800'; }
            ?>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 flex flex-col h-full relative overflow-hidden group">
                
                <?php if($show_enrolled): ?>
                    <div class="absolute top-0 right-0 bg-emerald-500 text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl shadow-sm z-10 uppercase tracking-wide">
                        <i class="fas fa-check-circle mr-1"></i> Enrolled
                    </div>
                <?php elseif($is_pending): ?>
                    <div class="absolute top-0 right-0 bg-yellow-400 text-yellow-900 text-[10px] font-bold px-3 py-1 rounded-bl-xl shadow-sm z-10 uppercase tracking-wide animate-pulse">
                        <i class="fas fa-clock mr-1"></i> Pending
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <span class="<?php echo $badge_color; ?> text-[10px] font-bold px-2.5 py-1 rounded-lg mb-2 inline-block uppercase tracking-wider">
                        <?php echo htmlspecialchars($row['stream']); ?>
                    </span>
                    <h3 class="text-lg font-bold text-slate-800 line-clamp-1" title="<?php echo htmlspecialchars($row['subject']); ?>">
                        <?php echo htmlspecialchars($row['subject']); ?>
                    </h3>
                    <p class="text-xs text-slate-500 font-medium truncate mt-1"><?php echo htmlspecialchars($row['class_name']); ?></p>
                </div>

                <div class="space-y-3 mb-6 flex-grow border-t border-dashed border-gray-100 pt-4">
                    <div class="flex items-center text-sm text-slate-600">
                        <div class="w-8 flex justify-center"><i class="fas fa-chalkboard-teacher text-slate-400"></i></div>
                        <span class="truncate font-medium"><?php echo htmlspecialchars($row['teacher_name']); ?></span>
                    </div>
                    <div class="flex items-center text-sm text-slate-600">
                        <div class="w-8 flex justify-center"><i class="far fa-calendar-alt text-slate-400"></i></div>
                        <span><?php echo htmlspecialchars($row['day']); ?></span>
                    </div>
                    <div class="flex items-center text-sm text-slate-600">
                        <div class="w-8 flex justify-center"><i class="far fa-clock text-slate-400"></i></div>
                        <span class="text-xs"><?php echo htmlspecialchars($row['time']); ?></span>
                    </div>
                    <div class="flex items-center text-sm text-slate-600">
                        <div class="w-8 flex justify-center"><i class="fas fa-tag text-slate-400"></i></div>
                        <span class="font-bold text-slate-800">LKR <?php echo number_format($row['fee'], 2); ?></span>
                    </div>
                </div>

                <div class="mt-auto">
                    
                    <?php if($show_enrolled): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                            <input type="hidden" name="action" value="unenroll">
                            <button type="submit" onclick="return confirm('Are you sure you want to unenroll from this class?')" 
                                class="w-full py-2.5 rounded-xl border border-red-100 text-red-500 text-sm font-bold hover:bg-red-50 hover:text-red-600 transition-colors flex items-center justify-center gap-2 group-hover:border-red-200">
                                <i class="fas fa-sign-out-alt"></i> Unenroll
                            </button>
                        </form>

                    <?php elseif($is_pending): ?>
                        <button disabled class="w-full py-2.5 rounded-xl bg-yellow-100 text-yellow-700 text-sm font-bold cursor-not-allowed flex items-center justify-center gap-2 border border-yellow-200">
                            <i class="fas fa-hourglass-half"></i> Approval Pending
                        </button>

                    <?php else: ?>
                        <a href="enroll_class.php?class_id=<?php echo $class_id; ?>" 
                            class="w-full py-2.5 rounded-xl <?php echo $btn_color; ?> text-white text-sm font-bold shadow-lg shadow-indigo-500/20 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2 text-center decoration-0">
                            Enroll Now <i class="fas fa-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                    
                </div>

            </div>

            <?php 
                } 
            } else {
                echo '<div class="col-span-full py-20 text-center text-gray-400">No classes found.</div>';
            }
            $stmt->close();
            ?>
        </div>
    </main>
</div>

<?php include('../includes/footer.php'); ?>