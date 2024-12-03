<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: managerlog.php');
    exit;
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

$packages = [];
while ($row = mysqli_fetch_assoc($result)) {
    $packages[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_package'])) {
    $package_id = $_POST['package_id'];
    $package_type = $_POST['package_type'];


    $delete_query = "";
    switch ($package_type) {
        case 'wedding':
            $delete_query = "DELETE FROM wedding_packages WHERE id = ?";
            break;
        case 'corporate':
            $delete_query = "DELETE FROM corporate_packages WHERE id = ?";
            break;
        case 'birthday':
            $delete_query = "DELETE FROM birthday_packages WHERE id = ?";
            break;
        case 'fashion':
            $delete_query = "DELETE FROM fashion_packages WHERE id = ?";
            break;
    }

    if ($delete_query) {
        $stmt = $con->prepare($delete_query);
        $stmt->bind_param('i', $package_id);
        
        if ($stmt->execute()) {
            echo "Package deleted successfully.";
            header("Refresh:0");
        } else {
            echo "Error deleting package: " . mysqli_error($con);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_package'])) {
    $package_name = $_POST['new_package_name'];
    $price = $_POST['new_price'];
    $description = $_POST['new_description'];
    $contact_person = $_POST['new_contact_person'];
    $contact_phone = $_POST['new_contact_phone'];
    $package_type = $_POST['new_package_type'];

    $insert_query = "";
    switch ($package_type) {
        case 'wedding':
            $insert_query = "INSERT INTO wedding_packages (package_name, price, description, contact_person, contact_phone) VALUES (?, ?, ?, ?, ?)";
            break;
        case 'corporate':
            $insert_query = "INSERT INTO corporate_packages (package_name, price, description, contact_person, contact_phone) VALUES (?, ?, ?, ?, ?)";
            break;
        case 'birthday':
            $insert_query = "INSERT INTO birthday_packages (package_name, price, description, contact_person, contact_phone) VALUES (?, ?, ?, ?, ?)";
            break;
        case 'fashion':
            $insert_query = "INSERT INTO fashion_packages (package_name, price, description, contact_person, contact_phone) VALUES (?, ?, ?, ?, ?)";
            break;
    }

    if ($insert_query) {
        $stmt = $con->prepare($insert_query);
        $stmt->bind_param('sdsss', $package_name, $price, $description, $contact_person, $contact_phone);
        
        if ($stmt->execute()) {
            echo "New package added successfully.";
            header("Refresh:0");
        } else {
            echo "Error adding package: " . mysqli_error($con);
        }
    }
}


mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Packages - Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<header>
    <nav>
        <div class="logo">
            Admin Panel
        </div>
        <div class="menu">
            <a href="admin.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>
</header>

<main>
    <h1>Edit Packages</h1>

    <h2>Add New Package</h2>
    <form action="" method="POST">
        <input type="text" name="new_package_name" placeholder="Package Name" required>
        <input type="number" name="new_price" placeholder="Price" required step="0.01">
        <input type="text" name="new_description" placeholder="Description" required>
        <input type="text" name="new_contact_person" placeholder="Contact Person" required>
        <input type="text" name="new_contact_phone" placeholder="Contact Phone" required>
        <select name="new_package_type" required>
            <option value="wedding">Wedding</option>
            <option value="corporate">Corporate</option>
            <option value="birthday">Birthday</option>
            <option value="fashion">Fashion</option>
        </select>
        <button type="submit" name="add_package">Add Package</button>
    </form>

    <h1>Existing Packages</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Package Name</th>
                <th>Price</th>
                <th>Description</th>
                <th>Contact Person</th>
                <th>Contact Phone</th>
                <th>Package Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($packages as $package) { ?>
                <tr>
                    <td><?php echo $package['id']; ?></td>
                    <td><?php echo $package['package_name']; ?></td>
                    <td><?php echo $package['price']; ?></td>
                    <td><?php echo $package['description']; ?></td>
                    <td><?php echo $package['contact_person']; ?></td>
                    <td><?php echo $package['contact_phone']; ?></td>
                    <td><?php echo ucfirst($package['package_type']); ?></td>
                    <td>
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="package_id" value="<?php echo $package['id']; ?>">
                            <input type="hidden" name="package_type" value="<?php echo $package['package_type']; ?>">
                            <button type="submit" name="delete_package">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</main>

</body>
</html>
