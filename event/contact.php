<?php

$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'event_management';

$con = mysqli_connect($db_host, $db_username, $db_password, $db_name);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!mysqli_select_db($con, $db_name)) {
    die("Database '$db_name' does not exist");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    $query = "INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("sss", $name, $email, $message);
    $stmt->execute();

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="contact.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body>

    <header>
        <nav>
            <div class="logo">Contact</div>
            <div class="menu">
                <a href="home.php">Home</a>
                <a href="reviews.php">Reviews</a>
            </div>
        </nav>
    </header>

    
    <section class="contact-section">
        <div class="container">
            <h2>Get in Touch</h2>
            <p>Feel free to reach out to us for any inquiries or assistance!</p>

            <div class="contact-info">
                <div class="info-box">
                    <i class="fas fa-phone-alt"></i>
                    <h3>Phone</h3>
                    <p>+880 1762550111</p>
                    <p>+880 1776899819</p>
                </div>
                <div class="info-box">
                    <i class="fas fa-envelope"></i>
                    <h3>Email</h3>
                    <p>jabidreadom@gmail.com</p>
                    <p>sajibmasud@gmail.com</p>
                </div>
                <div class="info-box">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Address</h3>
                    <p>Bracu,Dhaka,1212,Badda</p>
                </div>
            </div>

            <div class="form-container">
                <h3>Send us a Message</h3>
                <form action="" method="post">
                    <div class="input-group">
                        <input type="text" id="name" name="name" placeholder="Your Name" required>
                    </div>
                    <div class="input-group">
                        <input type="email" id="email" name="email" placeholder="Your Email" required>
                    </div>
                    <div class="input-group">
                        <textarea id="message" name="message" placeholder="Your Message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Submit</button>
                </form>
            </div>
        </div>
    </section>

    <section class="map-section">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d14604.426421836505!2d90.412518!3d23.810331!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b85c7c511f0b%3A0xf7e0f6f9df878b!2sBangladesh!5e0!3m2!1sen!2sbd!4v1695265002734!5m2!1sen!2sbd" 
            width="100%" 
            height="400" 
            frameborder="0" 
            style="border:0;" 
            allowfullscreen="">
        </iframe>
    </section>
</body>

</html>