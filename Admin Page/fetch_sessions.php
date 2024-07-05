<?php
session_start();
@include '../conn/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['memberID'])) {
    $memberID = $_GET['memberID'];

    $query = "SELECT * FROM tblsessions WHERE memberID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $memberID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $events = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $color = 'gray';

        if ($row['paymentStatus'] == 'paid') {
            $color = 'green';
        } elseif ($row['paymentStatus'] == 'unpaid') {
            $color = 'red';
        }

        $events[] = array(
            'id' => $row['sessionID'],
            'title' => $row['body'],
            'start' => $row['sessionDate'],
            'backgroundColor' => $color,
            'sessionID' => $row['sessionID'],
            'paymentStatus' => $row['paymentStatus'],
        );
    }

    mysqli_free_result($result);
    mysqli_stmt_close($stmt);
    echo json_encode($events);
} else {
    echo json_encode(array());
}
