<?php
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

// Check if database exists
if (!mysqli_select_db($con, $db_name)) {
    die("Database '$db_name' does not exist");
}

// Handle form submission to add a new review
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $review = $_POST["review"];
    $rating = $_POST["rating"];

    $query = "INSERT INTO reviews (name, review, rating) VALUES (?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ssi", $name, $review, $rating);
    $stmt->execute();

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit;
}

// Fetch the reviews from the database
$query = "SELECT id, name, review, rating FROM reviews";
$result = mysqli_query($con, $query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

// Fetch the reviews
$reviews = [];
while ($row = mysqli_fetch_assoc($result)) {
    $reviews[] = $row;
} 
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Reviews</title>
    <link rel="stylesheet" href="reviews.css">
</head>
<body>

    <header>
        <nav>
            <div class="logo">
                Reviews
            </div>
            <div class="menu">
                <a href="home.php">Home</a>
                <a href="contact.php">Contact</a>
            </div>
        </nav>
    </header>

    <section class="review-section">
        <div class="container">
            <div class="reviews-container">
                <?php foreach ($reviews as $review) { ?>
                    <div class="review-card">
                        <h3><?php echo $review['name']; ?></h3>
                        <p>"<?php echo $review['review']; ?>"</p>
                        <span class="rating">
                            <?php
                            for ($i = 0; $i < $review['rating']; $i++) {
                                echo '⭐';
                            }
                            ?>
                        </span>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <section class="add-review-section">
        <div class="form-container">
            <h3>Add Your Review</h3>
            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
                <div class="input-group">
                    <input type="text" id="name" name="name" placeholder="Name" required>
                </div>
                <div class="input-group">
                    <textarea id="review" name="review" placeholder="Review" rows="5" required></textarea>
                </div>
                <div class="input-group">
                    <select id="rating" name="rating" required>
                        <option value="" disabled selected>Rating</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <button type="submit" class="submit-btn">Add Review</button>
            </form>
        </div>
    </section>

</body>
</html>
