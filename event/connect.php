<?php
$host = "localhost"; // Database host
$dbname = "event_management"; // Database name
$username = "root"; // Database username
$password = ""; // Database password

// Create a connection to the MySQL database
$con = new mysqli($host, $username, $password, $dbname);

// Check if the connection was successful
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
?>
