<?php
session_start();

// Database Connection (මෙතන ඔයාගේ database details දාන්න)
// $conn = new mysqli("localhost", "root", "", "your_db_name");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- reCAPTCHA CHECK ---
    
    // 1. Captcha එක click කරලා ද බලනවා
    if (empty($_POST['g-recaptcha-response'])) {
        header("Location: login.php?error=Please check the reCAPTCHA box.");
        exit();
    }

    // 2. Google Verify කිරීම
    // ඔයා දුන්න Secret Key එක මෙතන දාලා තියෙනවා
    $secretKey = "6LeQlxcsAAAAAFqr7YCjbYnVW4uClfzKoOM04lv3"; 
    $responseKey = $_POST['g-recaptcha-response'];
    $userIP = $_SERVER['REMOTE_ADDR'];

    // Google වෙත දත්ත යැවීම
    $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$responseKey&remoteip=$userIP";
    $response = file_get_contents($url);
    $responseKeys = json_decode($response, true);

    // 3. Verification අසාර්ථක නම්
    if(!$responseKeys["success"]) {
        header("Location: login.php?error=Robot verification failed. Please try again.");
        exit();
    }

    // --- RECAPTCHA SUCCESS ---
    // මෙතනින් පහළට ඔයාගේ Username/Password check කරන කෝඩ් එක ලියන්න
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    // උදාහරණයක් (Example Login Logic):
    /*
    $sql = "SELECT * FROM users WHERE username = ?";
    // ... code to check database ...
    if ($login_success) {
        $_SESSION['user_id'] = $user_id;
        header("Location: dashboard.php");
    } else {
        header("Location: login.php?error=Invalid username or password");
    }
    */
    
    // දැනට ටෙස්ට් කරන්න නිකන් message එකක්:
    echo "reCAPTCHA Verified Successfully! Now checking database...";
}
?>