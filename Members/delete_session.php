<?php
session_start();
@include '../conn/config.php';

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sessionId'])) {
    $sessionId = $_POST['sessionId'];

    // Fetch member's typeMember
    $query_member_type = "SELECT typeMember FROM tblmember WHERE memberID = ?";
    $stmt_member_type = mysqli_prepare($conn, $query_member_type);
    mysqli_stmt_bind_param($stmt_member_type, "i", $_SESSION['memberID']);
    mysqli_stmt_execute($stmt_member_type);
    mysqli_stmt_bind_result($stmt_member_type, $typeMember);
    mysqli_stmt_fetch($stmt_member_type);
    mysqli_stmt_close($stmt_member_type);

    // Fetch payment status
    $query_status = "SELECT paymentStatus FROM tblsessions WHERE sessionID = ?";
    $stmt_status = mysqli_prepare($conn, $query_status);
    mysqli_stmt_bind_param($stmt_status, "i", $sessionId);
    mysqli_stmt_execute($stmt_status);
    mysqli_stmt_bind_result($stmt_status, $paymentStatus);
    mysqli_stmt_fetch($stmt_status);
    mysqli_stmt_close($stmt_status);

    // Check if the member is a daily member and the session is unpaid
    if ($typeMember === 'Daily Member' && $paymentStatus !== 'paid') {
        $query_delete = "DELETE FROM tblsessions WHERE sessionID = ?";
        $stmt_delete = mysqli_prepare($conn, $query_delete);
        mysqli_stmt_bind_param($stmt_delete, "i", $sessionId);
        if (mysqli_stmt_execute($stmt_delete)) {
            $response['canDelete'] = true;
        } else {
            $response['canDelete'] = false;
            $response['error'] = "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt_delete);
    } else if ($typeMember !== 'Daily Member') {
        $query_delete = "DELETE FROM tblsessions WHERE sessionID = ?";
        $stmt_delete = mysqli_prepare($conn, $query_delete);
        mysqli_stmt_bind_param($stmt_delete, "i", $sessionId);
        if (mysqli_stmt_execute($stmt_delete)) {
            $response['canDelete'] = true;
        } else {
            $response['canDelete'] = false;
            $response['error'] = "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt_delete);
    }
}

// Output response as JSON
echo json_encode($response);
