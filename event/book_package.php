<?php
session_start();


if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "event_management");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['package_name'])) {
    $package_name = $_GET['package_name'];

    $stmt = $con->prepare("SELECT * FROM wedding_packages WHERE package_name = ? 
                            UNION SELECT * FROM corporate_packages WHERE package_name = ? 
                            UNION SELECT * FROM birthday_packages WHERE package_name = ? 
                            UNION SELECT * FROM fashion_packages WHERE package_name = ?");
    $stmt->bind_param("ssss", $package_name, $package_name, $package_name, $package_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $package = $result->fetch_assoc();
        $package_price = $package['price'];
        $package_description = $package['description'];
    } else {
        echo "Package not found.";
        exit();
    }
} else {
    echo "No package selected.";
    exit();
}


$customer_email = $_SESSION['email'];
$customer_name = '';
$stmt = $con->prepare("SELECT fullname FROM signup WHERE email = ?");
$stmt->bind_param("s", $customer_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $customer_details = $result->fetch_assoc();
    $customer_name = $customer_details['fullname'];
} else {
    echo "Customer details not found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="book_package.css">
    <title>Book Package</title>
</head>
<body>
    <header>
        <nav>
            <div class="logo">Event-MS</div>
            <div class="menu">
                <a href="packages.php">Packages</a>
            </div>
            <div class="logout">
                <a href="logout.php">Logout</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="booking-container">
            <h1>Book Package</h1>
            <h2>Package Name: <?php echo htmlspecialchars($package['package_name']); ?></h2>
            <p><strong>Price:</strong> $<?php echo htmlspecialchars($package_price); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($package_description); ?></p>
            
            <form action="insert_order.php" method="post">
                <input type="hidden" name="package_name" value="<?php echo htmlspecialchars($package['package_name']); ?>">
                <input type="hidden" name="price" value="<?php echo htmlspecialchars($package_price); ?>">
                
                <label for="customer_name">Customer Name:</label>
                <input type="text" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($customer_name); ?>" readonly><br><br>
                
                <label for="customer_email">Customer Email:</label>
                <input type="email" id="customer_email" name="customer_email" value="<?php echo htmlspecialchars($customer_email); ?>" readonly><br><br>
                
                <label for="order_date">Order Date:</label>
                <input type="date" id="order_date" name="order_date" value="<?php echo date('Y-m-d'); ?>" readonly><br><br>
                

                <label for="event_date">Event Date:</label>
                <input type="date" id="event_date" name="event_date" required><br><br>
                

                <label for="venue">Venue:</label>
                <select id="venue" name="venue" required>
                    <option value="" disabled selected>Select a division</option>
                    <option value="Chittagong">Chittagong</option>
                    <option value="Khulna">Khulna</option>
                    <option value="Rajshahi">Rajshahi</option>
                    <option value="Barisal">Barisal</option>
                    <option value="Sylhet">Sylhet</option>
                    <option value="Rangpur">Rangpur</option>
                    <option value="Dhaka">Dhaka</option>
                    <option value="Mymensingh">Mymensingh</option>
                </select><br><br>

                

                <input type="submit" value="Book Now">
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Event Management System</p>
    </footer>
</body>
</html>

