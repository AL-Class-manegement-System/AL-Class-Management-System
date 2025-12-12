<?php
// teacher_portal/pages/add_questions.php
// Updated: Auto-detect Correct Answer from PDF/Image (Supports Sinhala & English)

include('../include/head.php');
require_once '../../includes/connection.php';

// Exam ID නැතිනම් Dashboard එකට යවන්න
$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
if ($exam_id == 0) {
    echo "<script>alert('Invalid Exam ID'); window.location.href='create_exam.php';</script>";
    exit();
}

$msg = "";
$msg_type = "";

// Helper Function: වත්මන් ප්‍රශ්න ගණන ලබාගැනීම
function getQuestionCount($conn, $exam_id) {
    $result = $conn->query("SELECT COUNT(*) as count FROM exam_questions WHERE exam_id = $exam_id");
    return $result->fetch_assoc()['count'];
}

// ---------------------------------------------------------
// 1. UPDATE Question Logic (Edit)
// ---------------------------------------------------------
if (isset($_POST['update_q'])) {
    $qid = intval($_POST['question_id']);
    $q_text = trim($_POST['question']);
    $opt1 = trim($_POST['opt1']);
    $opt2 = trim($_POST['opt2']);
    $opt3 = trim($_POST['opt3']);
    $opt4 = trim($_POST['opt4']);
    $opt5 = trim($_POST['opt5']); 
    // Default to 0 if not selected
    $correct = isset($_POST['correct']) ? intval($_POST['correct']) : 0;

    $sql = "UPDATE exam_questions SET question_text=?, option_1=?, option_2=?, option_3=?, option_4=?, option_5=?, correct_option=? WHERE question_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssii", $q_text, $opt1, $opt2, $opt3, $opt4, $opt5, $correct, $qid);
    
    if($stmt->execute()){
        $msg = "Question updated successfully!";
        $msg_type = "green";
        echo "<script>window.location.href='add_questions.php?exam_id=$exam_id&msg=Question Updated';</script>";
        exit();
    } else {
        $msg = "Error updating: " . $conn->error;
        $msg_type = "red";
    }
}

// ---------------------------------------------------------
// 2. Manual Question Add Logic
// ---------------------------------------------------------
if (isset($_POST['add_q'])) {
    $q_text = trim($_POST['question']);
    $opt1 = trim($_POST['opt1']);
    $opt2 = trim($_POST['opt2']);
    $opt3 = trim($_POST['opt3']);
    $opt4 = trim($_POST['opt4']);
    $opt5 = trim($_POST['opt5']); 
    $correct = isset($_POST['correct']) && $_POST['correct'] !== "" ? intval($_POST['correct']) : 0;

    $current_count = getQuestionCount($conn, $exam_id);

    if ($current_count >= 50) {
        $msg = "Maximum limit of 50 questions reached!";
        $msg_type = "red";
    } else {
        $sql = "INSERT INTO exam_questions (exam_id, question_text, option_1, option_2, option_3, option_4, option_5, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssssi", $exam_id, $q_text, $opt1, $opt2, $opt3, $opt4, $opt5, $correct);
        if($stmt->execute()){
            $msg = "Question added!";
            $msg_type = "green";
        } else {
            $msg = "Error: " . $conn->error;
            $msg_type = "red";
        }
    }
}

