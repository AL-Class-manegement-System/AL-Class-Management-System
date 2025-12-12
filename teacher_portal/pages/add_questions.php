<?php
// teacher_portal/pages/add_questions.php
// FINAL VERSION: Security + Image + OCR + RichText + Preview Step + Bulk Delete

include('../include/head.php');
require_once '../../includes/connection.php';

// =========================================================
// 1. SECURITY & INITIALIZATION
// =========================================================

if (!isset($_SESSION['teacher_id']) || !isset($_SESSION['teacher_db_id'])) {
    header("Location: ../../log/login.php");
    exit();
}

$teacher_db_id = $_SESSION['teacher_db_id']; 
$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;

// Security Check
$check_sql = "SELECT exam_id FROM online_exams WHERE exam_id = ? AND teacher_id = ?";
$stmt_check = $conn->prepare($check_sql);
$stmt_check->bind_param("ii", $exam_id, $teacher_db_id);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows === 0) {
    echo "<script>alert('Access Denied!'); window.location.href='create_exam.php';</script>";
    exit();
}
$stmt_check->close();

$msg = "";
$msg_type = "";
$show_preview = false; // Flag to trigger preview mode
$preview_data = [];    // Array to hold parsed questions for preview

// Helper Functions
function getQuestionCount($conn, $exam_id) {
    $result = $conn->query("SELECT COUNT(*) as count FROM exam_questions WHERE exam_id = $exam_id");
    return $result->fetch_assoc()['count'];
}

