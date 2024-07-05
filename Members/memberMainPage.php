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

$endDate = strtotime($memberData['endDate']);
$now = strtotime(date('Y-m-d H:i:s'));
$expired = false;

if ($memberData['status'] !== 'pending renew' && $memberData['endDate'] !== '') {
    if ($memberData['typeMember'] !== 'Daily Member') {
        $expired = ($endDate <= $now);

        if ($expired) {
            $updateQuery = "UPDATE tblmember SET status = 'expired' WHERE memberID = ?";
            $updateStmt = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($updateStmt, "i", $memberID);
            if (!mysqli_stmt_execute($updateStmt)) {
                echo "Error updating member status: " . mysqli_error($conn);
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['session-body-part']) && isset($_POST['session-date'])) {
    $sessionBodyPart = $_POST['session-body-part'];
    $sessionDate = strtotime(date('Y-m-d', strtotime($_POST['session-date']))); //in range
    $statusPayment = 'unpaid';

    // Fetch session row from database
    $sessionRowQuery = "SELECT * FROM tblsessions WHERE memberID = ?";
    $stmt = mysqli_prepare($conn, $sessionRowQuery);
    mysqli_stmt_bind_param($stmt, "i", $memberID);
    mysqli_stmt_execute($stmt);
    $sessionResult = mysqli_stmt_get_result($stmt);
    $sessionRow = mysqli_fetch_assoc($sessionResult);
    mysqli_free_result($sessionResult);
    mysqli_stmt_close($stmt);

    $dateAdded = strtotime(date('Y-m-d', strtotime($memberData['dateAdded'])));
    $endDate = strtotime(date('Y-m-d', strtotime($memberData['endDate'])));

    // Check if session date is within the date range (including time component)
    if ($sessionDate >= $dateAdded && $sessionDate <= $endDate) {
        if ($memberData['typeMember'] === 'Monthly Member' || $memberData['typeMember'] === 'With Instructor Member') {
            $statusPayment = 'paid';
        }

        $query = "INSERT INTO tblsessions (memberID, sessionDate, body, paymentStatus) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            echo "Error: " . mysqli_error($conn);
        }
        $sessionDateForInsert = date('Y-m-d', $sessionDate);
        mysqli_stmt_bind_param($stmt, "isss", $memberID, $sessionDateForInsert, $sessionBodyPart, $statusPayment);
        if (!mysqli_stmt_execute($stmt)) {
            echo "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }

    if ($memberData['typeMember'] === 'Daily Member') {
        $query = "INSERT INTO tblsessions (memberID, sessionDate, body, paymentStatus) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            echo "Error: " . mysqli_error($conn);
        }
        $sessionDateForInsert = date('Y-m-d', $sessionDate);
        mysqli_stmt_bind_param($stmt, "isss", $memberID, $sessionDateForInsert, $sessionBodyPart, $statusPayment);
        if (!mysqli_stmt_execute($stmt)) {
            echo "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['program'])) {
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

$accountExpired = false;
if ($expired) {
    $accountExpired = true;
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
    <title>Member Main Page</title>
</head>

<body>
    <nav>
        <div class="icon"><i class='fas fa-dumbbell'></i></div>
        <div class="logo">VFlex</div>
    </nav>

    <div class="main">
        <div class="header">
            <div class="subs">
                <?php
                if ($memberData) {
                    $startDate = date('F j, Y g:ia', strtotime($memberData['dateAdded']));
                    $endDate = date('F j, Y g:ia', strtotime($memberData['endDate']));
                    $typeMem = $memberData['typeMember'];

                    if ($memberData['typeMember'] == 'Daily Member') {
                        echo "<p>DAILY SUBSCRIPTION</p>";
                    } else {
                        echo "<p>Program: $typeMem</p>";
                        echo "<p>Subscription Range: $startDate to $endDate</p>";

                        // Calculate remaining subscription time
                        $remainingTime = strtotime($memberData['endDate']) - time();

                        // Format remaining time in days, hours, and minutes
                        $days = floor($remainingTime / (60 * 60 * 24));
                        $hours = floor(($remainingTime % (60 * 60 * 24)) / (60 * 60));
                        $minutes = floor(($remainingTime % (60 * 60)) / 60);

                        echo "<p>Remaining Subscription Time: ";
                        if ($days > 0) {
                            echo "$days days ";
                        }
                        if ($hours > 0) {
                            echo "$hours hours ";
                        }
                        if ($minutes > 0) {
                            echo "$minutes minutes ";
                        }
                        echo "left</p>";

                        if ($now > $endDate) {
                            $expired = true;
                        }
                    }
                }
                ?>
            </div>

            <div class="btn-profile">
                <a href="memProfile.php">
                    <i class="fas fa-user"></i>
                    <span>Your Profile</span>
                </a>
            </div>
        </div>

        <div class="main-content">
            <?php if (!empty($memberData)) : ?>
                <h1>Hello <?php echo $memberData['username']; ?>!</h1>
                <p>Welcome back to the gym!</p>
                <p>Today is another opportunity to push your limits, break barriers, and become stronger than yesterday.</p>
                <p>Remember, every drop of sweat brings you closer to your goals. Let's crush those workouts and make today count!</p>

                <div id="calendar"></div>

                <div class="logout-con">
                    <a href="pgMemLogin.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>LOGOUT</span>
                    </a>
                </div>
        </div>

        <?php if ($accountExpired) : ?>
            <div class="overlay" id="overlay"></div>
            <div class="renewal-con">
                <h2>YOUR ACCOUNT IS EXPIRED PLEASE RENEW</h2>
                <form id="renewalForm" method="post" action="memberMainPage.php">
                    <label for="program">Choose Program:</label>
                    <select id="program" name="program">
                        <option value="" disabled selected>Select Membership Type</option>
                        <option value="Daily Member">Daily Member</option>
                        <option value="Monthly Member">Monthly Member</option>
                        <option value="With Instructor Member">With Instructor Member</option>
                    </select>
                    <button type="submit" name="renewalBtn">Request Renewal</button>
                    <button type="button" id="logoutBtn" onclick="location.href='pgMemLogin.php';">Logout</button>
                </form>
            </div>

            <script>
                var overlay = document.getElementById("overlay");
                var renewalForm = document.querySelector(".renewal-con");

                function toggleOverlay() {
                    overlay.style.display = overlay.style.display === "none" ? "block" : "none";
                }

                overlay.style.display = "block";

                renewalForm.addEventListener("click", function(event) {
                    event.stopPropagation();
                });

                document.addEventListener("click", function() {
                    toggleOverlay();
                });

                overlay.addEventListener("click", function(event) {
                    event.stopPropagation();
                });
            </script>
        <?php endif; ?>
    <?php endif; ?>
    </div>

    <footer>&copy; 2024 VFlex Fitness Gym System. All rights reserved.</footer>

    <div id="session-form">
        <div class="con-session">
            <h1>Add Session: <span id="session-date"></span></h1>
            <form id="add-session-form" method="post" action="memberMainPage.php">
                <input type="hidden" id="session-date-input" name="session-date">
                <select id="session-body-part" name="session-body-part" required>
                    <option value="" selected disabled>Select Body Part</option>
                    <option value="Chest">Chest</option>
                    <option value="Back">Back</option>
                    <option value="Legs">Legs</option>
                    <option value="Biceps">Biceps</option>
                    <option value="Triceps">Triceps</option>
                    <option value="Shoulders">Shoulders</option>
                    <option value="Abs">Abs</option>
                    <option value="Glutes">Glutes</option>
                    <option value="Hamstrings">Hamstrings</option>
                    <option value="Quadriceps">Quadriceps</option>
                    <option value="Calves">Calves</option>
                    <option value="Forearms">Forearms</option>
                    <option value="Trapezius">Trapezius</option>
                </select>
                <div class="btn-con">
                    <button type="submit">Add Session</button>
                    <button type="button" onclick="cancelSession()">Cancel</button>
            </form>
        </div>
    </div>
    </div>

    <script src="../fullcalendar-6.1.11/fullcalendar-6.1.11/dist/index.global.min.js"></script>
    <script>
        var memberID = <?php echo json_encode($memberID); ?>;
        var sessionId = <?php echo isset($_POST['sessionId']) ? json_encode($_POST['sessionId']) : 'null'; ?>;
        var typeMember = "<?php echo isset($memberData['typeMember']) ? $memberData['typeMember'] : ''; ?>";
        var isSessionPaid = <?php echo isset($memberData['paymentStatus']) && $memberData['paymentStatus'] === 'paid' ? 'true' : 'false'; ?>;
    </script>
    <script src="memberMainPage.js"></script>
</body>

</html>