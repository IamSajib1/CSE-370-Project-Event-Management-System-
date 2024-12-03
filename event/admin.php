<?php
session_start();

// Allow access to package.php without login
if (basename($_SERVER['PHP_SELF']) !== 'package.php' && (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true)) {
    header('Location: login.php');
    exit();
}

// Configuration
$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'event_management';

// Create connection
$con = mysqli_connect($db_host, $db_username, $db_password, $db_name);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!mysqli_select_db($con, $db_name)) {
    die("Database '$db_name' does not exist");
}

if (isset($_POST['delete_order']) && !empty($_POST['delete_order_id'])) {
    $order_id = $_POST['delete_order_id'];

    $delete_query = "DELETE FROM orders WHERE id = ?";
    $stmt = $con->prepare($delete_query);
    $stmt->bind_param('i', $order_id);

    if ($stmt->execute()) {
        echo "Order ID $order_id has been deleted successfully.";
        header("Refresh:0");
    } else {
        echo "Error deleting order: " . mysqli_error($con);
    }
}

if (isset($_POST['delete_review']) && !empty($_POST['delete_review_id'])) {
    $review_id = $_POST['delete_review_id'];

    $delete_review_query = "DELETE FROM reviews WHERE id = ?";
    $stmt = $con->prepare($delete_review_query);
    $stmt->bind_param('i', $review_id);

    if ($stmt->execute()) {
        echo "Review ID $review_id has been deleted successfully.";
        header("Refresh:0");
    } else {
        echo "Error deleting review: " . mysqli_error($con);
    }
}
if (isset($_POST['delete_message']) && !empty($_POST['delete_message_id'])) {
    $message_id = $_POST['delete_message_id'];

    $delete_message_query = "DELETE FROM contact_messages WHERE id = ?";
    $stmt = $con->prepare($delete_message_query);
    $stmt->bind_param('i', $message_id);

    if ($stmt->execute()) {
        echo "Message ID $message_id has been deleted successfully.";
        header("Refresh:0");
    } else {
        echo "Error deleting message: " . mysqli_error($con);
    }
}

if (isset($_POST['update_catering']) && !empty($_POST['order_id']) && !empty($_POST['catering'])) {
    $order_id = $_POST['order_id'];
    $new_catering = $_POST['catering'];

    $update_query = "UPDATE orders SET catering = ?, status = 'Booked' WHERE id = ?";
    $stmt = $con->prepare($update_query);
    $stmt->bind_param('si', $new_catering, $order_id);

    if ($stmt->execute()) {
        echo "Catering option for Order ID $order_id has been updated successfully and status changed to 'Booked'.";
        header("Refresh:0");
    } else {
        echo "Error updating catering: " . mysqli_error($con);
    }
}

$query = "
    SELECT orders.id, orders.package_name, orders.customer_name, orders.order_date, orders.event_date,  -- Fetch the event date
           orders.status AS status, 
           orders.catering, packages.package_type, packages.price
    FROM orders
    INNER JOIN (
        SELECT package_name AS name, price, 'Wedding' AS package_type FROM wedding_packages
        UNION
        SELECT package_name AS name, price, 'Fashion' AS package_type FROM fashion_packages
        UNION
        SELECT package_name AS name, price, 'Birthday' AS package_type FROM birthday_packages
        UNION
        SELECT package_name AS name, price, 'Corporate' AS package_type FROM corporate_packages
    ) AS packages ON orders.package_name = packages.name";
$result = mysqli_query($con, $query);

// Error handling for orders query
if (!$result) {
    die("Error fetching orders: " . mysqli_error($con));
}

$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch the reviews
$query = "SELECT id, name, review, rating FROM reviews";
$result = mysqli_query($con, $query);
$reviews = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch the contact messages
$query = "SELECT * FROM contact_messages";
$result = mysqli_query($con, $query);
$contact_messages = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Close the database connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS - Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<header>
    <nav>
        <div class="logo">
            Admin Panel
        </div>
        <div class="menu">
            <a href="edit_packages.php">Edit Packages</a>
            <a href="userdetails.php">Users</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>
</header>

<main>
    <!-- Orders Table -->
    <h1>Orders</h1>
    <div class="order-box">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Package Name</th>
                    <th>Package Type</th>
                    <th>Price</th>
                    <th>Customer Name</th>
                    <th>Order Date</th>
                    <th>Event Date</th> <!-- New Event Date column -->
                    <th>Status</th>
                    <th>Catering Options</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['package_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['package_type']); ?></td>
                        <td><?php echo htmlspecialchars($order['price']); ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                        <td><?php echo htmlspecialchars($order['event_date']); ?></td> <!-- Display the event date -->
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                        <td>
                            <!-- Catering Update Form -->
                            <form action="" method="POST">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="catering">
                                    <option value="No catering" <?php echo $order['catering'] == 'No catering' ? 'selected' : ''; ?>>No catering</option>
                                    <option value="Basic catering" <?php echo $order['catering'] == 'Basic catering' ? 'selected' : ''; ?>>Basic catering</option>
                                    <option value="Moderate Catering" <?php echo $order['catering'] == 'Moderate Catering' ? 'selected' : ''; ?>>Moderate Catering</option>
                                    <option value="Premium catering" <?php echo $order['catering'] == 'Premium catering' ? 'selected' : ''; ?>>Premium catering</option>
                                </select>
                                <button type="submit" name="update_catering">Update</button>
                            </form>
                        </td>
                        <td>
                            <!-- Delete Order Form -->
                            <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                <input type="hidden" name="delete_order_id" value="<?php echo $order['id']; ?>">
                                <button type="submit" name="delete_order">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Reviews Table -->
    <h1>Reviews</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Review</th>
                <th>Rating</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reviews as $review) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($review['id']); ?></td>
                    <td><?php echo htmlspecialchars($review['name']); ?></td>
                    <td><?php echo htmlspecialchars($review['review']); ?></td>
                    <td><?php echo htmlspecialchars($review['rating']); ?></td>
                    <td>
                        <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this review?');">
                            <input type="hidden" name="delete_review_id" value="<?php echo $review['id']; ?>">
                            <button type="submit" name="delete_review">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Contact Messages Table -->
    <h1>Contact Messages</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contact_messages as $message) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($message['id']); ?></td>
                    <td><?php echo htmlspecialchars($message['name']); ?></td>
                    <td><?php echo htmlspecialchars($message['email']); ?></td>
                    <td><?php echo htmlspecialchars($message['message']); ?></td>
                    <td>
                        <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this message?');">
                            <input type="hidden" name="delete_message_id" value="<?php echo $message['id']; ?>">
                            <button type="submit" name="delete_message">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</main>

</body>
</html>