function uploadImage($file) {
    if(isset($file) && $file['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if(!in_array($ext, $allowed)) return ["error" => "Invalid file type."];
        if($file['size'] > 2 * 1024 * 1024) return ["error" => "Max 2MB."];

        $upload_dir = "../../assets/images/questions/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $new_filename = uniqid("q_") . "." . $ext;
        if(move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
            return ["path" => "assets/images/questions/" . $new_filename];
        }
    }
    return null;
}

// =========================================================
// 2. ACTION HANDLERS
// =========================================================

// --- A. BULK DELETE ---
if (isset($_POST['delete_selected'])) {
    if(!empty($_POST['selected_ids'])) {
        $ids = array_map('intval', $_POST['selected_ids']);
        $ids_str = implode(',', $ids);
        
        // Delete images first (Optional)
        $res = $conn->query("SELECT image_path FROM exam_questions WHERE question_id IN ($ids_str) AND exam_id = $exam_id");
        while($row = $res->fetch_assoc()) {
            if($row['image_path'] && file_exists("../../".$row['image_path'])) unlink("../../".$row['image_path']);
        }
        
        // Delete Records
        if($conn->query("DELETE FROM exam_questions WHERE question_id IN ($ids_str) AND exam_id = $exam_id")) {
            $msg = count($ids) . " questions deleted successfully.";
            $msg_type = "green";
        }
    } else {
        $msg = "No questions selected for deletion.";
        $msg_type = "red";
    }
}

// --- B. CONFIRM IMPORT (AFTER PREVIEW) ---
if (isset($_POST['confirm_import'])) {
    $imported = 0;
    $current_total = getQuestionCount($conn, $exam_id);
    
    // Iterate through posted array
    if (isset($_POST['imp_q'])) {
        $stmt = $conn->prepare("INSERT INTO exam_questions (exam_id, question_text, option_1, option_2, option_3, option_4, option_5, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($_POST['imp_q'] as $key => $q_text) {
            if ($current_total >= 50) break;
            $q_text = trim($q_text);
            if(empty($q_text)) continue;

            $o1 = $_POST['imp_o1'][$key] ?? '-';
            $o2 = $_POST['imp_o2'][$key] ?? '-';
            $o3 = $_POST['imp_o3'][$key] ?? '-';
            $o4 = $_POST['imp_o4'][$key] ?? '-';
            $o5 = $_POST['imp_o5'][$key] ?? '-';
            $ans = intval($_POST['imp_ans'][$key] ?? 0);

            $stmt->bind_param("issssssi", $exam_id, $q_text, $o1, $o2, $o3, $o4, $o5, $ans);
            if ($stmt->execute()) {
                $imported++;
                $current_total++;
            }
        }
        $msg = "Successfully imported $imported questions!";
        $msg_type = "green";
    }
}

// --- C. PREPARE PREVIEW (SMART IMPORT) ---
if (isset($_POST['bulk_import'])) {
    $raw_text = $_POST['bulk_text'];
    $ans_text = $_POST['bulk_answers'];
    
    // Parse Answers
    $extracted_answers = [];
    if(preg_match_all('/\b(\d+)[\s\.\)\:\-]+([a-eA-E1-5])\b/i', $ans_text, $matches, PREG_SET_ORDER)) {
        foreach($matches as $m) {
            $val_char = strtolower($m[2]);
            $map = ['a'=>1, 'b'=>2, 'c'=>3, 'd'=>4, 'e'=>5, '1'=>1, '2'=>2, '3'=>3, '4'=>4, '5'=>5];
            if(isset($map[$val_char])) $extracted_answers[intval($m[1])] = $map[$val_char];
        }
    }

    // Parse Questions
    $raw_text = preg_replace('/(\s+[\(]?[2-5][\)\.])/', "\n$1", $raw_text);
    $lines = preg_split('/\r\n|\r|\n/', $raw_text);
    
    $current_q = [];
    $temp_preview = [];
    
    foreach ($lines as $line) {
        $line = trim($line); if (empty($line)) continue;
        
        if (preg_match('/^\s*(Q?\d+[\.)\s]|Question\s\d+)\s*[:\.]?\s*(.*)/i', $line, $matches)) {
            if (!empty($current_q)) { $temp_preview[] = $current_q; }
            
            $q_num = intval(preg_replace('/[^0-9]/', '', $matches[1]));
            $ans = isset($extracted_answers[$q_num]) ? $extracted_answers[$q_num] : 0;
            $current_q = ['text' => $matches[2], 'options' => [], 'answer' => $ans];
            
        } elseif (preg_match('/^\s*[\(]?([a-eA-E1-5])[\)\.\-]\s*(.*)/', $line, $matches)) {
            $current_q['options'][] = $matches[2];
        } elseif (!empty($current_q) && empty($current_q['options'])) { 
            $current_q['text'] .= " " . $line; 
        }
    }
    if (!empty($current_q)) { $temp_preview[] = $current_q; }
    
    if(!empty($temp_preview)) {
        $show_preview = true;
        $preview_data = $temp_preview;
        $msg = "Please review the data below and click 'Confirm Import'.";
        $msg_type = "blue";
    } else {
        $msg = "No questions detected. Please check your text format.";
        $msg_type = "red";
    }
}

// --- D. MANUAL ADD / UPDATE ---
if (isset($_POST['add_q']) || isset($_POST['update_q'])) {
    $is_update = isset($_POST['update_q']);
    $q_text = $_POST['question'];
    $opt1 = trim($_POST['opt1']); $opt2 = trim($_POST['opt2']);
    $opt3 = trim($_POST['opt3']); $opt4 = trim($_POST['opt4']);
    $opt5 = trim($_POST['opt5']); 
    $correct = isset($_POST['correct']) ? intval($_POST['correct']) : 0;
    
    $image_path = null;
    if(isset($_FILES['q_image']) && $_FILES['q_image']['error'] == 0){
        $res = uploadImage($_FILES['q_image']);
        if(isset($res['error'])) { $msg = $res['error']; $msg_type = "red"; }
        else { $image_path = $res['path']; }
    }

    if(empty($msg)) {
        if ($is_update) {
            $qid = intval($_POST['question_id']);
            $sql = "UPDATE exam_questions SET question_text=?, option_1=?, option_2=?, option_3=?, option_4=?, option_5=?, correct_option=?";
            $params = [$q_text, $opt1, $opt2, $opt3, $opt4, $opt5, $correct];
            $types = "ssssssi";
            if($image_path) { $sql .= ", image_path=?"; $params[] = $image_path; $types .= "s"; }
            $sql .= " WHERE question_id=?"; $params[] = $qid; $types .= "i";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            if($stmt->execute()) { header("Location: add_questions.php?exam_id=$exam_id&msg=Updated"); exit(); }
        } else {
            if(getQuestionCount($conn, $exam_id) < 50) {
                $sql = "INSERT INTO exam_questions (exam_id, question_text, option_1, option_2, option_3, option_4, option_5, correct_option, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("issssssis", $exam_id, $q_text, $opt1, $opt2, $opt3, $opt4, $opt5, $correct, $image_path);
                if($stmt->execute()) { $msg = "Added!"; $msg_type = "green"; }
            } else { $msg = "Limit Reached."; $msg_type = "red"; }
        }
    }
}

// Delete Single
if(isset($_GET['del_q'])) {
    $conn->query("DELETE FROM exam_questions WHERE question_id=".intval($_GET['del_q']));
    header("Location: add_questions.php?exam_id=$exam_id&msg=Deleted"); exit();
}

$edit_data = null;
if (isset($_GET['edit_q'])) {
    $res = $conn->query("SELECT * FROM exam_questions WHERE question_id=".intval($_GET['edit_q'])." AND exam_id=$exam_id");
    if ($res->num_rows > 0) $edit_data = $res->fetch_assoc();
}

if (isset($_POST['finish'])) { echo "<script>alert('Saved!'); window.location.href='create_exam.php';</script>"; }
$total_q = getQuestionCount($conn, $exam_id);
?>

<?php include("../include/sidebar.php"); ?>

<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>window.MathJax = { tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] }, svg: { fontCache: 'global' } };</script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<script src="https://unpkg.com/tesseract.js@v2.1.0/dist/tesseract.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
<script>pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';</script>

