<?php
session_start();
require "include/db_conn.php";

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Use a prepared statement with a parameterized query
    $query = "SELECT email FROM users WHERE verification_code = :code LIMIT 1;";
    $stmt = $conn->prepare($query);

    // Bind the parameter and execute the query
    $stmt->bindParam(':code', $code, PDO::PARAM_STR);
    $stmt->execute();

    // Fetch the email
    $email = $stmt->fetchColumn();
} else {
    header("Location: index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo $webIcon ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BagoTours</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/verify.css">
</head>

<body>
    <?php include 'nav/topnav.php' ?>

    <div class="main-container">
        <div class="modal-content">
            <h2>Sign Up</h2>
            <?php if ($email) {

                echo "
            <p>
                    Verification successful for email: " . htmlspecialchars($email); ?>

                </p>
                <div id="sign-up-form" class="form-container">
                    <div class="form-group profilePic">
                        <img src="upload/Profile Pictures/default.png" alt="">
                        <label for="profilePicture" id="pp-icon"><i class="fa fa-camera"></i></label>
                        <input type="file" id="profilePicture" name="profilePicture">
                    </div>
                    <form id="signupForm">
                        <input type="hidden" name="email" value="<?= $email ?>">
                        <div class="form-group details">
                            <div id="name" class="name">
                                <input id="fname" name="firstname" type="text" placeholder="First name"
                                    style="width: 49%;min-width:auto" autocomplete="first name" />
                                <input id="lname" name="lastname" type="text" placeholder="Last name"
                                    style="width: 49%;min-width:auto" />
                            </div>
                            <div id="name-error" class="error-message"></div>

                            <input id="signup-username" name="username" type="text" placeholder="Username"
                                autocomplete="username" />
                            <div id="username-error" class="error-message"></div>

                            <div class="country-group">
                                <div class="input-group" style="width: 60%;">
                                    <select name="gender" id="gender" required>
                                        <option value="" selected disabled>Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div id="gender-error" class="error-message"></div>

                                <div class="input-group">
                                    <select name="country" id="country" required>
                                        <option value="" selected disabled>Select Country</option>
                                        <!-- Auto Generated country throu JS -->
                                    </select>
                                </div>
                                <div id="country-error" class="error-message"></div>

                            </div>
                            <div class="country-group">
                                <div class="input-group province" style="display:none;">
                                    <select name="province" id="province" required disabled>
                                        <option value="" selected disabled>Select Province</option>
                                        <!-- Auto Generated country throu JS -->
                                    </select>
                                    <div id="province-error" class="error-message"></div>

                                </div>
                                <div class="input-group city" style="display:none;">
                                    <select name="city" id="city" required disabled>
                                        <option value="" selected disabled>Select City/Municipality</option>
                                        <!-- Auto Generated country throu JS -->
                                    </select>
                                </div>
                                <div id="city-error" class="error-message"></div>
                            </div>

                            <input id="pwd" name="pwd" type="password" placeholder="Password" />
                            <div id="password-error" class="error-message"></div>

                            <input id="con-pwd" name="con-pwd" id="conPass" type="password"
                                placeholder="Confirm password" />
                            <div id="conPass-error" class="error-message"></div>
                        </div>

                        <button type="submit">Sign Up</button>
                    </form>
                </div>
            <?php } else {
                echo "<p>Invalid verification code.</p>";
            } ?>
        </div>
    </div>
    <script src="index.js"></script>
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="assets/js/country.js"></script>
    <script>
        $(document).ready(function () {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });

            $('#signupForm').on('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);

                $.ajax({
                    url: 'php/register.php',
                    type: 'POST',
                    data: formData,
                    processData: false, // Required for FormData
                    contentType: false, // Required for FormData
                    success: function (response) {
                        const data = JSON.parse(response);

                        // Clear all previous error messages and reset borders
                        $('#name-error, #username-error, #gender-error, #password-error, #conPass-error, #country-error, #province-error, #city-error').text('');
                        $('#fname, #lname, #signup-username, #pwd, #con-pwd').css('border', '1px solid #ddd');

                        if (data.success) {
                            Toast.fire({
                                icon: 'success',
                                title: 'Registration Successful'
                            });
                        } else if (data.errors) {
                            // Show errors for specific fields
                            if (data.errors.firstname) {
                                $('#name-error').text(data.errors.firstname);
                                $('#fname').css('border', '1px solid red');
                            }
                            if (data.errors.lastname) {
                                $('#name-error').text(data.errors.lastname);
                                $('#lname').css('border', '1px solid red');
                            }
                            if (data.errors.username) {
                                $('#username-error').text(data.errors.username);
                                $('#signup-username').css('border', '1px solid red');
                            }
                            if (data.errors.gender) {
                                $('#gender-error').text(data.errors.gender);
                            }
                            if (data.errors.password) {
                                $('#password-error').text(data.errors.password);
                                $('#pwd').css('border', '1px solid red');
                            }
                            if (data.errors.confirm_password) {
                                $('#conPass-error').text(data.errors.confirm_password);
                                $('#con-pwd').css('border', '1px solid red');
                            }
                            if (data.errors.country) {
                                $('#country-error').text(data.errors.country);
                            }
                            if (data.errors.province) {
                                $('#province-error').text(data.errors.province);
                            }
                            if (data.errors.city) {
                                $('#city-error').text(data.errors.city);
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        // General AJAX error handler
                        console.error('AJAX Error:', error);
                        Toast.fire({
                            icon: 'error',
                            title: 'An error occurred. Please try again later.'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>