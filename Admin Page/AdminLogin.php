<?php
@include '../conn/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "SELECT * FROM tbl_admin WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Verify the hashed password
        if (password_verify($password, $row['password'])) {
            // Password is correct
            header("Location: pgDailyMember.php");
            exit();
        } else {
            // Password is incorrect
            $error = "Invalid Username or Password!";
        }
    } else {
        $error = "Invalid Username or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="icon" type="image/png" href="../WebImages/logo.jpg">
    <link rel="stylesheet" href="../Styles/AdminLogin.css">
    <title>Admin Login</title>
</head>

<body>
    <nav>
        <div class="icon"><i class='fas fa-dumbbell'></i></div>
        <div class="logo">VFlex</div>
    </nav>

    <div class="main">
        <div class="main-content">
            <div class="login-container">
                <form action="AdminLogin.php" method="post">
                    <h2>ADMIN LOGIN</h2>

                    <div class="msg-con">
                        <?php if (isset($error)) : ?>
                            <div class="error-message" id="error-msg"><?php echo $error; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="textbox" style="margin-bottom: 1rem;">
                        <input type="text" name="username" required="required">
                        <div class="F-icon"><i class='fas fa-user-circle'></i></div>
                        <span>Username</span>
                    </div>
                    <div class="textbox">
                        <input type="password" name="password" id="passwordField" required="required">
                        <div class="F-icon"><i class="fa fa-lock"></i></div>
                        <span>Password</span>
                    </div>

                    <div class="showpass-container">
                        <input type="checkbox" id="showPassword">
                        <label for="showPassword">Show Password</label>
                    </div>

                    <div class="btn-con">
                        <button type="submit">LOGIN</button>
                    </div>
                </form>
            </div>

            <div class="img-con">
                <img src="../WebImages/ror.jpg" alt="background">
            </div>
        </div>

        <footer>&copy; 2024 VFlex Fitness Gym System. All rights reserved.</footer>

        <script>
            const passwordField = document.getElementById("passwordField");
            const showPasswordCheckbox = document.getElementById("showPassword");

            showPasswordCheckbox.addEventListener("change", function() {
                passwordField.type = this.checked ? "text" : "password";
            });

            //Error Message Last for 5sec
            document.addEventListener("DOMContentLoaded", function() {
                var errorMessage = document.getElementById("error-msg");

                if (errorMessage) {
                    setTimeout(function() {
                        errorMessage.style.display = "none";
                    }, 3000);
                }
            });
        </script>
    </div>
</body>

</html>