<?php include('../includes/header.php'); ?>
<link rel="stylesheet" href="../css/contact.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="contact-container">
    <section class="contact-us">
        <h1>Contact Us</h1>
        <p class="main-text">You can contact us through the following:</p>

        <div class="content-wrapper">
            <div class="contact-form-column">
                <form action="submit_form.php" method="POST" class="contact-form">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required placeholder="Name">

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required placeholder="Email">

                    <label for="message">Message:</label>
                    <textarea id="message" name="message" rows="6" required placeholder="Message"></textarea>

                    <button type="submit" class="send-button">Send</button>
                </form>
            </div>

            <div class="office-details-column">
                <p class="logo-text">FUTUREMINDS.lk</p>
                <h2>Our Head Office <br> At Rajagiriya</h2>

                <p>No: 160, Buthgamuwa Road, Kalapaluwawa, Rajagiriya.</p>
                <p>075 444 4444</p>
                <p>info@futureminds.lk</p>
                <p>Monday-Saturday : 8am-5pm</p>

                <div class="social-icons">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </section>
</div>
<?php include('../includes/footer.php'); ?>