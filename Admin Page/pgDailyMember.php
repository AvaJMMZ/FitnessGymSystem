<?php
@include '../conn/config.php';

$query = "SELECT * FROM tblmember WHERE status = 'approved' AND typeMember = 'Daily Member'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="icon" type="image/png" href="../WebImages/logo.jpg">
    <link rel="stylesheet" href="../Styles/Dash-Members.css">
    <title>Admin Main Page</title>
</head>

<body>
    <nav>
        <div class="icon"><i class='fas fa-dumbbell'></i></div>
        <div class="logo">VFlex</div>
    </nav>

    <div class="main">
        <div class="header">
            <div class="pending-con">
                <div class="btn-Pending">
                    <button id="btnPending">PENDING</button>
                </div>
            </div>
            <div class="btn-con">
                <button id="btnDailyMembers" class="active-btn">Daily Members</button>
                <button id="btnMontlyMembers">Monthly Members</button>
                <button id="btnWithTrainorMembers">Personal Trainer Members</button>
            </div>

            <div class="admin" id="adminLink">
                <div><i class='fas fa-user-circle'></i></div>
                <h6>ADMIN</h6>
            </div>
        </div>

        <div class="main-content-p">
            <h1>DAILY MEMBERS</h1>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Member ID</th>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Date Registered</th>
                            <th>Membership Type</th>
                            <th>Actions</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $memberID = $row['memberID']; ?>
                                <tr>
                                    <td><?php echo $row['memberID']; ?></td>
                                    <td><?php echo $row['username']; ?></td>
                                    <td><?php echo $row['name']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo date("F j, Y g:ia", strtotime($row['dateAdded'])); ?></td>
                                    <td><?php echo $row['typeMember']; ?></td>
                                    <td style="width: 17rem;">
                                        <button onclick="openModal('<?php echo $row['gender'] . ',' . date("F j / Y", strtotime($row['birthdate'])) . ',' . $row['address'] . ',' . $row['mobile'] . ',' . $row['emergecyName'] . ',' . $row['emergecyContact']; ?>')">Personal Details</button>
                                        <button onclick="openModalMH('<?php echo $row['mhHP'] . ',' . $row['mhBPP'] . ',' . $row['mhCH'] . ',' . $row['mhABP'] . ',' . $row['mhJP'] . ',' . $row['mhNBP'] . ',' . $row['mhPJGB'] . ',' . $row['smoke'] . ',' . $row['medication'] . ',' . $row['other']  . ',' . $row['imageAgreement']; ?>')">Medical History</button>
                                    </td>
                                    <td>
                                        <a href="pgViewSessions.php?memberID=<?php echo $memberID; ?>"><button>View Sessions</button></a>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="12" style="text-align: center;">No Daily Members</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <footer>&copy; 2024 VFlex Fitness Gym System. All rights reserved.</footer>

        <div id="personalDetails" class="PD-modal">
            <div class="PD-content">
                <span class="close">&times;</span>
                <h2>Personal Details</h2>
                <div id="memberDetails">
                    <div class="detail-row">
                        <div class="detail-label">Gender:</div>
                        <div class="detail-value"><?php echo $row['gender']; ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Birthdate:</div>
                        <div class="detail-value"><?php echo $row['birthdate']; ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Address:</div>
                        <div class="detail-value"><?php echo $row['address']; ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Mobile:</div>
                        <div class="detail-value"><?php echo $row['mobile']; ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Emergency Contact Name:</div>
                        <div class="detail-value"><?php echo $row['emergecyName']; ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Emergency Contact:</div>
                        <div class="detail-value"><?php echo $row['emergecyContact']; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="medicalHistory" class="MH-modal">
            <div class="MH-content">
                <span class="closeMH">&times;</span>
                <h2>Medical History</h2>
                <div id="medicalDetails">
                    <div class="detail-row">
                        <div class="detail-label">Heart Problem:</div>
                        <div class="detail-value-MH"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Blood Pressure Problems:</div>
                        <div class="detail-value-MH"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Chest Pain History:</div>
                        <div class="detail-value-MH"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Asthma or Breathing Problem:</div>
                        <div class="detail-value-MH"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Joint Problems:</div>
                        <div class="detail-value-MH"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Neck or Back Problem:</div>
                        <div class="detail-value-MH"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Pregnant / Just Gave Birth:</div>
                        <div class="detail-value-MH"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Smoking:</div>
                        <div class="detail-value-MH"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Current Medication:</div>
                        <div class="detail-value-MH"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Other Medical Condition:</div>
                        <div class="detail-value-MH"></div>
                    </div>
                </div>
                <div class="con-sig">
                    <div class="detail-label-sig">Agreement Image:</div>
                    <img id="agreementImage" src="#" alt="Agreement Image">
                </div>
            </div>
        </div>

        <script>
            document.getElementById("btnDailyMembers").addEventListener("click", function() {
                window.location.href = "pgDailyMember.php";
            });

            document.getElementById("btnMontlyMembers").addEventListener("click", function() {
                window.location.href = "pgMonthlyMembers.php";
            });

            document.getElementById("btnWithTrainorMembers").addEventListener("click", function() {
                window.location.href = "pgWithTrainer.php";
            });

            document.getElementById("btnPending").addEventListener("click", function() {
                window.location.href = "pgPendingMember.php";
            });

            var modal = document.getElementById("personalDetails");

            var span = document.getElementsByClassName("close")[0];

            function openModal(memberData) {
                var modal = document.getElementById("personalDetails");
                modal.style.display = "block";

                var dataArr = memberData.split(',');

                var detailValues = document.querySelectorAll(".detail-value");
                for (var i = 0; i < detailValues.length; i++) {
                    detailValues[i].textContent = dataArr[i].trim();
                }
            }

            span.onclick = function() {
                modal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }

            var modalMH = document.getElementById("medicalHistory");
            var spanMH = document.getElementsByClassName("closeMH")[0];

            function openModalMH(medicalData) {
                modalMH.style.display = "block";

                var dataArr = medicalData.split(',');

                var detailValues = document.querySelectorAll(".detail-value-MH");
                for (var i = 0; i < detailValues.length; i++) {
                    detailValues[i].textContent = dataArr[i].trim();
                }

                // Set the image src and link href
                var agreementImage = document.getElementById('agreementImage');
                var imageName = dataArr[dataArr.length - 1].trim();
                agreementImage.src = '../Members/' + imageName;

                var viewAgreementLink = document.getElementById('viewAgreementLink');
                viewAgreementLink.href = '../Members/' + imageName;
            }

            spanMH.onclick = function() {
                modalMH.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == modalMH) {
                    modalMH.style.display = "none";
                }
            }

            document.getElementById("adminLink").addEventListener("click", function() {
                // Redirect to admin page
                window.location.href = "AdminPage.php";
            });
        </script>
    </div>
</body>

</html>