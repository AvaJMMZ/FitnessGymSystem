<?php
session_start();
@include '../conn/config.php';

// Ensure memberID is set and not empty
if (isset($_GET['memberID']) && !empty($_GET['memberID'])) {
    $memberID = $_GET['memberID'];
} else {
    exit(json_encode([]));
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

$query = "SELECT sessionID, memberID, sessionDate, body, paymentStatus FROM tblsessions WHERE memberID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $memberID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch session data
$sessions = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['start'] = $row['sessionDate'];
    $row['title'] = $row['body'];
    unset($row['sessionDate'], $row['body']);
    $sessions[] = $row;

    $memberIDForSales = $row['memberID'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="icon" type="image/png" href="../WebImages/logo.jpg">
    <link rel="stylesheet" href="../Styles/style_MemMainPage.css">
    <title>View Sessions</title>
</head>

<body>
    <div class="main">
        <div class="header">
            <div class="back-btn">
                <a href="pgDailyMember.php">
                    <button type="button">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                </a>
            </div>
        </div>

        <div class="main-content">
            <h1>Sessions of: <?php echo $memberData['name']; ?> | <span style="font-weight: lighter;"><?php echo $memberData['typeMember']; ?></span></h1>

            <div id="calendar"></div>
        </div>
    </div>

    <footer>&copy; 2024 VFlex Fitness Gym System. All rights reserved.</footer>

    <div id="sessionDetails" class="session-content" style="display: none;">
        <div class="sessionDetails-content">
            <span class="close">&times;</span>
            <strong>Session ID:</strong> <span id="sessionID"></span><br>
            <strong>Session Date:</strong> <span id="sessionDate"></span><br>
            <strong>Body:</strong> <span id="sessionBody"></span><br>
            <strong>Payment Status:</strong> <span id="paymentStatus"></span>
            <div id="paidButtonContainer" class="SD-btn" style="display: none;">
                <button id="paymentToggleButton" onclick="togglePaymentStatus()"></button>
                <button id="saveButton" onclick="savePaymentStatus()">Save</button>
            </div>
        </div>
    </div>


    <script src="../fullcalendar-6.1.11/fullcalendar-6.1.11/dist/index.global.min.js"></script>
    <script>
        var memberID = <?php echo json_encode($memberID); ?>;

        function togglePaymentStatus() {
            var button = document.getElementById("paymentToggleButton");
            var paymentStatusSpan = document.getElementById("paymentStatus");

            var currentPaymentStatus = paymentStatusSpan.textContent.toLowerCase();

            // Toggle payment status
            if (currentPaymentStatus === 'unpaid') {
                paymentStatus = 'paid';
                button.textContent = 'UNPAID';
                paymentStatusSpan.textContent = 'PAID';
                button.style.backgroundColor = '#dc3545';
            } else {
                paymentStatus = 'unpaid';
                paymentStatusSpan.textContent = 'UNPAID';
                button.textContent = 'PAID';
                button.style.backgroundColor = '#28a745';
            }
        }
    </script>
    <script src="ViewSession.js"></script>
</body>

</html>