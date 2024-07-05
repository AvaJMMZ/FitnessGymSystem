<?php
@include '../conn/config.php';

function usernameExists($conn, $username)
{
    $query = "SELECT * FROM tblmember WHERE username='$username'";
    $result = mysqli_query($conn, $query);
    return mysqli_num_rows($result) > 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['check_username'])) {
        $username = $_POST['username'];
        $exists = usernameExists($conn, $username);
        echo $exists ? 'exists' : 'not_exists';
        exit; // Exit to prevent further execution of the script
    }

    // Step 1 data
    $membershipType = $_POST['membershipType'];
    $name = $_POST['name'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $email = strtolower($_POST['email']);
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Step 2 data
    $heartProblem = $_POST['heartProblem'];
    $bloodPressure = $_POST['bloodPressure'];
    $chestPain = $_POST['chestPain'];
    $asthma = $_POST['asthma'];
    $jointProblems = $_POST['jointProblems'];
    $neckBackProblems = $_POST['neckBackProblems'];
    $pregnant = $_POST['pregnant'];
    $smoke = $_POST['smoke'];
    $medication = $_POST['medication'];
    $otherMedicalCondition = isset($_POST['otherMedicalCondition']) && $_POST['otherMedicalCondition'] === 'Yes' ? $_POST['additionalConditionInput'] : 'No';

    // Step 3 data
    $agreeCheckbox = isset($_POST['agreeCheckbox']) ? 1 : 0;
    $emerContactName = $_POST['emerContactName'];
    $emerContactNumber = $_POST['emerContactNumber'];

    $uploadDir = "../Members/Signatures/"; // Corrected directory where files will be uploaded
    $targetFile = $uploadDir . basename($_FILES["signatureImage"]["name"]);

    // Check if file is an actual file or fake file
    if (is_uploaded_file($_FILES["signatureImage"]["tmp_name"])) {
        // Attempt to move the uploaded file to the specified directory
        if (!move_uploaded_file($_FILES["signatureImage"]["tmp_name"], $targetFile)) {
            die("Sorry, there was an error uploading your file.");
        }
    } else {
        die("File is not an uploaded file.");
    }

    $query = "INSERT INTO tblmember (name, gender, birthdate, address, mobile, email, username, password, 
                mhHP, mhBPP, mhCH, mhABP, mhJP, mhNBP, mhPJGB, smoke, medication, other, imageAgreement, 
                emergecyName, emergecyContact, dateAdded, typeMember)
                VALUES ('$name', '$gender', '$birthdate', '$address', '$contact', '$email', '$username', 
                '$password', '$heartProblem', '$bloodPressure', '$chestPain', '$asthma', '$jointProblems', 
                '$neckBackProblems', '$pregnant', '$smoke', '$medication', '$otherMedicalCondition', '$targetFile', 
                '$emerContactName', '$emerContactNumber', NOW(), '$membershipType')";

    if (mysqli_query($conn, $query)) {
        mysqli_close($conn);
        echo "<script>alert('Registration Successful! Please wait for admin\'s approval before logging in.'); window.location.href = 'pgMemLogin.php';</script>";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
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
    <link rel="stylesheet" href="../Styles/style_register.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <title>Member Registration</title>
</head>

<body>
    <nav>
        <div class="icon"><i class='fas fa-dumbbell'></i></div>
        <div class="logo">VFlex</div>
    </nav>

    <div class="main">
        <div class="main-content">
            <div class="setup-con">
                <form id="multiStepForm" action="pgRegister.php" method="post" enctype="multipart/form-data">
                    <div class="step1" id="step1">
                        <h1>LET'S GET STARTED!!</h1>
                        <h2>Personal Information</h2>

                        <div class="textbox" style="width: 100%; text-align: center;">
                            <select id="membershipType" name="membershipType" required style="padding: 0.2rem;">
                                <option value="" disabled selected>Select Membership Type</option>
                                <option value="Daily Member">Daily Member</option>
                                <option value="Monthly Member">Monthly Member</option>
                                <option value="With Instructor Member">With Instructor Member</option>
                            </select>
                        </div>

                        <div style="display: flex; ">
                            <div class="textbox">
                                <input type="text" id="name" name="name" required>
                                <span>Full Name</span>
                            </div>

                            <div class="textbox" style="margin-left: 1rem;">
                                <input type="date" id="birthdate" name="birthdate" value="2000-01-01" required>
                                <span>Birthdate</span>
                            </div>
                        </div>

                        <div class="label">Gender:</div>
                        <div class="gender-con">
                            <div>
                                <input type="radio" id="male" name="gender" value="male" required>
                                <label for="male">Male</label>
                            </div>
                            <div>
                                <input type="radio" id="female" name="gender" value="female" required>
                                <label for="female">Female</label>
                            </div>
                            <div>
                                <input type="radio" id="other" name="gender" value="other" required>
                                <label for="other">Other</label>
                            </div>
                        </div>

                        <div class="textbox" style="width: 90%;">
                            <input type="text" id="address" name="address" required>
                            <span>Address</span>
                        </div>

                        <div style="display: flex; ">
                            <div class="textbox">
                                <input type="text" id="contact" name="contact" required>
                                <span>Contact</span>
                            </div>

                            <div class="textbox" style="margin-left: 1rem;">
                                <input type="email" id="email" name="email" required>
                                <span>Email</span>
                            </div>
                        </div>

                        <div style="display: flex;">
                            <div class="textbox">
                                <input type="text" id="username" name="username" required>
                                <span>Username</span>
                            </div>

                            <div class="textbox" style="margin-left: 1rem;">
                                <input type="password" id="password" name="password" required>
                                <span>Password</span>
                            </div>
                        </div>

                        <div style="margin-top: 0.5rem; text-align:none; padding-left: 7rem; font-size: 18px">
                            <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()">
                            <label for="showPassword">Show Password</label>
                        </div>

                        <div id="error-message-step1" class="error-message" style="display: none;">
                            Please fill in all required fields!
                        </div>

                        <div class="btn-con">
                            <button type="button" onclick="nextStep(1)">NEXT>></button>
                        </div>
                    </div>

                    <div class="step2" id="step2" style="display: none;">
                        <h2>MEDICAL HISTORY</h2>

                        <table class="radio-table">
                            <tr>
                                <th>Question</th>
                                <th>Yes</th>
                                <th>No</th>
                            </tr>
                            <tr>
                                <td>Heart Problem</td>
                                <td><input type="radio" id="heartProblemYes" name="heartProblem" value="Yes" required></td>
                                <td><input type="radio" id="heartProblemNo" name="heartProblem" value="No" required></td>
                            </tr>
                            <tr>
                                <td>Blood Pressure Problems</td>
                                <td><input type="radio" id="bloodPressureYes" name="bloodPressure" value="Yes" required></td>
                                <td><input type="radio" id="bloodPressureNo" name="bloodPressure" value="No" required></td>
                            </tr>
                            <tr>
                                <td>Chest Pain</td>
                                <td><input type="radio" id="chestPainYes" name="chestPain" value="Yes" required></td>
                                <td><input type="radio" id="chestPainNo" name="chestPain" value="No" required></td>
                            </tr>
                            <tr>
                                <td>Asthma or Breathing Problems</td>
                                <td><input type="radio" id="asthmaYes" name="asthma" value="Yes" required></td>
                                <td><input type="radio" id="asthmaNo" name="asthma" value="No" required></td>
                            </tr>
                            <tr>
                                <td>Joint Problems</td>
                                <td><input type="radio" id="jointProblemsYes" name="jointProblems" value="Yes" required></td>
                                <td><input type="radio" id="jointProblemsNo" name="jointProblems" value="No" required></td>
                            </tr>
                            <tr>
                                <td>Neck or Back Problems</td>
                                <td><input type="radio" id="neckBackProblemsYes" name="neckBackProblems" value="Yes" required></td>
                                <td><input type="radio" id="neckBackProblemsNo" name="neckBackProblems" value="No" required></td>
                            </tr>
                            <tr>
                                <td>Pregnant/Just Gave Birth</td>
                                <td><input type="radio" id="pregnantYes" name="pregnant" value="Yes" required></td>
                                <td><input type="radio" id="pregnantNo" name="pregnant" value="No" required></td>
                            </tr>
                            <tr>
                                <td>DO YOU SMOKE?</td>
                                <td><input type="radio" id="smokeYes" name="smoke" value="Yes" required></td>
                                <td><input type="radio" id="smokeNo" name="smoke" value="No" required></td>
                            </tr>
                            <tr>
                                <td>ARE YOU IN ANY MEDICATION?</td>
                                <td><input type="radio" id="medicationYes" name="medication" value="Yes" required></td>
                                <td><input type="radio" id="medicationNo" name="medication" value="No" required></td>
                            </tr>
                            <tr>
                                <td>Any Other Medical Condition</td>
                                <td><input type="radio" id="otherMedicalConditionYes" name="otherMedicalCondition" value="Yes" required onchange="toggleAdditionalCondition()"></td>
                                <td><input type="radio" id="otherMedicalConditionNo" name="otherMedicalCondition" value="No" required onchange="toggleAdditionalCondition()"></td>
                            </tr>
                        </table>

                        <div class="additional-condition" id="additionalConditionInput">
                            <label for="additionalConditionInput">Specify:</label>
                            <input type="text" id="additionalConditionInput" name="additionalConditionInput">
                        </div>

                        <div id="error-message-step2" class="error-message" style="display: none;">
                            Please fill in all required fields!
                        </div>

                        <div class="btn-con">
                            <button type="button" onclick="prevStep(2)">
                                << Previous</button>
                                    <button type="button" onclick="nextStep(2)">Next >></button>
                        </div>
                    </div>

                    <div class="step3" id="step3" style="display: none;">
                        <h2>Liability Waiver</h2>

                        <p>
                            I, the undersigned, being aware of my own health and physical condition and having the knowledge that any participation in my exercise program may be injurious to my health. I am voluntarily participating in physical activities.
                            Having knowledge, I hereby acknowledge this release, any representative agents, and successors from liability for accident injury or illness which may incur as a result of participating in the said physical activities. I hereby assume all risk connected there with and consent to participate in the said program
                            I agree to disclose any physical limitations, disabilities, ailments, or impairments, which may affect my ability to participate in said fitness program.
                        </p>

                        <div class="agree-con">
                            <input type="checkbox" id="agreeCheckbox" required>
                            <label for="agreeCheckbox">I agree to the terms and conditions</label>
                        </div>

                        <div class="additional-info">
                            <div class="prof-con">
                                <label for="signatureImage">Upload picture of signature over printed name:</label>
                                <input type="file" id="signatureImage" name="signatureImage" accept="image/*" required>
                            </div>

                            <div class="emer-contact">
                                <div>
                                    <label for="emerContactName">Name of emergency contact person:</label>
                                    <input type="text" id="emerContactName" name="emerContactName" required>
                                </div>
                                <div>
                                    <label for="emerContactNumber">Emergency contact number:</label>
                                    <input type="tel" id="emerContactNumber" name="emerContactNumber" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="btn-con" id="finishButton" style="display: none;">
                        <button type="button" onclick="prevStep(3)">Previous</button>
                        <button type="submit">Finish</button>
                    </div>
                </form>
            </div>

            <div class="img-con">
                <img src="../WebImages/ror.jpg" alt="background">

                <div class="login-link">
                    <p>Already have an account? <a href="pgMemLogin.php">Login</a></p>
                </div>
            </div>
        </div>

        <footer>&copy; 2024 VFlex Fitness Gym System. All rights reserved.</footer>

        <script>
            function showStep(step) {
                document.querySelectorAll('.step1, .step2, .step3').forEach(function(element) {
                    element.style.display = 'none';
                });

                document.querySelector(`#step${step}`).style.display = 'block';

                var finishButton = document.getElementById('finishButton');
                finishButton.style.display = step === 3 ? 'block' : 'none';
            }

            function nextStep(currentStep) {
                var isStepValid = areFieldsFilled(currentStep);

                if (isStepValid) {
                    if (currentStep === 1) {
                        if (!isPasswordValid()) {
                            alert("Password must be at least 8 characters long and include a special character, lowercase, and uppercase letter.");
                            return;
                        }

                        var username = document.getElementById('username').value;
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', 'pgRegister.php', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === XMLHttpRequest.DONE) {
                                if (xhr.status === 200) {
                                    var response = xhr.responseText;
                                    if (response === 'exists') {
                                        alert("Username already exists!");
                                    } else {
                                        showStep(currentStep + 1);
                                    }
                                } else {
                                    console.error('Error:', xhr.statusText);
                                }
                            }
                        };
                        // Send AJAX request for username availability check
                        xhr.send('check_username=true&username=' + username);
                    } else {
                        showStep(currentStep + 1);
                    }
                }
            }

            function areFieldsFilled(step) {
                var requiredFields = document.querySelectorAll(`#step${step} [required]`);
                var isFilled = true;

                for (var i = 0; i < requiredFields.length; i++) {
                    if (!requiredFields[i].checkValidity()) {
                        isFilled = false;
                        // Trigger the default HTML5 validation message
                        requiredFields[i].reportValidity();
                        break;
                    }
                }

                if (!isFilled) {
                    // Show the error message for the current step
                    document.getElementById(`error-message-step${step}`).style.display = 'block';

                    // Hide the error message after 3 seconds (adjust as needed)
                    setTimeout(function() {
                        document.getElementById(`error-message-step${step}`).style.display = 'none';
                    }, 3000);
                }

                return isFilled;
            }

            function isPasswordValid() {
                var passwordInput = document.getElementById('password');
                var password = passwordInput.value;

                // Add your password conditions here (8 characters, special char, lower and upper case)
                var isLengthValid = password.length >= 8;
                var hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
                var hasLowerCase = /[a-z]/.test(password);
                var hasUpperCase = /[A-Z]/.test(password);

                if (!isLengthValid || !hasSpecialChar || !hasLowerCase || !hasUpperCase) {
                    alert("Password must be at least 8 characters long and include a special character, lowercase, and uppercase letter.");
                    return false;
                }

                return true;
            }

            document.getElementById('finishButton').addEventListener('click', function() {
                if (areFieldsFilled(3)) {
                    document.getElementById('multiStepForm').submit();
                }
            });

            function prevStep(currentStep) {
                showStep(currentStep - 1);
            }

            showStep(1);

            function toggleAdditionalCondition() {
                var additionalConditionInput = document.getElementById('additionalConditionInput');
                var otherMedicalConditionYes = document.getElementById('otherMedicalConditionYes');
                var otherMedicalConditionNo = document.getElementById('otherMedicalConditionNo');

                if (otherMedicalConditionYes.checked) {
                    additionalConditionInput.style.display = 'block';
                } else if (otherMedicalConditionNo.checked) {
                    additionalConditionInput.style.display = 'none';
                }

            }

            function togglePasswordVisibility() {
                var passwordInput = document.getElementById('password');
                var showPasswordCheckbox = document.getElementById('showPassword');

                if (showPasswordCheckbox.checked) {
                    passwordInput.type = 'text';
                } else {
                    passwordInput.type = 'password';
                }
            }
        </script>
    </div>
</body>

</html>