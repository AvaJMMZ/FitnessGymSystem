<?php
@include '../conn/config.php';

$query = "SELECT username, firstname, middlename, lastname, address, contact, email, password FROM tbl_admin";
$result = mysqli_query($conn, $query);

if ($result) {
    $adminData = mysqli_fetch_assoc($result);
}

$tquery = "SELECT tranID, memberID, status, typeMember, dateStarted, endEnded FROM tbltransaction";
$tresult = mysqli_query($conn, $tquery);

if ($tresult) {
    $transactions = mysqli_fetch_all($tresult, MYSQLI_ASSOC);
} else {
    echo "Error executing transaction query: " . mysqli_error($conn);
}

mysqli_free_result($tresult);


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['editAdminForm'])) {
    $username = $_POST['username'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];

    $sql = "UPDATE tbl_admin SET username=?, firstname=?, middlename=?, lastname=?, address=?, contact=?, email=?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssssss", $username, $firstname, $middlename, $lastname, $address, $contact, $email);
        if (mysqli_stmt_execute($stmt)) {
            header("location: AdminPage.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['changePasswordForm'])) {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    $storedPassword = $adminData['password'];

    if (password_verify($currentPassword, $storedPassword)) {
        if ($newPassword === $confirmPassword) {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            $updatePasswordQuery = "UPDATE tbl_admin SET password=? WHERE username=?";
            if ($stmt = mysqli_prepare($conn, $updatePasswordQuery)) {
                mysqli_stmt_bind_param($stmt, "ss", $hashedPassword, $adminData['username']);

                if (mysqli_stmt_execute($stmt)) {
                    header("location: AdminPage.php");
                    exit();
                } else {
                    echo "Oops! Something went wrong while updating the password. Please try again later.";
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else {
            echo "New password and confirm password don't match.";
        }
    } else {
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

            <div class="viewTran-con">
                <div class="btn-viewTran">
                    <button><a href="pgSales.php" class="sales-button">Sales</a></button>
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

        <div class="logout-con">
            <a href="AdminLogin.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>LOGOUT</span>
            </a>
        </div>

        <footer>&copy; 2024 VFlex Fitness Gym System. All rights reserved.</footer>

        <div id="editAdmin" class="editAdmin-con">
            <div class="editAdmin-content">
                <span class="close">&times;</span>
                <h2>Edit Admin Details</h2>
                <form id="editAdminForm" action="AdminPage.php" method="post">
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
                    <button type="submit" name="editAdminForm">Save Changes</button>
                </form>
            </div>
        </div>

        <div id="changePassword" class="changePassword-con">
            <div class="changePassword-content">
                <span class="close">&times;</span>
                <h2>Change Password</h2>
                <form id="changePasswordForm" action="AdminPage.php" method="post">
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
                    <div class="show-password">
                        <input type="checkbox" id="showPasswordCheckbox">
                        <label for="showPasswordCheckbox">Show Password</label>
                    </div>
                    <button type="submit" name="changePasswordForm">Change Password</button>
                </form>
            </div>
        </div>

        <div id="transactionPopup" class="transaction-popup">
            <div class="transaction-popup-content">
                <span class="close" id="closePopup">&times;</span>
                <h2>Transactions</h2>
                <table id="transactionTable">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Member ID</th>
                            <th>Status</th>
                            <th>Type of Member</th>
                            <th>Date Started</th>
                            <th>Date Ended</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Loop through each transaction data and echo it as table rows
                        foreach ($transactions as $transaction) {
                            echo "<tr>";
                            echo "<td>" . $transaction['tranID'] . "</td>";
                            echo "<td>" . $transaction['memberID'] . "</td>";
                            echo "<td>" . $transaction['status'] . "</td>";
                            echo "<td>" . $transaction['typeMember'] . "</td>";
                            echo "<td>" . $transaction['dateStarted'] . "</td>";
                            echo "<td>" . $transaction['endEnded'] . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        document.getElementById("btnManageMem").addEventListener("click", function() {
            window.location.href = "pgDailyMember.php";
        });

        var editButton = document.getElementById("btnEdit");
        var editPopup = document.getElementById("editAdmin");

        var closeButton = editPopup.querySelector(".close");

        editButton.addEventListener("click", function() {
            editPopup.style.display = "block";
        });

        closeButton.addEventListener("click", function() {
            editPopup.style.display = "none";
        });

        var changePasswordButton = document.getElementById("btnChangePassword");
        var changePasswordPopup = document.getElementById("changePassword");

        var passwordCloseButton = changePasswordPopup.querySelector(".close");

        changePasswordButton.addEventListener("click", function() {
            changePasswordPopup.style.display = "block";
        });

        passwordCloseButton.addEventListener("click", function() {
            changePasswordPopup.style.display = "none";
        });

        function openTransactionPopup() {
            var popup = document.getElementById("transactionPopup");
            popup.style.display = "block";
        }

        // Function to close the transaction popup
        function closeTransactionPopup() {
            var popup = document.getElementById("transactionPopup");
            popup.style.display = "none";
        }

        // Attach event listener to the View Transactions button
        document.getElementById("btnViewTran").addEventListener("click", openTransactionPopup);

        // Attach event listener to the close button within the transaction popup
        document.getElementById("closePopup").addEventListener("click", closeTransactionPopup);

        function togglePasswordVisibility(inputId) {
            var x = document.getElementById(inputId);
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        // Event listener for checkbox
        document.getElementById("showPasswordCheckbox").addEventListener("change", function() {
            togglePasswordVisibility("currentPassword");
            togglePasswordVisibility("newPassword");
            togglePasswordVisibility("confirmPassword");
        });
    </script>
    </div>
</body>

</html>