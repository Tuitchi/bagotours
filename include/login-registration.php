<div id="modal" class="modal">
    <div class="backdrop"></div>
    <div class="modal-content">
        <button type="button" class="closeBtn" id="close-modal">&times;</button>
        <div id="sign-in-form" class="form-container">
            <form id="loginForm">
                <h2>Sign In</h2>
                <p id="login-first" style="display:none; color:red">To begin, you must log in.</p>
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
                <div id="name" class="name">
                    <input id="fname" name="firstname" type="text" placeholder="First name"
                        style="width: 49%;min-width:auto" autocomplete="first name" />
                    <input id="lname" name="lastname" type="text" placeholder="Last name"
                        style="width: 49%;min-width:auto" />
                </div>
                <div id="regName-error" class="error-message"></div>

                <input id="signup-username" name="username" type="text" placeholder="Username"
                    autocomplete="username" />
                <div id="regUsername-error" class="error-message"></div>

                <input id="email" name="email" type="text" placeholder="Email" autocomplete="email" />
                <div id="regEmail-error" class="error-message"></div>

                <input id="home-address" name="home-address" type="text" placeholder="Home Address" />
                <div id="regHome-error" class="error-message"></div>


                <input id="pwd" name="pwd" type="password" placeholder="Password" />
                <div id="regPassword-error" class="error-message"></div>

                <input id="con-pwd" name="con-pwd" id="conPass" type="password" placeholder="Confirm password" />
                <div id="regconPass-error" class="error-message"></div>

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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                            window.location.href = data.redirect;
                        }, 1500);
                    } else {
                        if (data.errors.username) {
                            $('#username-error').text(data.errors.username);
                            $('#username').css('border', '1px solid red');
                        }
                        if (data.errors.password) {
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

            $.ajax({
                url: 'php/register.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    const data = JSON.parse(response);
                    // Clear all error fields
                    $('#regName-error, #regUsername-error, #regEmail-error, #regHome-error, #regPassword-error, #regconPass-error').text('');
                    $('#fname, #lname, #signup-username, #email, #home-address, #pwd, #con-pwd').css('border', '1px solid #ddd');

                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.errors.name) {
                            $('#regName-error').text(data.errors.name);
                            $('#fname, #lname').css('border', '1px solid red');
                        }
                        if (data.errors.uname) {
                            $('#regUsername-error').text(data.errors.uname);
                            $('#signup-username').css('border', '1px solid red');
                        }
                        if (data.errors.email) {
                            $('#regEmail-error').text(data.errors.email);
                            $('#email').css('border', '1px solid red');
                        }
                        if (data.errors.home) {
                            $('#regHome-error').text(data.errors.home);
                            $('#home-address').css('border', '1px solid red');
                        }
                        if (data.errors.pwd) {
                            $('#regPassword-error').text(data.errors.pwd);
                            $('#pwd').css('border', '1px solid red');
                        }
                        if (data.errors.confirm_password) {
                            $('#regconPass-error').text(data.errors.confirm_password);
                            $('#con-pwd').css('border', '1px solid red');
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