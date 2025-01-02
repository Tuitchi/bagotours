<?php require_once 'include/db_conn.php';
require_once 'func/func.php';
session_start();

if (isset($_GET['tour_id'])) {
    $id = $_GET['tour_id'];
    if (validateQR($conn, $id)) {
        try {
            $query = "SELECT title, user_id as admin FROM tours WHERE id = :tour_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':tour_id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $title = $row['title'];
            $admin = $row['admin'];
        } catch (PDOException $e) {
            die('Error: ' . $e->getMessage());
        }
    } else {
        header('Location: index');
        exit();
    }
} else {
    header('Location: index');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo $webIcon ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/index.css" />
    <link rel="stylesheet" href="assets/css/login.css" />
    <link rel="stylesheet" href="user.css" />
    <title>Visit - <?php echo $title ?></title>
    <style>
        .modal-content {
            margin: auto;
            background-color: #ededed;
            position: relative;
        }

        .modal-content label {
            color: #333;
        }

        .modal-content p {
            color: #333;
        }
    </style>
</head>

<body>
    <div class="modal-content">
        <?php if (!isset($_COOKIE['device_id'])) { ?>
            <div id="sign-in-form" class="form-container hidden" width="80%">
                <form id="loginForm">
                    <h2>Sign In</h2>
                    <input id="username" name="username" type="text" placeholder="Email" autocomplete="username" />
                    <div id="username-error" class="error-message"></div>
                    <input id="password" name="password" type="password" placeholder="Password" />
                    <div id="password-error" class="error-message"></div>
                    <button type="submit" class="btn">Sign in</button>
                </form>
                <a href="#" id="forgot-password">Forgot Password?</a>
                <p>Need an Account? <a href="#" id="to-sign-up">Sign Up</a></p>

            </div>
            <div id="sign-up-form" class="form-container">
                <h2>Sign Up</h2>
                <form id="signupForm">
                    <input id="email" name="email" type="text" placeholder="Email" autocomplete="email" />
                    <div id="regEmail-error" class="error-message"></div>
                    <div class="name">
                        <select name="country" id="country" required>
                            <option value="" selected disabled>Select Country</option>
                            <!-- Auto Generated country throu JS -->
                        </select>
                        <select name="province" id="province" required disabled>
                            <option value="" selected disabled>Select Province</option>
                            <!-- Auto Generated country throu JS -->
                        </select>
                        <select name="city" id="city" required disabled>
                            <option value="" selected disabled>Select City/Municipality</option>
                            <!-- Auto Generated country throu JS -->
                        </select>
                    </div>
                    <input id="pwd" name="pwd" type="password" placeholder="Password" />
                    <div id="regPassword-error" class="error-message"></div>

                    <input id="con-pwd" name="con-pwd" id="conPass" type="password" placeholder="Confirm password" />
                    <div id="conPass-error" class="error-message"></div>

                    <button type="submit">Sign Up</button>
                </form>
                <p>Already have an Account? <a href="#" id="to-sign-in">Sign In</a></p>
            </div>
            <div id="forgot-password-form" class="form-container hidden">
                <h2>Find your account</h2>
                <p>Please enter your email or mobile number to search for your account.</p>
                <form id="forgotForm">
                    <input id="forgotEmail" name="email" type="text" placeholder="Email" autocomplete="email" />
                    <div id="forgotEmail-error" class="error-message"></div>
                    <button type="submit">Find Account</button>
                </form>
                <button id="cancel-button">Cancel</button>
            </div>
        <?php } else {
            try {
                $stmt = $conn->prepare('SELECT id, home_address FROM users WHERE device_id = ?');
                $stmt->execute([$_COOKIE['device_id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                header("Location:index.php");
                exit();
            }
            if (!empty($user['home_address'])) {

                if (hasVisitedToday($conn, $id, $user['id'])) { ?>
                    <h1>You've already been to <?php echo $title ?> today.</h1>
                <?php } else {
                    if (recordVisit($conn, $id, $user['id'])) {
                        try {
                            $stmt = $conn->prepare("SELECT CONCAT(firstname , ' ', lastname) as name FROM users WHERE id = ?");
                            $stmt->execute([$user['id']]);
                            $user = $stmt->fetchColumn();
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage();
                        }
                        createNotification($conn, $admin, $id, "$user visits $title", "dashboard", "visits");
                    } ?>
                    <h1>Thank you for visiting <?php echo $title ?>.</h1>
                <?php }
            } else { ?>
                <div class="form-container">
                    <h2>Home Address</h2>
                    <form id="address">
                        <input type="hidden" value="<?php echo $user['id'] ?>" name="id">
                        <div class="name">
                            <select name="country" id="country" required>
                                <option value="" selected disabled>Select Country</option>

                            </select>
                            <select name="province" id="province" required disabled>
                                <option value="" selected disabled>Select Province</option>

                            </select>
                            <select name="city" id="city" required disabled>
                                <option value="" selected disabled>Select City/Municipality</option>

                            </select>
                        </div>
                        <div id="address-error" class="error-message"></div>

                        <button type="submit">Update Address</button>
                    </form>
                </div>
            <?php }
        } ?>
    </div>
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="assets/js/country.js"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <script>
        $(document).ready(function () {
            const $modal = $('#modal');
            const $forgotPassForm = $('#forgot-password-form');
            const $signInForm = $('#sign-in-form');
            const $signUpForm = $('#sign-up-form');
            const $loginFirst = $('#login-first');
            const $openModalButtons = $('#open-modal');
            const $toSignUpButton = $('#to-sign-up');
            const $forgotPassButton = $('#forgot-password');
            const $toSignInButton = $('#to-sign-in');
            const $cancelButton = $('#cancel-button');
            const $closeModalButton = $('#close-modal');

            function clearFormInputs($form) {
                $form[0].reset();
            }

            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                const results = regex.exec(window.location.search);
                return results === null ? null : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }

            // Forgot Password
            $('#forgotForm').on('submit', function (event) {
                event.preventDefault();
                const $submitButton = $(this).find('button[type="submit"]');
                $submitButton.prop('disabled', true).text('Searching...');

                const formData = new FormData(this);

                $.ajax({
                    url: 'php/forgot-password.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        const data = JSON.parse(response);

                        $('#forgotEmail-error').text('').css('color', '').css('border', '');
                        $('#forgotEmail').css('border', '1px solid #ddd');

                        if (data.success) {
                            $('#forgotEmail-error').css('color', 'green').text(data.message);
                            $('#forgotEmail').css('border', '1px solid green');
                            setTimeout(function () {
                                $modal.removeClass('active');
                                clearFormInputs($('#loginForm'));
                                clearFormInputs($forgotPassForm);
                                clearFormInputs($signUpForm);
                            }, 3000);
                        } else if (data.errors.email) {
                            $('#forgotEmail-error').text(data.errors.email);
                            $('#forgotEmail').css('border', '1px solid red');
                        }
                    },
                    error: function () {
                        alert('An error occurred. Please try again.');
                    },
                    complete: function () {
                        $submitButton.prop('disabled', false).text('Sign in');
                    },
                });
            });

            // update 
            $('#address').on('submit', function (event) {
                event.preventDefault();
                const $submitButton = $(this).find('button[type="submit"]');
                $submitButton.prop('disabled', true).text('Updating...');

                const formData = new FormData(this);

                $.ajax({
                    url: 'php/update-address.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        const data = JSON.parse(response);

                        $('#address-error').text('').css('color', '').css('border', '');
                        $('#address').css('border', '1px solid #ddd');

                        if (data.success) {
                            $('#address-error').css('color', 'green').text(data.message);
                            $('#address').css('border', '1px solid green');
                            setTimeout(function () {
                                $modal.removeClass('active');
                            }, 3000);
                        } else if (data.errors.address) {
                            $('#address-error').text(data.errors.address);
                            $('#address').css('border', '1px solid red');
                        }
                    },
                    error: function () {
                        alert('An error occurred. Please try again.');
                    },
                    complete: function () {
                        $submitButton.prop('disabled', false).text('Sign in');
                    },
                });
            });

            // Login Form
            $('#loginForm').on('submit', function (event) {
                event.preventDefault();
                const $submitButton = $(this).find('button[type="submit"]');
                $submitButton.prop('disabled', true).text('Logging in...');

                const formData = new FormData(this);

                $.ajax({
                    url: 'php/login.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        const data = JSON.parse(response);
                        $('#username-error, #password-error').text('');
                        $('#username, #password').css('border', '1px solid #ddd');

                        if (data.success) {
                            setTimeout(() => {
                                window.location.href = window.location.href;
                            }, 1500);
                        } else {
                            if (data.errors.username) {
                                $('#username-error').text(data.errors.username);
                                $('#username').css('border', '1px solid red');
                            }
                            if (data.errors.password) {
                                $('#username').css('border', '1px solid red');
                                $('#password-error').text(data.errors.password);
                                $('#password').css('border', '1px solid red');
                            }
                        }
                    },
                    error: function () {
                        alert('An error occurred. Please try again.');
                    },
                    complete: function () {
                        $submitButton.prop('disabled', false).text('Sign in');
                    },
                });
            });

            // Signup Form
            $('#signupForm').on('submit', function (event) {
                event.preventDefault();
                const formData = new FormData(this);
                const $submitButton = $(this).find('button[type="submit"]');
                $submitButton.prop('disabled', true).text('Signing Up...');

                $.ajax({
                    url: 'php/register.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        const data = JSON.parse(response);
                        ['#regEmail-error', '#regPassword-error', '#conPass-error', '#country-error'].forEach(id => $(id).text(''));
                        ['#email', '#pwd', '#con-pwd', '#country'].forEach(id => $(id).css('border', '1px solid #ddd'));

                        if (data.success) {
                            setTimeout(() => {
                                window.location.href = window.location.href;
                            }, 1500);
                        } else {
                            if (data.errors.email) {
                                $('#regEmail-error').text(data.errors.email);
                                $('#email').css('border', '1px solid red');
                            }
                            if (data.errors.password) {
                                $('#regPassword-error').text(data.errors.password);
                                $('#pwd').css('border', '1px solid red');
                            }
                            if (data.errors.confirm_password) {
                                $('#conPass-error').text(data.errors.confirm_password);
                                $('#con-pwd').css('border', '1px solid red');
                            }
                            if (data.errors.country) {
                                $('#country-error').text(data.errors.country);
                                $('#country').css('border', '1px solid red');
                            }
                            $submitButton.prop('disabled', false).text('Sign up');
                        }
                    },
                    error: function () {
                        alert('An error occurred. Please try again.');
                    },
                    complete: function () {
                        submitButton.prop('disabled', false).text('Sign up');
                    },
                });
            });

            // Button and Modal Handlers
            $openModalButtons.on('click', function () {
                $modal.addClass('active');
                $signInForm.addClass('slide-in');
            });

            $cancelButton.on('click', function (e) {
                e.preventDefault();
                $forgotPassForm.addClass('hidden').removeClass('slide-in');
                $signInForm.removeClass('hidden').addClass('slide-in');
            });

            $forgotPassButton.on('click', function (e) {
                e.preventDefault();
                $signInForm.addClass('hidden').removeClass('slide-in');
                $forgotPassForm.removeClass('hidden').addClass('slide-in');
            });

            $toSignUpButton.on('click', function (e) {
                e.preventDefault();
                $signInForm.addClass('hidden').removeClass('slide-in');
                $signUpForm.removeClass('hidden').addClass('slide-in');
            });

            $toSignInButton.on('click', function (e) {
                e.preventDefault();
                $signUpForm.addClass('hidden').removeClass('slide-in');
                $signInForm.removeClass('hidden').addClass('slide-in');
            });

            $closeModalButton.on('click', function () {
                $modal.removeClass('active');
                clearFormInputs($('#loginForm'));
                clearFormInputs($('#forgotForm'));
                clearFormInputs($('#signupForm'));
            });

            $(window).on('keydown', function (e) {
                if (e.key === 'Escape') {
                    $modal.removeClass('active');
                }
            });

            // Check for query parameter
            <?php if (!isset($_SESSION['user_id'])) { ?>
                if (getUrlParameter('login') === 'true') {
                    $loginFirst.show();
                    $modal.addClass('active');
                }
            <?php } ?>
        });

    </script>
</body>

</html>