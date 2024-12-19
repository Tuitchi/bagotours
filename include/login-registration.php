<?php
require_once 'vendor/autoload.php';

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$redirectUri = $protocol . $host . $basePath . '/home';

$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");
$loginUrl = $client->createAuthUrl();
if (isset($_GET['code'])) {

    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $email = $google_account_info->email;
    $existingUser = getUserByEmail($conn, $email);
    if ($existingUser) {
        $_SESSION['user_id'] = $existingUser['id'];
        $_SESSION['role'] = $existingUser['role'];
        $_SESSION['profile-pic'] = $existingUser['profile_picture'];
        $device_id = $existingUser['device_id'];
        if (empty($existingUser['device_id']) || is_null($existingUser['device_id'])) {
            $device_id = md5($email . $existingUser['username']);
            $stmt = $conn->prepare("UPDATE users SET device_id = ? WHERE email=?");
            $stmt->execute([$device_id, $email]);
            echo "<script>alert('Goodstuff');</script>";
        }
        if ($_SESSION['role'] == 'user') {
            echo "<script>window.location.replace(window.location.pathname);</script>";
        } elseif ($_SESSION['role'] == 'owner') {
            echo "<script>window.location.replace('owner/home');</script>";
        } elseif ($_SESSION['role'] == 'admin') {
            echo "<script>window.location.replace('admin/home');</script>";
        }
        exit;
    } else {
        $firstname = $google_account_info->given_name;
        $lastname = $google_account_info->family_name;
        $profile_picture = $google_account_info->picture;
        $newUserId = createUser($conn, $email, $firstname, $lastname, $profile_picture);
        $_SESSION['user_id'] = $newUserId;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = "user";
        $_SESSION['profile-pic'] = $profile_picture;
        if (empty($existingUser['device_id'])) {
            $device_id = md5($email . $existingUser['username']);
            $stmt = $conn->prepare("UPDATE users SET device_id = ? WHERE email=?");
            $stmt->execute([$device_id, $email]);
        }
        setcookie('device_id', $device_id, time() + (10 * 365 * 24 * 60 * 60), "/");
        echo "<script>window.location.replace(window.location.pathname);</script>";
        exit;
    }
}
?>

<div id="modal" class="modal">
    <div class="backdrop"></div>
    <div class="modal-content">
        <button type="button" class="closeBtn" id="close-modal">&times;</button>
        <div id="sign-in-form" class="form-container" width="80%">
            <form id="loginForm">
                <h2>Sign In</h2>
                <p id="login-first" style="display:none; color:red">To begin, you must log in.</p>
                <center>
                    <a href="<?php echo htmlspecialchars($client->createAuthUrl()); ?>">
                        <img src="assets/sign-in.png" width="100%">
                    </a>
                </center>


                <div class="section-header">
                    <hr class="section-divider">
                    <h3 class="section-title">or</h3>
                    <hr class="section-divider">
                </div>
                <input id="username" name="username" type="text" placeholder="Email" autocomplete="username" />
                <div id="username-error" class="error-message"></div>
                <input id="password" name="password" type="password" placeholder="Password" />
                <div id="password-error" class="error-message"></div>
                <button type="submit" class="btn">Sign in</button>
            </form>
            <a href="#" id="forgot-password">Forgot Password?</a>
            <p>Need an Account? <a href="#" id="to-sign-up">Sign Up</a></p>

        </div>
        <div id="sign-up-form" class="form-container hidden">
            <h2>Sign Up</h2>
            <form id="signupForm">
                <center>
                    <a href="<?php echo $client->createAuthUrl() ?>"><img src="assets/sign-up.png" width="100%">
                    </a>
                </center>

                <div class="section-header">
                    <hr class="section-divider">
                    <h3 class="section-title">or</h3>
                    <hr class="section-divider">
                </div>
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
    </div>
</div>
<script src="https://unpkg.com/scrollreveal"></script>
<script src="assets/js/country.js"></script>
<script> function handleCredentialResponse(response) { // Process the response 
        console.log("Encoded JWT ID token: " + response.credential); // Send token to server for verification 
    } 
</script>
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

        // Login Form
        $('#loginForm').on('submit', function (event) {
            event.preventDefault();
            const submitButton = $(this).find('button[type="submit"]');
            const originalText = submitButton.html();
            submitButton.html('<span class="spinner"></span> Logging in...').prop('disabled', true);
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
                            window.location.href = data.redirect;
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
                        submitButton.prop('disabled', false).text('Sign in');
                    }
                },
                error: function () {
                    alert('An error occurred. Please try again.');
                    $submitButton.prop('disabled', false).text('Sign in');
                },
            });
        });

        // Signup Form
        $('#signupForm').on('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(this);
            const submitButton = $(this).find('button[type="submit"]'); // Target the submit button
            const originalText = submitButton.html(); // Store the original button text

            // Show loading spinner or text and disable the button
            submitButton.html('<span class="spinner"></span> Signing up...').prop('disabled', true);

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
                            window.location.href = data.redirect;
                        }, 1500);
                    } else {
                        submitButton.html(originalText).prop('disabled', false);
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
                    }
                },
                error: function () {
                    alert('An error occurred. Please try again.');
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