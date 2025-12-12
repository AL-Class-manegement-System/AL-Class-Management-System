<?php 
// subscribe_process.php
include 'connection.php'; 

if (isset($_POST['subscribe_btn'])) {

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // ඔබ පැමිණි පිටුව සොයා ගනී (නැවත එම පිටුවටම යැවීමට)
    $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../pages/index.php';
    
    // URL එකට query parameters එකතු කිරීමට ලකුණ තෝරා ගැනීම (? හෝ &)
    $separator = (parse_url($redirect_url, PHP_URL_QUERY) == NULL) ? '?' : '&';

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
         
        $checkQuery = "SELECT * FROM newsletter_subs WHERE email='$email' ";
        $result = mysqli_query($conn, $checkQuery);

        if(mysqli_num_rows($result) > 0) {
            // දැනටමත් තිබේ නම් warning එකක් යවමු
            header("Location: $redirect_url" . $separator . "status=warning&msg=This+email+is+already+subscribed");
        } else {
            $insertQuery = "INSERT INTO newsletter_subs (email) VALUES ('$email') ";
            if(mysqli_query($conn, $insertQuery)) {
                // සාර්ථක නම් success එකක් යවමු
                header("Location: $redirect_url" . $separator . "status=success&msg=Thank+you+for+subscribing!");
            } else {
                header("Location: $redirect_url" . $separator . "status=error&msg=Something+went+wrong.+Please+try+again.");
            }
        }
    } else {
        header("Location: $redirect_url" . $separator . "status=error&msg=Please+enter+a+valid+email+address");
    }
    exit();
}
?>