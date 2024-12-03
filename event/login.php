<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {

        $stmt = $con->prepare("SELECT email, password FROM signup WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();


        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashed_password = $row['password'];

 
            if (password_verify($password, $hashed_password)) {
  
                $_SESSION['email'] = $email;  
                $_SESSION['admin_logged_in'] = true;  
                header("Location: package.php"); 
                exit();
            } else {
                $error_message = "Email and password don't match.";
            }
        } else {
            $error_message = "No user found with this email";
        }
    } else {
        $error_message = "Please enter valid information";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="login.css">
    <title>Login to EMS</title>
</head>
<body>
    <section>
        <div class="form-box">
            <div class="form-value">

                <form action="" method="POST">
                    <h2>Login</h2>
                    <div class="inputbox">
                        <ion-icon name="mail-outline"></ion-icon>

                        <input type="email" name="email" required>
                        <label for="">Email</label>
                    </div>
                    <div class="inputbox">
                        <ion-icon name="lock-closed-outline"></ion-icon>

                        <input type="password" name="password" id="password" required>
                        <label for="">Password</label>

                        <ion-icon name="eye-off-outline" id="toggle-password" class="toggle-password"></ion-icon>
                    </div>
                    <button type="submit">Log in</button>
                    <div class="register">
                        <p>Don't have an account? <a href="sign.php">Signup</a> now!</p>
                        <p class="manager-login">One of the managers? <a href="managerlog.php">Login here!</a></p>
                    </div>
                    <?php if (isset($error_message)) { ?>
                        <p style="color: red; text-align: center;"><?php echo $error_message; ?></p>
                    <?php } ?>
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