// ---------------------------------------------------------
// 3. Smart Import & Column Import Logics
// ---------------------------------------------------------
if (isset($_POST['bulk_import'])) {
    $raw_text = $_POST['bulk_text'];
    
    // --- PRE-PROCESSING ---
    // 1. Inline Options: (2) -> \n(2)
    $raw_text = preg_replace('/(\s+[\(]?[2-5][\)\.])/', "\n$1", $raw_text);
    
    // 2. Inline Answers: "Ans: 1" or "පිළිතුර: 1" -> \nAns: 1
    // Supports: Ans, Answer, Pilithura (Sinhala), Uththaraya (Sinhala)
    $raw_text = preg_replace('/(\s+(?:Ans|Answer|පිළිතුර|උත්තරය)\s*[:\-\.]?\s*[a-eA-E1-5])/iu', "\n$1", $raw_text);

    $lines = preg_split('/\r\n|\r|\n/', $raw_text);
    $imported_count = 0;
    $current_q = [];
    $current_total = getQuestionCount($conn, $exam_id);
    
    foreach ($lines as $line) {
        $line = trim($line); 
        if (empty($line)) continue;
        if ($current_total >= 50) break;

        // MATCH QUESTION (Starts with 1. or Q1. etc)
        if (preg_match('/^\s*(Q?\d+[\.)\s]|Question\s\d+)\s*[:\.]?\s*(.*)/i', $line, $matches)) {
            if (!empty($current_q)) {
                if(!isset($current_q['answer'])) { $current_q['answer'] = 0; }
                if(saveQuestionToDB($conn, $exam_id, $current_q)) { 
                    $imported_count++; 
                    $current_total++; 
                }
            }
            $current_q = ['text' => $matches[2], 'options' => [], 'answer' => 0];
        } 
        // MATCH OPTIONS ((1), 1., a., etc)
        elseif (preg_match('/^\s*[\(]?([a-eA-E1-5])[\)\.\-]\s*(.*)/', $line, $matches)) {
            $current_q['options'][] = $matches[2];
        }
        // MATCH ANSWER (Ans: 1, Answer: A, පිළිතුර: 1, උත්තරය: 1)
        elseif (preg_match('/^\s*(Ans|Answer|පිළිතුර|උත්තරය)\s*[:\-\.]?\s*([a-eA-E1-5])/iu', $line, $matches)) {
            $ans_char = strtolower($matches[2]);
            $map = ['a'=>1, 'b'=>2, 'c'=>3, 'd'=>4, 'e'=>5, '1'=>1, '2'=>2, '3'=>3, '4'=>4, '5'=>5];
            if(isset($map[$ans_char])) $current_q['answer'] = $map[$ans_char];
        }
        // Append extra text to Question
        elseif (!empty($current_q) && empty($current_q['options']) && $current_q['answer'] == 0) {
            $current_q['text'] .= " " . $line;
        }
    }
    
    // Save the last question
    if (!empty($current_q) && $current_total < 50) {
        if(!isset($current_q['answer'])) { $current_q['answer'] = 0; }
        if(saveQuestionToDB($conn, $exam_id, $current_q)) $imported_count++;
    }
    
    $msg = "Imported $imported_count questions.";
    $msg_type = "green";
}

if (isset($_POST['bulk_import_col'])) {
    $q_lines = preg_split('/\r\n|\r|\n/', $_POST['col_questions']);
    $o1_lines = preg_split('/\r\n|\r|\n/', $_POST['col_opt1']);
    $o2_lines = preg_split('/\r\n|\r|\n/', $_POST['col_opt2']);
    $o3_lines = preg_split('/\r\n|\r|\n/', $_POST['col_opt3']);
    $o4_lines = preg_split('/\r\n|\r|\n/', $_POST['col_opt4']);
    $o5_lines = preg_split('/\r\n|\r|\n/', $_POST['col_opt5']);
    $c_lines = preg_split('/\r\n|\r|\n/', $_POST['col_correct']);

    $imported_count = 0;
    $current_total = getQuestionCount($conn, $exam_id);
    
    $stmt = $conn->prepare("INSERT INTO exam_questions (exam_id, question_text, option_1, option_2, option_3, option_4, option_5, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($q_lines as $index => $q_text) {
        $q_text = trim($q_text);
        if (empty($q_text)) continue; 
        if ($current_total >= 50) break;

        $opt1 = isset($o1_lines[$index]) ? trim($o1_lines[$index]) : "-";
        $opt2 = isset($o2_lines[$index]) ? trim($o2_lines[$index]) : "-";
        $opt3 = isset($o3_lines[$index]) ? trim($o3_lines[$index]) : "-";
        $opt4 = isset($o4_lines[$index]) ? trim($o4_lines[$index]) : "-";
        $opt5 = isset($o5_lines[$index]) ? trim($o5_lines[$index]) : "-";
        if($opt1=="") $opt1="-"; if($opt2=="") $opt2="-"; if($opt3=="") $opt3="-"; if($opt4=="") $opt4="-"; if($opt5=="") $opt5="-";

        $correct_raw = isset($c_lines[$index]) ? trim($c_lines[$index]) : "";
        $map = ['a'=>1, 'b'=>2, 'c'=>3, 'd'=>4, 'e'=>5, '1'=>1, '2'=>2, '3'=>3, '4'=>4, '5'=>5];
        $ans = 0; 
        if(!empty($correct_raw) && isset($map[strtolower(substr($correct_raw, 0, 1))])) {
            $ans = $map[strtolower(substr($correct_raw, 0, 1))];
        }
        
        $stmt->bind_param("issssssi", $exam_id, $q_text, $opt1, $opt2, $opt3, $opt4, $opt5, $ans);
        if ($stmt->execute()) { $imported_count++; $current_total++; }
    }
    $msg = "Imported $imported_count questions.";
    $msg_type = "green";
}

