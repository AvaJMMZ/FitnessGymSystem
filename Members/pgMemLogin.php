<?php
session_start();
@include '../conn/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM tblmember WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if ($row['status'] === 'pending' || $row['status'] === 'pending renewal') {
            $error_message = "Your account is still pending. Please wait for approval.";
        } else {
            if (password_verify($password, $row['password'])) {
                $_SESSION['memberID'] = $row['memberID'];
                $_SESSION['username'] = $username;
                header("Location: memberMainPage.php");
                exit();
            } else {
                $error_message = "Incorrect username or password!";
            }
        }
    } else {
        $error_message = "Incorrect username or password!";
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
    <link rel="stylesheet" href="../Styles/styleMemLogin.css">
    <title>Member Login</title>
</head>

<body>
    <nav>
        <div class="icon"><i class='fas fa-dumbbell'></i></div>
        <div class="logo">VFlex</div>
    </nav>

    <div class="main">
        <div class="main-content">
            <div class="setup-con">
                <h1>LOGIN FORM</h1>

                <form action="pgMemLogin.php" method="post" id="loginForm">
                    <div id="error" class="error-message"><?php echo isset($error_message) ? $error_message : ''; ?></div>
                    <div class="textbox">
                        <input type="text" id="username" name="username" required>
                        <span>Username</span>
                    </div>

                    <div class="textbox">
                        <input type="password" id="password" name="password" required>
                        <span>Password</span>
                    </div>

                    <div class="showpass">
                        <input type="checkbox" id="showPasswordCheckbox" onclick="togglePasswordVisibility()">
                        <label for="showPasswordCheckbox">Show Password</label>
                    </div>


                    <input type="submit" class="btn" value="LOGIN">
                </form>

                <div class="register-link">
                    <p>Don't have an account yet? <a href="pgRegister.php">Register here</a>.</p>
                </div>
            </div>

            <div class="img-con">
                <img src="../WebImages/ror.jpg" alt="background">
            </div>
        </div>

        <footer>&copy; 2024 VFlex Fitness Gym System. All rights reserved.</footer>

        <script>
            var errorMessage = document.getElementById('error');
            errorMessage.style.display = '<?php echo isset($error_message) ? 'block' : 'none'; ?>';

            setTimeout(function() {
                errorMessage.style.display = 'none';
            }, 5000);

            function togglePasswordVisibility() {
                var passwordInput = document.getElementById('password');
                var checkbox = document.getElementById('showPasswordCheckbox');
                passwordInput.type = checkbox.checked ? 'text' : 'password';
            }
        </script>
    </div>
</body>

</html>