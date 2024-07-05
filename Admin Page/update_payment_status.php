<?php
session_start();
@include '../conn/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get session ID and new payment status from POST data
    $sessionID = $_POST['sessionID'];
    $newPaymentStatus = strtolower($_POST['newPaymentStatus']);

    // Prepare and execute SQL statement to update payment status
    $query = "UPDATE tblsessions SET paymentStatus = ? WHERE sessionID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $newPaymentStatus, $sessionID);
    $success = mysqli_stmt_execute($stmt);

    // Check if update was successful
    if ($success) {
        echo "Payment status updated successfully!";

        // Add data to tblsales if payment status is updated to 'paid'
        if ($newPaymentStatus == 'paid') {
            // Get memberID from tblsessions
            $memberIDQuery = "SELECT memberID FROM tblsessions WHERE sessionID = ?";
            $memberIDStmt = mysqli_prepare($conn, $memberIDQuery);
            mysqli_stmt_bind_param($memberIDStmt, "i", $sessionID);
            mysqli_stmt_execute($memberIDStmt);
            mysqli_stmt_bind_result($memberIDStmt, $memberID);
            mysqli_stmt_fetch($memberIDStmt);
            mysqli_stmt_close($memberIDStmt);

            // Insert data into tblsales
            $salesAmount = 80;
            $currentDate = date('Y-m-d H:i:s');

            // Prepare and execute SQL statement to insert data into tblsales
            $salesQuery = "INSERT INTO tblsales (memberID, date, amount) VALUES (?, ?, ?)";
            $salesStmt = mysqli_prepare($conn, $salesQuery);
            mysqli_stmt_bind_param($salesStmt, "iss", $memberID, $currentDate, $salesAmount);
            $salesSuccess = mysqli_stmt_execute($salesStmt);

            // Check if insertion was successful
            if ($salesSuccess) {
                echo "Data added to tblsales successfully!";
            } else {
                echo "Failed to add data to tblsales!";
            }

            // Close sales statement
            mysqli_stmt_close($salesStmt);
        }
    } else {
        echo "Failed to update payment status!";
    }

    // Close statement and connection
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