<div class="p-4 sm:ml-64 pb-20">
    <div class="flex justify-between items-center mb-6 bg-white p-4 rounded-lg shadow-sm border">
        <h2 class="text-2xl font-bold text-gray-800">Exam Management <span class="text-sm font-normal text-gray-500">ID: #<?php echo $exam_id; ?></span></h2>
        <span class="text-3xl font-bold <?php echo ($total_q >= 50) ? 'text-red-500' : 'text-green-500'; ?>"><?php echo $total_q; ?>/50</span>
    </div>

    <?php if($msg || isset($_GET['msg'])): $m = $msg ?: $_GET['msg']; $t = $msg_type ?: 'green'; ?>
        <div class="bg-<?php echo $t; ?>-100 text-<?php echo $t; ?>-700 p-4 mb-6 rounded shadow-sm"><?php echo htmlspecialchars($m); ?></div>
    <?php endif; ?>

    <?php if ($show_preview): ?>
        <div class="bg-white border border-blue-200 rounded-lg shadow-lg p-6 mb-8 ring-2 ring-blue-100">
            <h3 class="font-bold text-xl text-blue-800 mb-4 flex items-center"><i class="ph ph-eye mr-2"></i> Review & Confirm Import</h3>
            <p class="text-sm text-gray-600 mb-4">Please review the extracted questions below. Edit any errors before saving.</p>
            
            <form method="POST">
                <div class="space-y-6">
                    <?php foreach ($preview_data as $idx => $q): ?>
                        <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                            <div class="flex gap-2 mb-2">
                                <span class="font-bold text-gray-500 pt-2">Q<?php echo $idx+1; ?></span>
                                <textarea name="imp_q[]" class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" rows="2" required><?php echo htmlspecialchars($q['text']); ?></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 ml-8">
                                <input type="text" name="imp_o1[]" value="<?php echo htmlspecialchars($q['options'][0] ?? '-'); ?>" class="p-2 border rounded text-sm" placeholder="Option 1">
                                <input type="text" name="imp_o2[]" value="<?php echo htmlspecialchars($q['options'][1] ?? '-'); ?>" class="p-2 border rounded text-sm" placeholder="Option 2">
                                <input type="text" name="imp_o3[]" value="<?php echo htmlspecialchars($q['options'][2] ?? '-'); ?>" class="p-2 border rounded text-sm" placeholder="Option 3">
                                <input type="text" name="imp_o4[]" value="<?php echo htmlspecialchars($q['options'][3] ?? '-'); ?>" class="p-2 border rounded text-sm" placeholder="Option 4">
                                <input type="text" name="imp_o5[]" value="<?php echo htmlspecialchars($q['options'][4] ?? '-'); ?>" class="p-2 border rounded text-sm" placeholder="Option 5">
                                <select name="imp_ans[]" class="p-2 border rounded bg-yellow-50 text-sm font-bold">
                                    <option value="0">No Ans</option>
                                    <?php for($k=1;$k<=5;$k++): ?>
                                        <option value="<?php echo $k; ?>" <?php echo ($q['answer']==$k)?'selected':''; ?>>Ans: <?php echo $k; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="flex gap-4 mt-6 sticky bottom-4 bg-white p-4 border-t shadow-md z-10">
                    <button type="submit" name="confirm_import" class="flex-1 bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700">Confirm & Save All</button>
                    <a href="add_questions.php?exam_id=<?php echo $exam_id; ?>" class="flex-1 bg-gray-500 text-white py-3 rounded-lg font-bold hover:bg-gray-600 text-center">Cancel</a>
                </div>
            </form>
        </div>
    <?php else: ?>

        <div class="mb-6 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                <li class="mr-2"><button onclick="switchTab('manual')" id="tab-btn-manual" class="inline-block p-4 border-b-2 rounded-t-lg text-indigo-600 border-indigo-600 active-tab">Manual Entry</button></li>
                <li class="mr-2"><button onclick="switchTab('import')" id="tab-btn-import" class="inline-block p-4 border-b-2 border-transparent hover:text-gray-600">Smart Import</button></li>
            </ul>
        </div>

        <div id="tab-content-manual" class="tab-content">
            <?php if ($total_q < 50 || $edit_data): ?>
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-8">
                <form method="POST" enctype="multipart/form-data">
                    <?php if($edit_data): ?><input type="hidden" name="question_id" value="<?php echo $edit_data['question_id']; ?>"><?php endif; ?>
                    <div class="mb-4">
                        <label class="block mb-2 font-medium text-sm">Question Text</label>
                        <textarea name="question" id="editor" rows="3"><?php echo $edit_data ? $edit_data['question_text'] : ''; ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block mb-2 text-sm">Image</label>
                        <input type="file" name="q_image" accept="image/*" class="w-full text-sm border rounded p-1">
                        <?php if($edit_data && $edit_data['image_path']): ?><img src="../../<?php echo htmlspecialchars($edit_data['image_path']); ?>" class="h-20 mt-2 rounded border"><?php endif; ?>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <input type="text" name="opt1" value="<?php echo $edit_data ? htmlspecialchars($edit_data['option_1']) : ''; ?>" placeholder="Option 1" required class="p-2 border rounded">
                        <input type="text" name="opt2" value="<?php echo $edit_data ? htmlspecialchars($edit_data['option_2']) : ''; ?>" placeholder="Option 2" required class="p-2 border rounded">
                        <input type="text" name="opt3" value="<?php echo $edit_data ? htmlspecialchars($edit_data['option_3']) : ''; ?>" placeholder="Option 3" required class="p-2 border rounded">
                        <input type="text" name="opt4" value="<?php echo $edit_data ? htmlspecialchars($edit_data['option_4']) : ''; ?>" placeholder="Option 4" required class="p-2 border rounded">
                        <input type="text" name="opt5" value="<?php echo $edit_data ? htmlspecialchars($edit_data['option_5']) : ''; ?>" placeholder="Option 5" required class="p-2 border rounded md:col-span-2">
                    </div>
                    <div class="mb-4 w-1/3">
                        <select name="correct" class="w-full p-2 border rounded">
                            <option value="0">Select Answer</option>
                            <?php for($k=1;$k<=5;$k++): ?>
                                <option value="<?php echo $k; ?>" <?php echo ($edit_data && $edit_data['correct_option']==$k)?'selected':''; ?>>Option <?php echo $k; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" name="<?php echo $edit_data ? 'update_q' : 'add_q'; ?>" class="bg-indigo-600 text-white px-6 py-2 rounded"><?php echo $edit_data ? 'Update' : 'Add'; ?></button>
                    <?php if($edit_data): ?> <a href="add_questions.php?exam_id=<?php echo $exam_id; ?>" class="ml-2 text-gray-500">Cancel</a> <?php endif; ?>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <div id="tab-content-import" class="tab-content hidden">
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-8">
                <form method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-4 bg-gray-50 border rounded-lg">
                            <label class="block mb-2 font-bold text-gray-700">1. Question Paper</label>
                            <input type="file" id="ocrFileQ" accept=".pdf, .png, .jpg" class="block w-full text-sm mb-2">
                            <button type="button" onclick="extractText('ocrFileQ', 'bulkText', 'statusQ')" class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded mb-2">Extract Questions</button>
                            <p id="statusQ" class="text-xs text-blue-600 mb-2"></p>
                            <textarea name="bulk_text" id="bulkText" class="w-full h-64 p-2 text-xs border rounded font-mono"></textarea>
                        </div>
                        <div class="p-4 bg-green-50 border rounded-lg">
                            <label class="block mb-2 font-bold text-green-700">2. Marking Scheme</label>
                            <input type="file" id="ocrFileA" accept=".pdf, .png, .jpg" class="block w-full text-sm mb-2">
                            <button type="button" onclick="extractText('ocrFileA', 'bulkAnswers', 'statusA')" class="bg-green-600 text-white text-xs px-3 py-1.5 rounded mb-2">Extract Answers</button>
                            <p id="statusA" class="text-xs text-green-600 mb-2"></p>
                            <textarea name="bulk_answers" id="bulkAnswers" class="w-full h-64 p-2 text-xs border rounded font-mono"></textarea>
                        </div>
                    </div>
                    <button type="submit" name="bulk_import" class="mt-6 w-full bg-indigo-700 text-white py-3 rounded-lg font-bold">Process & Preview</button>
                </form>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-700">Questions List</h3>
                
                <button form="bulkDeleteForm" type="submit" name="delete_selected" onclick="return confirm('Are you sure you want to delete selected questions?')" class="bg-red-500 text-white text-xs px-3 py-2 rounded font-bold hover:bg-red-600 shadow-sm">
                    <i class="ph ph-trash"></i> Delete Selected
                </button>
            </div>

            <form id="bulkDeleteForm" method="POST">
                <?php
                $res = $conn->query("SELECT * FROM exam_questions WHERE exam_id = $exam_id ORDER BY question_id ASC");
                if($res->num_rows > 0) {
                    $i = 1;
                    while($row = $res->fetch_assoc()){
                        $color = ($row['correct_option'] > 0) ? 'border-gray-200' : 'border-red-300 bg-red-50';
                ?>
                    <div class="p-3 mb-3 border rounded <?php echo $color; ?> flex items-start gap-3">
                        <div class="pt-1">
                            <input type="checkbox" name="selected_ids[]" value="<?php echo $row['question_id']; ?>" class="w-4 h-4 text-red-600 rounded border-gray-300 focus:ring-red-500">
                        </div>

                        <div class="flex-1 flex flex-col md:flex-row justify-between gap-4">
                            <div>
                                <div class="font-semibold text-gray-800">
                                    <span class="text-indigo-600 mr-1"><?php echo $i++; ?>.</span> 
                                    <?php echo $row['question_text']; ?>
                                </div>
                                <?php if($row['image_path']): ?>
                                    <img src="../../<?php echo htmlspecialchars($row['image_path']); ?>" class="h-16 mt-2 rounded border shadow-sm">
                                <?php endif; ?>
                            </div>
                            
                            <div class="shrink-0 text-right">
                                <?php if($row['correct_option'] > 0): ?>
                                    <span class="inline-block bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded border border-green-200">Ans: <?php echo $row['correct_option']; ?></span>
                                <?php else: ?>
                                    <span class="inline-block bg-red-100 text-red-800 text-xs font-bold px-2 py-1 rounded border border-red-200">No Ans</span>
                                <?php endif; ?>
                                
                                <div class="mt-2 space-x-2">
                                    <a href="?exam_id=<?php echo $exam_id; ?>&edit_q=<?php echo $row['question_id']; ?>#tab-content-manual" class="text-blue-500 hover:text-blue-700 text-sm font-medium">Edit</a>
                                    <a href="?exam_id=<?php echo $exam_id; ?>&del_q=<?php echo $row['question_id']; ?>" onclick="return confirm('Delete?')" class="text-red-400 hover:text-red-600 text-sm font-medium">Del</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                    }
                } else { echo "<p class='text-gray-400 text-center py-4'>No questions added yet.</p>"; }
                ?>
            </form>

            <div class="mt-6 border-t pt-4">
                <form method="POST"><button name="finish" class="w-full bg-gray-800 text-white py-3 rounded-lg font-bold hover:bg-gray-900 shadow">Save & Finish Exam</button></form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function switchTab(t){
    document.querySelectorAll('.tab-content').forEach(e=>e.classList.add('hidden'));
    document.getElementById('tab-content-'+t).classList.remove('hidden');
    // Simple logic to highlight active tab
    document.querySelectorAll('button[id^="tab-btn-"]').forEach(b => {
        b.classList.remove('text-indigo-600', 'border-indigo-600');
        b.classList.add('border-transparent');
    });
    document.getElementById('tab-btn-'+t).classList.add('text-indigo-600', 'border-indigo-600');
    document.getElementById('tab-btn-'+t).classList.remove('border-transparent');
}
switchTab('manual');

