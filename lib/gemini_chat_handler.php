<?php
//API Key 
$api_key = "AIzaSyAnsGDxJbW0Q1FnMyHbNp208WCmmkwS-AY";
$model_name = "gemini-2.5-flash";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    header('Content-Type: application/json');

    $user_message = $_POST['message'];


    include '../includes/connection.php';

    // 1. Fetch Classes (Public Info)
    $class_sql = "SELECT class_name, stream, subject, teacher_name, day, time, fee FROM classes WHERE status = 1";
    $class_res = $conn->query($class_sql);
    $classes_text = "";
    if ($class_res && $class_res->num_rows > 0) {
        while ($row = $class_res->fetch_assoc()) {
            $classes_text .= "- " . $row['class_name'] . " (" . $row['stream'] . " - " . $row['subject'] . ") by " . $row['teacher_name'] . " on " . $row['day'] . " at " . $row['time'] . " (Fee: Rs. " . $row['fee'] . ")\n";
        }
    } else {
        $classes_text = "No active classes found.";
    }

    // 2. Fetch Teachers (Public Profile Info)
    $teacher_sql = "SELECT full_name, subject, qualifications, description FROM teachers WHERE status = 1";
    $teacher_res = $conn->query($teacher_sql);
    $teachers_text = "";
    if ($teacher_res && $teacher_res->num_rows > 0) {
        while ($row = $teacher_res->fetch_assoc()) {
            $qual = !empty($row['qualifications']) ? " (" . $row['qualifications'] . ")" : "";
            $desc = !empty($row['description']) ? " - " . substr($row['description'], 0, 100) . "..." : "";
            $teachers_text .= "- " . $row['full_name'] . " (" . $row['subject'] . ")" . $qual . $desc . "\n";
        }
    } else {
        $teachers_text = "No active teachers details available.";
    }

    // 3. Fetch Physical Exams (Schedule)
    $exam_sql = "SELECT exam_name, subject, date FROM exams ORDER BY date DESC LIMIT 5";
    $exam_res = $conn->query($exam_sql);
    $exams_text = "";
    if ($exam_res && $exam_res->num_rows > 0) {
        while ($row = $exam_res->fetch_assoc()) {
            $sub = !empty($row['subject']) ? "For " . $row['subject'] : "";
            $exams_text .= "- " . $row['exam_name'] . " " . $sub . " on " . $row['date'] . "\n";
        }
    } else {
        $exams_text = "No upcoming physical exams found.";
    }

    // 4. Fetch Online Exams (Available Tests)
    $online_exam_sql = "SELECT title, subject, duration FROM online_exams WHERE approval_status = 'Approved' ORDER BY created_at DESC LIMIT 5";
    $online_exam_res = $conn->query($online_exam_sql);
    $online_exams_text = "";
    if ($online_exam_res && $online_exam_res->num_rows > 0) {
        while ($row = $online_exam_res->fetch_assoc()) {
            $online_exams_text .= "- " . $row['title'] . " (" . $row['subject'] . ") - Duration: " . $row['duration'] . " mins\n";
        }
    } else {
        $online_exams_text = "No active online exams found.";
    }

    // 5. Statistics (Aggregate Data only)
    $student_sql = "SELECT COUNT(*) as total FROM students WHERE status = 1";
    $student_res = $conn->query($student_sql);
    $student_count = $student_res ? $student_res->fetch_assoc()['total'] : 0;

    $teacher_count_sql = "SELECT COUNT(*) as total FROM teachers WHERE status = 1";
    $teacher_count_res = $conn->query($teacher_count_sql);
    $teacher_count = $teacher_count_res ? $teacher_count_res->fetch_assoc()['total'] : 0;

    $system_instruction = "You are a helpful AI assistant for a class management system. 
    
    **Language Preferences:**
    - **Primary:** English (Default). Always try to answer in English first unless the user explicitly asks in Sinhala.
    - **Secondary:** Sinhala. Use this only if the user's query is in Sinhala or if they request it.
    
    Use the following real-time database information to answer user queries. Do NOT reveal private student data or passwords. Only use the public info provided below:
    
    **Active Classes:**
    $classes_text

    **Our Teachers:**
    $teachers_text

    **Upcoming Physical Exams:**
    $exams_text

    **Available Online Exams:**
    $online_exams_text
    
    **Institute Statistics:**
    - Active Students: $student_count
    - Active Teachers: $teacher_count
    
    Answer questions based on this data. If the answer is not in the data, say you don't know.";

    $url = "https://generativelanguage.googleapis.com/v1beta/models/" . $model_name . ":generateContent?key=" . $api_key;

    // The payload for the Gemini API
    $payload = json_encode([
        'systemInstruction' => [
            'parts' => [
                ['text' => $system_instruction]
            ]
        ],
        'contents' => [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => $user_message]
                ]
            ]
        ]
    ]);

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);

    if ($http_code !== 200 || isset($data['error'])) {
        $error_message = $data['error']['message'] ?? 'API Error: Could not reach the service.';
        echo json_encode(['error' => 'API Error: ' . $error_message]);
        exit;
    }


    $ai_response = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, I could not generate a response.';

    echo json_encode(['response' => $ai_response]);

} else {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method.']);
}
?>