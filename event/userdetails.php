<?php
session_start();

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

// Handle delete request
if (isset($_POST['delete_user']) && !empty($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // Prepare and execute the delete query
    $delete_query = "DELETE FROM signup WHERE id = ?";
    $stmt = $con->prepare($delete_query);
    $stmt->bind_param('i', $user_id);

    if ($stmt->execute()) {
        echo "User ID $user_id has been deleted successfully.";
        header("Refresh:0");
    } else {
        echo "Error deleting user: " . mysqli_error($con);
    }
}

// Fetch users from the signup table
$query = "SELECT id, fullname, email FROM signup";
$result = mysqli_query($con, $query);

// Error handling for users query
if (!$result) {
    die("Error fetching users: " . mysqli_error($con));
}

$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Close the database connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link rel="stylesheet" href="userdetails.css"> 
</head>
<body>

<header>
    <nav>
        <div class="logo">User Management</div>
        <div class="menu">
            <a href="admin.php">Admin Panel</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>
    <h1>User Details</h1>
</header>

<main>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <!-- Delete User Form -->
                        <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="delete_user">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</main>

</body>
</html>