if(document.querySelector('#editor')) {
    ClassicEditor.create(document.querySelector('#editor')).catch(e => console.error(e));
}

async function extractText(inputId, outputId, statusId) {
    const file = document.getElementById(inputId).files[0];
    const status = document.getElementById(statusId);
    const textArea = document.getElementById(outputId);
    if (!file) { alert("Select file!"); return; }
    status.innerText = "Scanning...";
    try {
        if (file.type === 'application/pdf') {
            const pdf = await pdfjsLib.getDocument(await file.arrayBuffer()).promise;
            let fullText = "";
            for (let i = 1; i <= pdf.numPages; i++) {
                const page = await pdf.getPage(i);
                const viewport = page.getViewport({ scale: 1.5 });
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                canvas.height = viewport.height; canvas.width = viewport.width;
                await page.render({ canvasContext: ctx, viewport: viewport }).promise;
                const { data: { text } } = await Tesseract.recognize(canvas, 'sin+eng');
                fullText += text + "\n";
            }
            textArea.value = fullText;
        } else {
            const { data: { text } } = await Tesseract.recognize(file, 'sin+eng');
            textArea.value = text;
        }
        status.innerText = "Done!";
    } catch (e) { status.innerText = "Error: " + e.message; }
}
</script>

<?php include("../include/footer.php"); ?>