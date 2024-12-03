<?php
session_start();

$logged_in = false; 

if (isset($_SESSION['email']) && !empty($_SESSION['email'])) {
    $logged_in = true; 
} elseif (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $logged_in = true; 
}

if (!$logged_in) {
    header("Location: login.php");
    exit();
}


$con = mysqli_connect("localhost", "root", "", "event_management");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "SELECT *, 'wedding' AS package_type FROM wedding_packages 
          UNION ALL 
          SELECT *, 'corporate' AS package_type FROM corporate_packages 
          UNION ALL 
          SELECT *, 'birthday' AS package_type FROM birthday_packages 
          UNION ALL 
          SELECT *, 'fashion' AS package_type FROM fashion_packages";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

$wedding_packages = [];
$corporate_packages = [];
$birthday_packages = [];
$fashion_packages = [];


while ($row = mysqli_fetch_assoc($result)) {
    switch ($row['package_type']) {
        case 'wedding':
            $wedding_packages[] = $row;
            break;
        case 'corporate':
            $corporate_packages[] = $row;
            break;
        case 'birthday':
            $birthday_packages[] = $row;
            break;
        case 'fashion':
            $fashion_packages[] = $row;
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS - Packages</title>
    <link rel="stylesheet" href="package.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Event-MS</div>
            <div class="menu">
                <a href="user_profile.php">User Profile</a>
            </div>
            <div class="logout">
                <a href="logout.php">Logout</a>
            </div>
        </nav>
        <section class="htxt">
            <span>Explore</span>
            <h1>Our Exclusive Event Packages</h1>
        </section>
    </header>

    <main>

        <div class="packages-section">
            <h2>Wedding Packages</h2>
            <div class="package-container">
                <?php foreach ($wedding_packages as $package) { ?>
                    <div class="package-card">
                        <h3><?php echo $package['package_name']; ?></h3>
                        <p><strong>Price:</strong> $<?php echo $package['price']; ?></p>
                        <p><?php echo $package['description']; ?></p>
                        <div class="button-container">
                            <a href="book_package.php?package_name=<?php echo $package['package_name']; ?>">Book Now</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="packages-section">
            <h2>Corporate Packages</h2>
            <div class="package-container">
                <?php foreach ($corporate_packages as $package) { ?>
                    <div class="package-card">
                        <h3><?php echo $package['package_name']; ?></h3>
                        <p><strong>Price:</strong> $<?php echo $package['price']; ?></p>
                        <p><?php echo $package['description']; ?></p>
                        <div class="button-container">
                            <a href="book_package.php?package_name=<?php echo $package['package_name']; ?>">Book Now</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>


        <div class="packages-section">
            <h2>Birthday Packages</h2>
            <div class="package-container">
                <?php foreach ($birthday_packages as $package) { ?>
                    <div class="package-card">
                        <h3><?php echo $package['package_name']; ?></h3>
                        <p><strong>Price:</strong> $<?php echo $package['price']; ?></p>
                        <p><?php echo $package['description']; ?></p>
                        <div class="button-container">
                            <a href="book_package.php?package_name=<?php echo $package['package_name']; ?>">Book Now</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>


        <div class="packages-section">
            <h2>Fashion Packages</h2>
            <div class="package-container">
                <?php foreach ($fashion_packages as $package) { ?>
                    <div class="package-card">
                        <h3><?php echo $package['package_name']; ?></h3>
                        <p><strong>Price:</strong> $<?php echo $package['price']; ?></p>
                        <p><?php echo $package['description']; ?></p>
                        <div class="button-container">
                            <a href="book_package.php?package_name=<?php echo $package['package_name']; ?>">Book Now</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Event Management System</p>
    </footer>
</body>
</html>
