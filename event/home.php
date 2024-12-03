<?php
    include("connect.php");
    session_start();

    if (isset($_SESSION['admin_logged_in'])) {
        $logged_in = true;
    } else {
        $logged_in = false;
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS</title>
    <link rel="stylesheet" href="home.css">

</head>

<body>

    <header>
        <nav>
            <div class="logo">
                Event-MS
            </div>
            <div class="menu">
                <a href="login.php">Login</a>
                <a href="contact.php">Contact</a>
                <a href="reviews.php">Reviews</a>
            </div>
            <div class="Signup">
                <a href="sign.php">Signup</a>
            </div>
        </nav>
        <section class="htxt">
            <span>Enjoy</span>
            <h1>The Best Event Management</h1>
            <br>

            <a href="login.php">Book your event now !</a>
            
        </section>
    </header>

</body>
</html>