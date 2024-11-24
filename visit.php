<?php require_once 'include/db_conn.php';
require_once 'func/func.php';
session_start();

if (isset($_GET['tour_id'])) {
    $id = $_GET['tour_id'];
    if (validateQR($conn, $id)) {
        try {
            $query = "SELECT title FROM tours WHERE id = :tour_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':tour_id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $title = $row['title'];
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
        <?php } else {
            try {
                $stmt = $conn->prepare('SELECT id FROM users WHERE device_id = ?');
                $stmt->execute([$_COOKIE['device_id']]);
                $user_id = $stmt->fetchColumn();
            } catch (PDOException $e) {
                header("Location:index.php");
                exit();
            }
            if (hasVisitedToday($conn, $id, $user_id)) { ?>
                <h1>You've already been to <?php echo $title ?> today.</h1>
            <?php } else {
                if (recordVisit($conn, $id, $user_id)) {
                    try {
                        $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
                        $stmt->execute([$user_id]);
                        $user = $stmt->fetchColumn();
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                    createNotification($conn, $user_id, $id, "$user visits $title", "dashboard", "visits");
                } ?>
                <h1>Thank you for visiting <?php echo $title ?>.</h1>
            <?php }
        } ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('modal');
            const forgotPassForm = document.getElementById('forgot-password-form');
            const signInForm = document.getElementById('sign-in-form');
            const signUpForm = document.getElementById('sign-up-form');
            const loginFirst = document.getElementById('login-first');
            const openModalButtons = document.querySelectorAll('#open-modal');
            const toSignUpButton = document.getElementById('to-sign-up');
            const forgotPassButton = document.getElementById('forgot-password');
            const toSignInButton = document.getElementById('to-sign-in');
            const cancelButton = document.getElementById('cancel-button');
            const closeModalButton = document.getElementById('close-modal');


            function clearFormInputs(form) {
                form.reset();
            }
            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                const results = regex.exec(window.location.search);
                return results === null ? null : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }
            // Forgot Password
            const forgotForm = document.getElementById('forgotForm');
            forgotForm.addEventListener('submit', function (event) {
                event.preventDefault();
                const submitButton = forgotForm.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.textContent = 'Searching...';

                const formData = new FormData(forgotForm);

                $.ajax({
                    url: 'php/forgot-password.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        const data = JSON.parse(response);

                        document.getElementById('forgotEmail-error').textContent = '';
                        document.getElementById('forgotEmail').style.border = '1px solid #ddd';


                        if (data.success) {
                            document.getElementById('forgotEmail-error').style.color = 'green';
                            document.getElementById('forgotEmail-error').textContent = data.message;
                            document.getElementById('forgotEmail').style.border = '1px solid green';
                            setTimeout(function () {
                                modal.classList.remove('active');
                                clearFormInputs(loginForm);
                                clearFormInputs(forgotPassForm);
                                clearFormInputs(signupForm);
                            }, 3000);
                        } else {
                            if (data.errors.email) {
                                document.getElementById('forgotEmail-error').textContent = data.errors.email;
                                document.getElementById('forgotEmail').style.border = '1px solid red';

                            }
                        }
                    },
                    error: function () {
                        alert('An error occurred. Please try again.');
                    },
                    complete: function () {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Sign in';
                    }
                });
            });

            // Login Form
            const loginForm = document.getElementById('loginForm');
            loginForm.addEventListener('submit', function (event) {
                event.preventDefault();
                const submitButton = loginForm.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.textContent = 'Logging in...';

                const formData = new FormData(loginForm);

                $.ajax({
                    url: 'php/login.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        const data = JSON.parse(response);
                        document.getElementById('username-error').textContent = '';
                        document.getElementById('username').style.border = '1px solid #ddd';

                        document.getElementById('password-error').textContent = '';
                        document.getElementById('password').style.border = '1px solid #ddd';


                        if (data.success) {
                            location.reload();
                        } else {
                            if (data.errors.username) {
                                document.getElementById('username-error').textContent = data.errors.username;
                                document.getElementById('username').style.border = '1px solid red';

                            }
                            if (data.errors.password) {
                                document.getElementById('password-error').textContent = data.errors.password;
                                document.getElementById('password').style.border = '1px solid red';

                            }
                        }
                    },
                    error: function () {
                        alert('An error occurred. Please try again.');
                    },
                    complete: function () {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Sign in';
                    }
                });
            });
            // Signup Form
            const signupForm = document.getElementById('signupForm');
            signupForm.addEventListener('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(signupForm);

                $.ajax({
                    url: 'php/register.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function () { },
                    success: function (response) {
                        let data = JSON.parse(response);
                        document.getElementById('regName-error').textContent = '';
                        document.getElementById('fname').style.border = '1px solid #ddd';
                        document.getElementById('lname').style.border = '1px solid #ddd';
                        document.getElementById('regUsername-error').textContent = '';
                        document.getElementById('signup-username').style.border = '1px solid #ddd';
                        document.getElementById('regEmail-error').textContent = '';
                        document.getElementById('regHome-error').textContent = '';
                        document.getElementById('email').style.border = '1px solid #ddd';
                        document.getElementById('home-address').style.border = '1px solid #ddd';
                        document.getElementById('regPassword-error').textContent = '';
                        document.getElementById('pwd').style.border = '1px solid #ddd';
                        document.getElementById('regconPass-error').textContent = '';
                        document.getElementById('con-pwd').style.border = '1px solid #ddd';

                        if (data.success) {
                            location.reload();
                        } else {
                            if (data.errors.name) {
                                document.getElementById('regName-error').textContent = data.errors.name;
                                document.getElementById('lname').style.border = '1px solid red';
                                document.getElementById('fname').style.border = '1px solid red';
                            }
                            if (data.errors.uname) {
                                document.getElementById('regUsername-error').textContent = data.errors.uname;
                                document.getElementById('signup-username').style.border = '1px solid red';
                            }
                            if (data.errors.email) {
                                document.getElementById('regEmail-error').textContent = data.errors.email;
                                document.getElementById('email').style.border = '1px solid red';
                            }
                            if (data.errors.home) {
                                document.getElementById('regHome-error').textContent = data.errors.home;
                                document.getElementById('home-address').style.border = '1px solid red';
                            }
                            if (data.errors.pwd) {
                                document.getElementById('regPassword-error').textContent = data.errors.pwd;
                                document.getElementById('pwd').style.border = '1px solid red';
                            }
                            if (data.errors.confirm_password) {
                                document.getElementById('regconPass-error').textContent = data.errors.confirm_password;
                                document.getElementById('con-pwd').style.border = '1px solid red';
                            }
                        }
                    },
                    error: function () {
                        alert('An error occurred. Please try again.');
                    }
                });
            });

            openModalButtons.forEach(button => {
                button.addEventListener('click', () => {
                    modal.classList.add('active');
                    signInForm.classList.add('slide-in');
                });
            });

            cancelButton.addEventListener('click', (event) => {
                event.preventDefault();
                forgotPassForm.classList.add('hidden');
                signInForm.classList.remove('hidden');
                signInForm.classList.add('slide-in');
                forgotPassForm.classList.remove('slide-in');

                document.getElementById('forgotEmail-error').textContent = '';
                document.getElementById('forgotEmail').style.border = '1px solid #ddd';
            });
            forgotPassButton.addEventListener('click', (event) => {
                event.preventDefault();
                signInForm.classList.add('hidden');
                forgotPassForm.classList.remove('hidden');
                forgotPassForm.classList.add('slide-in');
                signInForm.classList.remove('slide-in');

                document.getElementById('username-error').textContent = '';
                document.getElementById('username').style.border = '1px solid #ddd';
                document.getElementById('password-error').textContent = '';
                document.getElementById('password').style.border = '1px solid #ddd';
            });

            toSignUpButton.addEventListener('click', (event) => {
                event.preventDefault();
                signInForm.classList.add('hidden');
                signUpForm.classList.remove('hidden');
                signUpForm.classList.add('slide-in');
                signInForm.classList.remove('slide-in');

                document.getElementById('username-error').textContent = '';
                document.getElementById('username').style.border = '1px solid #ddd';
                document.getElementById('password-error').textContent = '';
                document.getElementById('password').style.border = '1px solid #ddd';
            });

            toSignInButton.addEventListener('click', (event) => {
                event.preventDefault();
                signUpForm.classList.add('hidden');
                signInForm.classList.remove('hidden');
                signInForm.classList.add('slide-in');
                signUpForm.classList.remove('slide-in');


                document.getElementById('regName-error').textContent = '';
                document.getElementById('fname').style.border = '1px solid #ddd';
                document.getElementById('lname').style.border = '1px solid #ddd';
                document.getElementById('regUsername-error').textContent = '';
                document.getElementById('signup-username').style.border = '1px solid #ddd';
                document.getElementById('regEmail-error').textContent = '';
                document.getElementById('regHome-error').textContent = '';
                document.getElementById('email').style.border = '1px solid #ddd';
                document.getElementById('home-address').style.border = '1px solid #ddd';
                document.getElementById('regPassword-error').textContent = '';
                document.getElementById('pwd').style.border = '1px solid #ddd';
                document.getElementById('regconPass-error').textContent = '';
                document.getElementById('con-pwd').style.border = '1px solid #ddd';
            });
            window.addEventListener("keydown", function (event) {
                if (event.key === "Escape") {
                    modal.classList.remove('active');
                    clearFormInputs(loginForm);
                    clearFormInputs(forgotForm);
                    clearFormInputs(signupForm);

                    document.getElementById('regName-error').textContent = '';
                    document.getElementById('fname').style.border = '1px solid #ddd';
                    document.getElementById('lname').style.border = '1px solid #ddd';
                    document.getElementById('regUsername-error').textContent = '';
                    document.getElementById('signup-username').style.border = '1px solid #ddd';
                    document.getElementById('regEmail-error').textContent = '';
                    document.getElementById('regHome-error').textContent = '';
                    document.getElementById('email').style.border = '1px solid #ddd';
                    document.getElementById('home-address').style.border = '1px solid #ddd';
                    document.getElementById('regPassword-error').textContent = '';
                    document.getElementById('pwd').style.border = '1px solid #ddd';
                    document.getElementById('regconPass-error').textContent = '';
                    document.getElementById('con-pwd').style.border = '1px solid #ddd';
                }
            });
            closeModalButton.addEventListener('click', () => {
                modal.classList.remove('active');
                clearFormInputs(loginForm);
                clearFormInputs(forgotForm);
                clearFormInputs(signupForm);

                document.getElementById('regName-error').textContent = '';
                document.getElementById('fname').style.border = '1px solid #ddd';
                document.getElementById('lname').style.border = '1px solid #ddd';
                document.getElementById('regUsername-error').textContent = '';
                document.getElementById('signup-username').style.border = '1px solid #ddd';
                document.getElementById('regEmail-error').textContent = '';
                document.getElementById('regHome-error').textContent = '';
                document.getElementById('email').style.border = '1px solid #ddd';
                document.getElementById('home-address').style.border = '1px solid #ddd';
                document.getElementById('regPassword-error').textContent = '';
                document.getElementById('pwd').style.border = '1px solid #ddd';
                document.getElementById('regconPass-error').textContent = '';
                document.getElementById('con-pwd').style.border = '1px solid #ddd';
            });
        });
    </script>
</body>

</html>