<?php
session_start();
date_default_timezone_set('Asia/Manila');
@include '../conn/config.php';

if (isset($_SESSION['memberID'])) {
    $memberID = $_SESSION['memberID'];
} else {
    header("Location: pgMemLogin.php");
    exit();
}

$query = "SELECT m.*, s.* 
          FROM tblmember m 
          LEFT JOIN tblsessions s ON m.memberID = s.memberID 
          WHERE m.memberID = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $memberID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$memberData = mysqli_fetch_assoc($result);
mysqli_free_result($result);
mysqli_stmt_close($stmt);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['editDetails'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $birthdate = mysqli_real_escape_string($conn, $_POST['birthdate']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);

    // Update user's information in the database
    $updateQuery = "UPDATE tblmember SET name = ?, email = ?, gender = ?, birthdate = ?, address = ?, mobile = ? WHERE memberID = ?";
    $updateStmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, "ssssssi", $name, $email, $gender, $birthdate, $address, $mobile, $memberID);
    $success = mysqli_stmt_execute($updateStmt);

    // Check if the update was successful
    if ($success) {
        // Redirect back to the profile page with a success message
        header("Location: memProfile.php");
        exit();
    } else {
        // Redirect back to the profile page with an error message
        header("Location: memProfile.php?error=1");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['changeprogBtn'])) {
    $tmemberID = $memberData['memberID'];
    $tstatus = $memberData['status'];
    $ttypeMember = $memberData['typeMember'];
    $tdateStarted = $memberData['dateAdded'];
    $tdateEnded = $memberData['endDate'];

    $insertQuery = "INSERT INTO tbltransaction (memberID, status, typeMember, dateStarted, endEnded) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insertQuery);

    mysqli_stmt_bind_param($stmt, "issss", $tmemberID, $tstatus, $ttypeMember, $tdateStarted, $tdateEnded);
    if (!mysqli_stmt_execute($stmt)) {
        echo "Error inserting into transaction table: " . mysqli_error($conn);
        exit();
    }

    $newtypeMember = $_POST['program'];
    $newdateAdded = date('Y-m-d H:i:s');
    $newdateEnd = null;
    $newstatus = 'pending renewal';

    $updateQuery = "UPDATE tblmember SET status = ?, typeMember = ?, dateAdded = ?, endDate = ? WHERE memberID = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ssssi", $newstatus, $newtypeMember, $newdateAdded, $newdateEnd, $memberID);
    if (!mysqli_stmt_execute($stmt)) {
        echo "Error updating member data: " . mysqli_error($conn);
        exit();
    }
    mysqli_stmt_close($stmt);

    header("Location: pgMemLogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="icon" type="image/png" href="../WebImages/logo.jpg">
    <link rel="stylesheet" href="../Styles/style_memProfile.css">
    <title>Member Profile</title>
</head>

<body>
    <nav>
        <div class="icon"><i class='fas fa-dumbbell'></i></div>
        <div class="logo">VFlex</div>
    </nav>

    <div class="main">
        <div class="back-btn">
            <a href="memberMainPage.php">
                <button type="button">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
            </a>
        </div>

        <div class="main-content">
            <div class="profile-container">
                <div class="profile-header">
                    <h1 class="profile-heading">Your Profile Details</h1>
                    <button id="toggleFormBtn" class="toggle-form-btn"></button>
                </div>
                <div class="profile-details">
                    <div class="profile-detail">
                        <label for="memberID">Member ID:</label>
                        <span id="memberID"><?php echo $memberData['memberID']; ?></span>
                    </div>
                    <div class="profile-detail">
                        <label for="name">Name:</label>
                        <span id="name"><?php echo $memberData['name']; ?></span>
                    </div>
                    <div class="profile-detail">
                        <label for="gender">Gender:</label>
                        <span id="gender"><?php echo $memberData['gender']; ?></span>
                    </div>
                    <div class="profile-detail">
                        <label for="birthdate">Birthdate:</label>
                        <span id="birthdate"><?php echo date('F j, Y', strtotime($memberData['birthdate'])); ?></span>
                    </div>
                    <div class="profile-detail">
                        <label for="address">Address:</label>
                        <span id="address"><?php echo $memberData['address']; ?></span>
                    </div>
                    <div class="profile-detail">
                        <label for="mobile">Mobile:</label>
                        <span id="mobile"><?php echo $memberData['mobile']; ?></span>
                    </div>
                    <div class="profile-detail">
                        <label for="email">Email:</label>
                        <span id="email" style="text-transform: lowercase;"><?php echo $memberData['email']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="changeprog-btn">
            <button type="button" id="openChangeprogBtn">Open Change Program Form</button>
        </div>
    </div>

    <!-- Profile Form -->
    <div class="form-container" style="display: none;">
        <h2 class="form-heading">Edit Profile</h2>
        <form id="profileForm" action="memProfile.php" method="post" class="profile-form">
            <input type="hidden" name="memberID" value="<?php echo $memberData['memberID']; ?>">

            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $memberData['name']; ?>" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="Male" <?php if ($memberData['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($memberData['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                </select>
            </div>

            <div class="form-group">
                <label for="birthdate">Birthdate:</label>
                <input type="date" id="birthdate" name="birthdate" value="<?php echo $memberData['birthdate']; ?>" required>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo $memberData['address']; ?>" required>
            </div>

            <div class="form-group">
                <label for="mobile">Mobile:</label>
                <input type="tel" id="mobile" name="mobile" value="<?php echo $memberData['mobile']; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $memberData['email']; ?>" required>
            </div>

            <button type="submit" class="btn-save" name="editDetails">Save Changes</button>
        </form>
    </div>


    <footer>&copy; 2024 VFlex Fitness Gym System. All rights reserved.</footer>

    <!-- Change Program -->
    <div class="overlay" id="overlay" style="display: none;"></div>
    <div class="changeprog-con" style="display: none;">
        <h2>YOUR CURRENT PROGRAM IS: <?php echo $memberData['typeMember']; ?></h2>
        <form id="changeprogForm" method="post" action="memProfile.php">
            <label for="program">Choose Program:</label>
            <select id="program" name="program">
                <option value="" disabled selected>Select Membership Type</option>
                <option value="Daily Member">Daily Member</option>
                <option value="Monthly Member">Monthly Member</option>
                <option value="With Instructor Member">With Instructor Member</option>
            </select>
            <button type="submit" name="changeprogBtn">Change Program</button>
            <button type="button" id="cancelBtn" onclick="cancelChangeprog()">Cancel</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var toggleFormBtn = document.getElementById("toggleFormBtn");
            var formContainer = document.querySelector(".form-container");

            toggleFormBtn.addEventListener("click", function() {
                if (window.getComputedStyle(formContainer).display === "none") {
                    formContainer.style.display = "block";
                    toggleFormBtn.innerHTML = '<i class="fas fa-times"></i>';
                } else {
                    formContainer.style.display = "none";
                    toggleFormBtn.innerHTML = '<i class="fas fa-edit"></i>';
                }
            });

            if (window.getComputedStyle(formContainer).display === "none") {
                toggleFormBtn.innerHTML = '<i class="fas fa-edit"></i>';
            } else {
                toggleFormBtn.innerHTML = '<i class="fas fa-times"></i>';
            }
        });

        const overlay = document.getElementById("overlay");
        const changeprogCon = document.querySelector(".changeprog-con");

        document.getElementById("openChangeprogBtn").addEventListener("click", function() {
            // Check if the current program is not a daily member
            if ("<?php echo $memberData['typeMember']; ?>" !== "Daily Member") {
                // If not a daily member, display the confirmation alert
                var confirmChange = confirm("Your current program is not yet expired. Are you sure you want to change program?");
                if (!confirmChange) {
                    return; // If user cancels, do nothing
                }
            }

            // If confirmed or current program is a daily member, proceed to display the form
            changeprogCon.style.display = "block";
            overlay.style.display = "block";
        });

        function cancelChangeprog() {
            changeprogCon.style.display = "none";
            overlay.style.display = "none";
        }

        overlay.addEventListener("click", function(event) {
            if (event.target === overlay) {
                cancelChangeprog();
            }
        });
    </script>
</body>

</html>