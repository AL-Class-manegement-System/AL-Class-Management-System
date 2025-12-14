<?php
//API Key 
$api_key = "AIzaSyB_nXNdo9jfi4ex453dvXYsydvx7r60e9w";
$model_name = "gemini-2.5-flash";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    header('Content-Type: application/json');

    $user_message = $_POST['message'];


    include '../includes/connection.php';

    $notice_sql = "SELECT title, description FROM notices WHERE status = 1 ORDER BY created_at DESC LIMIT 5";
    $notice_res = $conn->query($notice_sql);
    $notices_text = "";
    if ($notice_res && $notice_res->num_rows > 0) {
        while ($row = $notice_res->fetch_assoc()) {
            $notices_text .= "- " . $row['title'] . ": " . $row['description'] . "\n";
        }
    } else {
        $notices_text = "No active notices.";
    }

    $class_sql = "SELECT class_name, subject, teacher_name, day, time, fee FROM classes WHERE status = 1";
    $class_res = $conn->query($class_sql);
    $classes_text = "";
    if ($class_res && $class_res->num_rows > 0) {
        while ($row = $class_res->fetch_assoc()) {
            $classes_text .= "- " . $row['class_name'] . " (" . $row['subject'] . ") by " . $row['teacher_name'] . " on " . $row['day'] . " at " . $row['time'] . " (Fee: Rs. " . $row['fee'] . ")\n";
        }
    } else {
        $classes_text = "No active classes found.";
    }

    $exam_sql = "SELECT exam_name, subject, date FROM exams ORDER BY date DESC LIMIT 5";
    $exam_res = $conn->query($exam_sql);
    $exams_text = "";
    if ($exam_res && $exam_res->num_rows > 0) {
        while ($row = $exam_res->fetch_assoc()) {
            $exams_text .= "- " . $row['exam_name'] . " (" . $row['subject'] . ") on " . $row['date'] . "\n";
        }
    } else {
        $exams_text = "No upcoming exams found.";
    }

    $student_sql = "SELECT COUNT(*) as total FROM students WHERE status = 1";
    $student_res = $conn->query($student_sql);
    $student_count = $student_res ? $student_res->fetch_assoc()['total'] : 0;


    $teacher_sql = "SELECT COUNT(*) as total FROM teachers WHERE status = 1";
    $teacher_res = $conn->query($teacher_sql);
    $teacher_count = $teacher_res ? $teacher_res->fetch_assoc()['total'] : 0;

    $system_instruction = "You are a helpful AI assistant for a class management system. 
    
    **Language Preferences:**
    - **Primary:** English (Default). Always try to answer in English first unless the user explicitly asks in Sinhala.
    - **Secondary:** Sinhala. Use this only if the user's query is in Sinhala or if they request it.
    
    Use the following real-time database information to answer user queries:
    
    **Latest Notices:**
    $notices_text
    
    **Active Classes:**
    $classes_text

    **Upcoming Exams:**
    $exams_text
    
    **Statistics:**
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