<?php
@include '../conn/config.php';

// Fetch data from tbl_admin
$query = "SELECT username, firstname, middlename, lastname, address, contact, email FROM tbl_admin";
$result = mysqli_query($conn, $query);

// Check if query executed successfully
if ($result) {
    // Fetch data row by row
    $adminData = mysqli_fetch_assoc($result);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editAdminForm'])) {
    // Retrieve form data
    $username = $_POST['username'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];

    // Update admin information in the database
    $sql = "UPDATE tbl_admin SET username=?, firstname=?, middlename=?, lastname=?, address=?, contact=?, email=?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "sssssss", $username, $firstname, $middlename, $lastname, $address, $contact, $email);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Redirect back to AdminPage.php
            header("location: AdminPage.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }


    // Close connection
    mysqli_close($conn);
}

// Check if form is submitted for changing password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['changePasswordForm'])) {
    // Retrieve form data for changing password
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Your logic to change the password securely
    // For example, you can check if the current password matches and if the new password and confirm password match
    // You should also hash the new password before storing it in the database

    // Check if the current password matches the one stored in the database
    $storedPassword = ""; // Retrieve the stored password from the database
    if (password_verify($currentPassword, $storedPassword)) {
        // Check if the new password matches the confirm password
        if ($newPassword === $confirmPassword) {
            // Hash the new password securely
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the password in the database
            $updatePasswordQuery = "UPDATE tbl_admin SET password=? WHERE username=?";
            if ($stmt = mysqli_prepare($conn, $updatePasswordQuery)) {
                // Bind parameters
                mysqli_stmt_bind_param($stmt, "ss", $hashedPassword, $adminData['username']);

                // Execute the statement
                if (mysqli_stmt_execute($stmt)) {
                    // Password updated successfully
                    // You may redirect to a success page or display a success message
                    header("location: AdminPage.php");
                    exit();
                } else {
                    // Error updating password
                    echo "Oops! Something went wrong while updating the password. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            } else {
                // Error preparing statement
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else {
            // New password and confirm password don't match
            echo "New password and confirm password don't match.";
        }
    } else {
        // Current password is incorrect
        echo "Incorrect current password.";
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
    <link rel="stylesheet" href="../Styles/styleProfileAdmin.css">
    <title>Admin Profile Page</title>
</head>

<body>
    <nav>
        <div class="icon"><i class='fas fa-dumbbell'></i></div>
        <div class="logo">VFlex</div>
    </nav>

    <div class="main">
        <div class="header">
            <div class="manageMem-con">
                <div class="btn-manageMem">
                    <button id="btnManageMem">Manage Members</button>
                </div>
            </div>

            <div class="viewTran-con">
                <div class="btn-viewTran">
                    <button id="btnViewTran">View Transactions</button>
                </div>
            </div>
        </div>

        <div class="main-content">
            <h1>Admin Profile</h1>
            <div class="btn-con">
                <button id="btnEdit">Edit</button>
                <button id="btnChangePassword">Change Password</button>
            </div>
            <div class="admin-details">
                <p><strong>Username:</strong> <?php echo $adminData['username']; ?></p>
                <p><strong>Name:</strong> <?php echo $adminData['firstname'] . ' ' . $adminData['middlename'] . ' ' . $adminData['lastname']; ?></p>
                <p><strong>Address:</strong> <?php echo $adminData['address']; ?></p>
                <p><strong>Contact:</strong> <?php echo $adminData['contact']; ?></p>
                <p><strong>Email:</strong> <?php echo $adminData['email']; ?></p>
            </div>
        </div>

        <footer>&copy; 2024 VFlex Fitness Gym System. All rights reserved.</footer>

        <div id="editAdmin" class="editAdmin-con">
            <div class="editAdmin-content">
                <span class="close">&times;</span>
                <h2>Edit Admin Details</h2>
                <form id="editAdminForm" name="editAdminForm" action="AdminPage.php" method="post">
                    <div class="textbox">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" value="<?php echo $adminData['username']; ?>" required>
                    </div>
                    <div class="textbox">
                        <label for="firstname">First Name:</label>
                        <input type="text" id="firstname" name="firstname" value="<?php echo $adminData['firstname']; ?>" required>
                    </div>
                    <div class="textbox">
                        <label for="middlename">Middle Name:</label>
                        <input type="text" id="middlename" name="middlename" value="<?php echo $adminData['middlename']; ?>">
                    </div>
                    <div class="textbox">
                        <label for="lastname">Last Name:</label>
                        <input type="text" id="lastname" name="lastname" value="<?php echo $adminData['lastname']; ?>" required>
                    </div>
                    <div class="textbox">
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" value="<?php echo $adminData['address']; ?>">
                    </div>
                    <div class="textbox">
                        <label for="contact">Contact:</label>
                        <input type="text" id="contact" name="contact" value="<?php echo $adminData['contact']; ?>">
                    </div>
                    <div class="textbox">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo $adminData['email']; ?>" required>
                    </div>
                    <button type="submit">Save Changes</button>
                </form>
            </div>
        </div>

        <div id="changePassword" class="changePassword-con">
            <div class="changePassword-content">
                <span class="close">&times;</span>
                <h2>Change Password</h2>
                <form id="changePasswordForm" name="changePasswordForm" action="AdminPage.php" method="post">
                    <div class="textbox">
                        <label for="currentPassword">Current Password:</label>
                        <input type="password" id="currentPassword" name="currentPassword" required>
                    </div>
                    <div class="textbox">
                        <label for="newPassword">New Password:</label>
                        <input type="password" id="newPassword" name="newPassword" required>
                    </div>
                    <div class="textbox">
                        <label for="confirmPassword">Confirm Password:</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" required>
                    </div>
                    <button type="submit">Change Password</button>
                </form>
            </div>
        </div>

        <script>
            document.getElementById("btnManageMem").addEventListener("click", function() {
                window.location.href = "pgDailyMember.php";
            });

            var editButton = document.getElementById("btnEdit");
            var editAdmin = document.getElementById("editAdmin");

            var editCloseButton = editAdmin.querySelector(".close"); // Change variable name to avoid conflict

            editButton.addEventListener("click", function() {
                editAdmin.style.display = "block";
            });

            editCloseButton.addEventListener("click", function() { // Use the correct close button variable
                editAdmin.style.display = "none";
            });

            var changePasswordButton = document.getElementById("btnChangePassword");
            var changePasswordPopup = document.getElementById("changePassword");

            var passwordCloseButton = changePasswordPopup.querySelector(".close"); // Separate variable for close button

            changePasswordButton.addEventListener("click", function() {
                changePasswordPopup.style.display = "block";
            });

            passwordCloseButton.addEventListener("click", function() { // Use the correct close button variable
                changePasswordPopup.style.display = "none";
            });
        </script>
    </div>
</body>

</html>