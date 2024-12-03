<?php
session_start();
ob_start(); 

$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'event_management';


$con = mysqli_connect($db_host, $db_username, $db_password, $db_name);


if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}


$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $password = $_POST["password"];

    $query = "SELECT password FROM admin_users WHERE name = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 's', $name);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $stored_hashed_password);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);


    if ($stored_hashed_password && password_verify($password, $stored_hashed_password)) {

        $_SESSION['logged_in'] = true;

        ob_end_flush();
        header("Location: admin.php");
        exit;
    } else {
        $error_message = "Invalid name or password."; 
    }
}


mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="login.css">
    <title>Manager Login</title>
</head>
<body>
    <section>
        <div class="form-box">
            <div class="form-value">
                <form action="" method="POST">
                    <h2>M-Login</h2>
                    <div class="inputbox">
                        <ion-icon name="person-outline"></ion-icon>
                        <input type="text" name="name" required>
                        <label for="">Name</label>
                    </div>
                    <div class="inputbox">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" name="password" id="password" required>
                        <label for="">Password</label>
                        <ion-icon name="eye-off-outline" id="toggle-password" class="toggle-password"></ion-icon>
                    </div>

           
                    <?php if (!empty($error_message)) { ?>
                        <div class="error-message" style="color: red; font-size: 14px;">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php } ?>

                    <div class="forget">
                     
                    </div>
                    <button type="submit">Log in</button>
                </form>
            </div>
        </div>
    </section>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script>
      
        const passwordField = document.getElementById('password');
        const togglePassword = document.getElementById('toggle-password');

        togglePassword.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.setAttribute('name', type === 'password' ? 'eye-off-outline' : 'eye-outline');
        });
    </script>
</body>
</html>