function saveQuestionToDB($conn, $exam_id, $q) {
    while(count($q['options']) < 5) $q['options'][] = "-";
    if (!isset($q['answer'])) $q['answer'] = 0;
    
    $stmt = $conn->prepare("INSERT INTO exam_questions (exam_id, question_text, option_1, option_2, option_3, option_4, option_5, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssi", $exam_id, $q['text'], $q['options'][0], $q['options'][1], $q['options'][2], $q['options'][3], $q['options'][4], $q['answer']);
    return $stmt->execute();
}

if(isset($_GET['del_q'])) {
    $qid = intval($_GET['del_q']);
    $conn->query("DELETE FROM exam_questions WHERE question_id=$qid");
    header("Location: add_questions.php?exam_id=$exam_id&msg=Question Deleted");
    exit();
}

$edit_data = null;
if (isset($_GET['edit_q'])) {
    $edit_id = intval($_GET['edit_q']);
    $res = $conn->query("SELECT * FROM exam_questions WHERE question_id = $edit_id AND exam_id = $exam_id");
    if ($res->num_rows > 0) {
        $edit_data = $res->fetch_assoc();
    }
}

if (isset($_POST['finish'])) {
    $check_res = $conn->query("SELECT COUNT(*) as count FROM exam_questions WHERE exam_id = $exam_id AND correct_option = 0");
    $missing_count = $check_res->fetch_assoc()['count'];
    
    if($missing_count > 0) {
        echo "<script>if(confirm('Warning: $missing_count questions do NOT have a correct answer set. Are you sure you want to finish?')){ window.location.href='create_exam.php'; }</script>";
    } else {
        echo "<script>alert('Exam saved successfully!'); window.location.href='create_exam.php';</script>";
    }
}

$total_q = getQuestionCount($conn, $exam_id);
?>

<?php include("../include/sidebar.php"); ?>

<script src="https://unpkg.com/tesseract.js@v2.1.0/dist/tesseract.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
<script>pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';</script>

