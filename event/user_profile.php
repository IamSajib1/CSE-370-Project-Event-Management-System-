<?php
session_start();
include("connect.php");


if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$stmt = $con->prepare("SELECT fullname, email FROM signup WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();


if (isset($_POST['cancel_order_id'])) {
    $order_id = $_POST['cancel_order_id'];

    $update_query = "UPDATE orders SET status = 'Cancellation Requested' WHERE id = ? AND customer_email = ?";
    $stmt = $con->prepare($update_query);
    $stmt->bind_param('is', $order_id, $email);

    if ($stmt->execute()) {
        header("Location: user_profile.php");
        exit();
    } else {
        echo "Error updating order: " . mysqli_error($con);
    }
}


$stmt = $con->prepare("
    SELECT orders.id AS order_id, 
           orders.package_name, 
           orders.price, 
           orders.order_date, 
           orders.event_date,  -- Fetch the event date
           orders.status AS status, 
           orders.venue, 
           packages.package_type 
    FROM orders 
    INNER JOIN (
        SELECT package_name AS name, price, 'Wedding' AS package_type FROM wedding_packages
        UNION
        SELECT package_name AS name, price, 'Fashion' AS package_type FROM fashion_packages
        UNION
        SELECT package_name AS name, price, 'Birthday' AS package_type FROM birthday_packages
        UNION
        SELECT package_name AS name, price, 'Corporate' AS package_type FROM corporate_packages
    ) AS packages ON orders.package_name = packages.name 
    WHERE orders.customer_email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$order_result = $stmt->get_result();
$orders = $order_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="user_profile.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">User Profile</div>
            <div class="menu">
                <a href="package.php">Package</a>
            </div>
            <div class="logout">
                <a href="logout.php">Logout</a>
            </div>
        </nav>
    </header>

    <main>
        <h1>Welcome, <?php echo htmlspecialchars($user['fullname']); ?>!</h1>
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        
        <h2>Your Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Package Name</th>
                    <th>Package Type</th>
                    <th>Price</th>
                    <th>Order Date</th>
                    <th>Event Date</th> 
                    <th>Venue</th> 
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="9">No orders found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['package_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['package_type']); ?></td>
                            <td><?php echo htmlspecialchars($order['price']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td><?php echo htmlspecialchars($order['event_date']); ?></td> 
                            <td><?php echo htmlspecialchars($order['venue']); ?></td> 
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td>
                                <?php if ($order['status'] !== 'Cancellation Requested'): ?>
                                    <form action="" method="POST" onsubmit="return confirm('Are you sure you want to request cancellation of this order?');">
                                        <input type="hidden" name="cancel_order_id" value="<?php echo $order['order_id']; ?>">
                                        <button type="submit">Request Cancellation</button>
                                    </form>
                                <?php else: ?>
                                    <span>Cancellation Requested</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <footer>
        <p>&copy; 2024 Event Management System</p>
    </footer>
</body>
</html>

<?php

$con->close();
?>
