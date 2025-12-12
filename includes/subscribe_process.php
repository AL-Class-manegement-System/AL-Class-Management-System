<?php include 'connection.php'; 

if (isset ($_POST['subscribe_btn'])) {

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

if (filter_var($emil,FILTER_VALIDATE_EMAIL)) {
     
    $checkQuery = "SELECT * FROM newsletter_subs WHERE email='$email' ";
    $result = mysqli_query($conn, $checkQuery);

    if(mysqli_num_rows($result) > 0) {

        echo "<script>alert('This email is already subscribed.'); window.location.href='../index.php';</script>";
    } else {
        $insertQurey = "INSERT INTO newsletter_subs (email) VALUES ('$email') ";
        if(mysqli_query($conn, $insertQurey)) {
            echo "<script>alert('Subscription successful! Thank you for subscribing.'); window.location.href='../index.php';</script>";
        } else {
            echo "<script>alert('Error occurred while subscribing. Please try again later.'); window.location.href='../index.php';</script>";
        }
    }
}else {
    echo "<script>alert('Please enter a valid email address.'); window.location.href='../index.php';</script>";
}
}
?>