<div class="p-4 sm:ml-64 pb-20">
    
    <div class="flex justify-between items-center mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Exam Management</h2>
            <p class="text-sm text-gray-500">Exam ID: <span class="font-mono font-bold text-indigo-600">#<?php echo $exam_id; ?></span></p>
        </div>
        <div class="text-right">
            <span class="block text-xs text-gray-500 uppercase tracking-wide">Questions Added</span>
            <span class="text-3xl font-bold <?php echo ($total_q >= 50) ? 'text-red-500' : 'text-green-500'; ?>"><?php echo $total_q; ?>/50</span>
        </div>
    </div>

    <?php if($msg || isset($_GET['msg'])): 
        $m = $msg ? $msg : $_GET['msg'];
        $t = $msg_type ? $msg_type : 'green';
    ?>
        <div class="bg-<?php echo $t; ?>-100 border-l-4 border-<?php echo $t; ?>-500 text-<?php echo $t; ?>-700 p-4 mb-6 rounded shadow-sm flex justify-between">
            <p><?php echo htmlspecialchars($m); ?></p>
            <button onclick="this.parentElement.style.display='none'">&times;</button>
        </div>
    <?php endif; ?>

    <div class="mb-6 border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
            <li class="mr-2">
                <button onclick="switchTab('manual')" id="tab-btn-manual" class="inline-block p-4 border-b-2 rounded-t-lg text-indigo-600 border-indigo-600 active-tab transition-all">
                    <?php echo $edit_data ? 'Edit Question' : 'Manual Entry'; ?>
                </button>
            </li>
            <li class="mr-2">
                <button onclick="switchTab('import')" id="tab-btn-import" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 transition-all">Smart Import (OCR)</button>
            </li>
            <li class="mr-2">
                <button onclick="switchTab('column')" id="tab-btn-column" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 transition-all">Column Import (Excel)</button>
            </li>
        </ul>
    </div>

    <div id="tab-content-manual" class="tab-content">
        <?php if ($total_q < 50 || $edit_data): ?>
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 <?php echo $edit_data ? 'border-yellow-400 ring-2 ring-yellow-100' : ''; ?>">
            <h3 class="font-bold text-lg text-gray-700 mb-4">
                <?php echo $edit_data ? '✏️ Edit Question #' . $edit_data['question_id'] : 'Add Single Question'; ?>
            </h3>
            
            <form method="POST">
                <?php if($edit_data): ?>
                    <input type="hidden" name="question_id" value="<?php echo $edit_data['question_id']; ?>">
                <?php endif; ?>

                <div class="mb-4">
                    <label class="block mb-2 font-medium text-sm">Question Text <span class="text-red-500">*</span></label>
                    <textarea name="question" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition" rows="2" placeholder="Type your question here..."><?php echo $edit_data ? htmlspecialchars($edit_data['question_text']) : ''; ?></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <input type="text" name="opt1" value="<?php echo $edit_data ? htmlspecialchars($edit_data['option_1']) : ''; ?>" placeholder="Option 1" required class="p-2.5 border rounded-lg bg-gray-50">
                    <input type="text" name="opt2" value="<?php echo $edit_data ? htmlspecialchars($edit_data['option_2']) : ''; ?>" placeholder="Option 2" required class="p-2.5 border rounded-lg bg-gray-50">
                    <input type="text" name="opt3" value="<?php echo $edit_data ? htmlspecialchars($edit_data['option_3']) : ''; ?>" placeholder="Option 3" required class="p-2.5 border rounded-lg bg-gray-50">
                    <input type="text" name="opt4" value="<?php echo $edit_data ? htmlspecialchars($edit_data['option_4']) : ''; ?>" placeholder="Option 4" required class="p-2.5 border rounded-lg bg-gray-50">
                    <input type="text" name="opt5" value="<?php echo $edit_data ? htmlspecialchars($edit_data['option_5']) : ''; ?>" placeholder="Option 5" required class="p-2.5 border rounded-lg bg-gray-50 md:col-span-2">
                </div>
                
                <div class="mb-4 w-full md:w-1/3">
                    <label class="block mb-2 font-medium text-sm text-gray-900">Correct Answer (Optional)</label>
                    <select name="correct" class="w-full p-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500">
                        <option value="0" <?php echo (!$edit_data || $edit_data['correct_option'] == 0) ? 'selected' : ''; ?>>-- Not Selected --</option>
                        <?php $sel = $edit_data ? $edit_data['correct_option'] : 0; ?>
                        <option value="1" <?php if($sel==1) echo 'selected'; ?>>Option 1</option>
                        <option value="2" <?php if($sel==2) echo 'selected'; ?>>Option 2</option>
                        <option value="3" <?php if($sel==3) echo 'selected'; ?>>Option 3</option>
                        <option value="4" <?php if($sel==4) echo 'selected'; ?>>Option 4</option>
                        <option value="5" <?php if($sel==5) echo 'selected'; ?>>Option 5</option>
                    </select>
                </div>
                
                <div class="flex gap-2">
                    <?php if($edit_data): ?>
                        <button type="submit" name="update_q" class="bg-yellow-500 text-white px-6 py-2.5 rounded-lg hover:bg-yellow-600 font-medium shadow-lg transition transform hover:-translate-y-0.5">
                            <i class="ph ph-floppy-disk mr-2"></i> Update Question
                        </button>
                        <a href="add_questions.php?exam_id=<?php echo $exam_id; ?>" class="bg-gray-500 text-white px-6 py-2.5 rounded-lg hover:bg-gray-600 font-medium">Cancel</a>
                    <?php else: ?>
                        <button type="submit" name="add_q" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 font-medium shadow-lg transition transform hover:-translate-y-0.5">
                            <i class="ph ph-plus-circle mr-2"></i> Add Question
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <?php else: ?>
            <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50" role="alert">
                <span class="font-medium">Limit Reached!</span> You have reached the 50 question limit.
            </div>
        <?php endif; ?>
    </div>

    <div id="tab-content-import" class="tab-content hidden">
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h3 class="font-bold text-lg text-gray-700 mb-2">Smart Import (OCR)</h3>
            
            <div class="mb-4 p-4 bg-gray-50 border border-dashed border-gray-300 rounded-lg">
                <label class="block mb-2 text-sm font-medium text-gray-900">Upload PDF or Image (Sinhala/English)</label>
                <div class="flex gap-2">
                    <input type="file" id="ocrFile" accept=".pdf, .png, .jpg, .jpeg" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white focus:outline-none">
                    <button type="button" onclick="extractText()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium text-sm whitespace-nowrap">
                        <i class="ph ph-scan"></i> Extract Text
                    </button>
                </div>
                <p id="ocrStatus" class="mt-2 text-sm text-blue-600 font-semibold hidden">Processing... Please wait.</p>
                <p class="mt-1 text-xs text-gray-500">Supports: PDF, JPG, PNG. Uses OCR to read text. If 'Ans: 1' or 'පිළිතුර: 1' is found, it will be auto-selected.</p>
            </div>

            <p class="text-sm text-gray-500 mb-4">Edit extracted text below.</p>
            <form method="POST">
                <textarea name="bulk_text" id="bulkText" class="w-full h-96 p-4 border border-gray-300 rounded-lg font-mono text-sm bg-gray-50 focus:ring-2 focus:ring-green-500 outline-none" placeholder="Text will appear here..."></textarea>
                <button type="submit" name="bulk_import" class="mt-4 bg-green-600 text-white px-6 py-2.5 rounded-lg hover:bg-green-700 font-medium">Process & Import</button>
            </form>
        </div>
    </div>

    <div id="tab-content-column" class="tab-content hidden">
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h3 class="font-bold text-lg text-gray-700 mb-2">Column Import (From Excel)</h3>
            <p class="text-sm text-gray-500 mb-4"><strong class="text-blue-500">Correct Answer is Optional here too.</strong></p>
            <form method="POST" class="space-y-4">
                <div><label class="text-sm font-bold">Questions</label><textarea name="col_questions" required class="w-full h-32 p-2 border rounded text-sm bg-gray-50"></textarea></div>
                <div class="grid grid-cols-5 gap-2">
                    <textarea name="col_opt1" placeholder="Opt 1" class="h-32 p-2 border rounded text-sm bg-gray-50"></textarea>
                    <textarea name="col_opt2" placeholder="Opt 2" class="h-32 p-2 border rounded text-sm bg-gray-50"></textarea>
                    <textarea name="col_opt3" placeholder="Opt 3" class="h-32 p-2 border rounded text-sm bg-gray-50"></textarea>
                    <textarea name="col_opt4" placeholder="Opt 4" class="h-32 p-2 border rounded text-sm bg-gray-50"></textarea>
                    <textarea name="col_opt5" placeholder="Opt 5" class="h-32 p-2 border rounded text-sm bg-gray-50"></textarea>
                </div>
                <div><label class="text-sm font-bold text-gray-600">Answers (Optional)</label><textarea name="col_correct" class="w-full md:w-1/3 h-32 p-2 border border-gray-300 rounded text-sm bg-gray-50"></textarea></div>
                <button type="submit" name="bulk_import_col" class="bg-blue-600 text-white px-6 py-2.5 rounded">Import Columns</button>
            </form>
        </div>
    </div>

    <div class="mt-8 bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-bold text-gray-700">Exam Questions Preview</h3>
            <span class="text-xs font-medium bg-indigo-100 text-indigo-800 px-2.5 py-0.5 rounded">Total: <?php echo $total_q; ?></span>
        </div>
        
        <div class="p-6">
            <?php
            $q_res = $conn->query("SELECT * FROM exam_questions WHERE exam_id = $exam_id ORDER BY question_id ASC");
            if($q_res->num_rows > 0) {
                echo '<div class="space-y-4">';
                $i = 1;
                while($row = $q_res->fetch_assoc()){
                    $is_ans_set = ($row['correct_option'] > 0);
            ?>
                <div class="p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition group relative <?php echo (!$is_ans_set) ? 'border-red-300 bg-red-50' : ''; ?>">
                    <div class="flex justify-between items-start">
                        <p class="font-semibold text-gray-800 pr-8"><span class="text-indigo-500 mr-2"><?php echo $i++; ?>.</span> <?php echo htmlspecialchars($row['question_text']); ?></p>
                        
                        <div class="flex items-center space-x-2">
                            <?php if(!$is_ans_set): ?>
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded border border-red-400">⚠️ Answer Not Set</span>
                            <?php endif; ?>
                            
                            <a href="?exam_id=<?php echo $exam_id; ?>&edit_q=<?php echo $row['question_id']; ?>#tab-content-manual" 
                               class="text-blue-500 hover:text-blue-700 transition p-1" title="Edit">
                                <i class="ph ph-pencil-simple text-lg"></i>
                            </a>
                            <a href="?exam_id=<?php echo $exam_id; ?>&del_q=<?php echo $row['question_id']; ?>" onclick="return confirm('Delete this question?')" 
                               class="text-gray-400 hover:text-red-500 transition p-1" title="Delete">
                                <i class="ph ph-trash text-lg"></i>
                            </a>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 mt-2 text-sm text-gray-600 ml-6">
                        <div class="<?php echo ($row['correct_option'] == 1) ? 'text-green-600 font-bold bg-green-50 p-1 rounded' : ''; ?>">(1) <?php echo htmlspecialchars($row['option_1']); ?></div>
                        <div class="<?php echo ($row['correct_option'] == 2) ? 'text-green-600 font-bold bg-green-50 p-1 rounded' : ''; ?>">(2) <?php echo htmlspecialchars($row['option_2']); ?></div>
                        <div class="<?php echo ($row['correct_option'] == 3) ? 'text-green-600 font-bold bg-green-50 p-1 rounded' : ''; ?>">(3) <?php echo htmlspecialchars($row['option_3']); ?></div>
                        <div class="<?php echo ($row['correct_option'] == 4) ? 'text-green-600 font-bold bg-green-50 p-1 rounded' : ''; ?>">(4) <?php echo htmlspecialchars($row['option_4']); ?></div>
                        <div class="<?php echo ($row['correct_option'] == 5) ? 'text-green-600 font-bold bg-green-50 p-1 rounded' : ''; ?>">(5) <?php echo htmlspecialchars($row['option_5']); ?></div>
                    </div>
                </div>
            <?php 
                }
                echo '</div>';
            } else {
                echo "<div class='text-center py-10 text-gray-400'><p>No questions added yet.</p></div>";
            }
            ?>
        </div>
        
        <div class="p-4 bg-gray-50 border-t border-gray-200">
            <form method="POST">
                <button type="submit" name="finish" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-bold hover:bg-indigo-700 shadow-md transition transform hover:scale-[1.01]">
                    <i class="ph ph-check-circle mr-2"></i> Save & Finish
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    const btns = ['manual', 'import', 'column'];
    btns.forEach(btn => {
        let el = document.getElementById('tab-btn-' + btn);
        el.classList.remove('text-indigo-600', 'border-indigo-600', 'active-tab');
        el.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300');
    });
    document.getElementById('tab-content-' + tabName).classList.remove('hidden');
    let activeBtn = document.getElementById('tab-btn-' + tabName);
    activeBtn.classList.remove('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300');
    activeBtn.classList.add('text-indigo-600', 'border-indigo-600', 'active-tab');
}

