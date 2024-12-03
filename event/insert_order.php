<?php
session_start();
include("connect.php");


if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}


$package_name = $_POST['package_name'];
$price = $_POST['price'];
$customer_name = $_POST['customer_name'];
$customer_email = $_SESSION['email'];
$order_date = $_POST['order_date'];
$event_date = $_POST['event_date'];
$venue = $_POST['venue'];

$stmt = $con->prepare("INSERT INTO orders (package_name, price, customer_name, customer_email, order_date, event_date, venue, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
$stmt->bind_param("sdsssss", $package_name, $price, $customer_name, $customer_email, $order_date, $event_date, $venue);

if ($stmt->execute()) {
    header("Location: user_profile.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}
?>