if(window.location.hash === '#tab-content-manual') {
    switchTab('manual');
}

async function extractText() {
    const fileInput = document.getElementById('ocrFile');
    const status = document.getElementById('ocrStatus');
    const textArea = document.getElementById('bulkText');
    
    if (fileInput.files.length === 0) {
        alert("Please select a file first!");
        return;
    }

    const file = fileInput.files[0];
    const fileType = file.type;

    status.classList.remove('hidden');
    status.innerText = "Processing " + file.name + "... This may take some time (downloading Sinhala data).";

    try {
        if (fileType === 'application/pdf') {
            const arrayBuffer = await file.arrayBuffer();
            const pdf = await pdfjsLib.getDocument(arrayBuffer).promise;
            let fullText = "";

            for (let i = 1; i <= pdf.numPages; i++) {
                status.innerText = `Scanning PDF page ${i} of ${pdf.numPages}...`;
                const page = await pdf.getPage(i);
                const viewport = page.getViewport({ scale: 1.5 });
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                await page.render({ canvasContext: context, viewport: viewport }).promise;
                const { data: { text } } = await Tesseract.recognize(canvas, 'sin+eng');
                fullText += text + "\n\n";
            }
            textArea.value = fullText;
            status.innerText = "PDF scan complete! Please check and edit text below.";
        
        } else if (fileType.startsWith('image/')) {
            status.innerText = "Scanning image... (Downloading Sinhala data if first time)";
            const { data: { text } } = await Tesseract.recognize(file, 'sin+eng', {
                logger: m => {
                    if(m.status === 'recognizing text') {
                        status.innerText = `Scanning: ${Math.round(m.progress * 100)}%`;
                    }
                }
            });
            textArea.value = text;
            status.innerText = "Image scan complete! Please check and edit text below.";
        } else {
            alert("Unsupported file type. Please upload PDF or Image.");
            status.classList.add('hidden');
        }
    } catch (error) {
        console.error(error);
        status.innerText = "Error: " + error.message;
        status.classList.add('text-red-500');
    }
}
</script>

<?php include("../include/footer.php"); ?>