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
    <title>Visit - <?php echo $title ?></title>
    <style>
        .modal-content {
            margin: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
            position: relative;
            transform: scale(0.9);
            transition: transform 0.3s ease-in-out;
            padding: 20px;
        }

        .modal-content.show {
            transform: scale(1);
        }

        .modal-content .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.6);
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 18px;
            color: #fff;
        }

        .modal-content .close-btn:hover {
            background: rgba(255, 255, 255, 0.8);
        }

        .error-message {
            position: relative;
            height: 10px;
            color: red;
            text-align: right;
            font-size: 12px;
            top: -48px;
            margin-top: -10px;
        }

        .form-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 500px;
            align-items: center;
            color: #fff;
        }

        .form-container h2 {
            margin-bottom: 5px;
            text-align: center;
            color: white;
        }

        .form-container label {
            align-self: flex-start;
            margin-bottom: 3px;
            font-size: 14px;
            color: white;
        }

        .form-container input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.8);
            color: #333;
            width: 100%;
        }

        .form-container #forgot-password {
            margin-top: 10px;
            text-align: center;
            text-decoration: none;
            color: blue;
        }

        .form-container #forgot-password:hover {
            color: lightblue;
        }

        .form-container p {
            margin-top: 10px;
            font-size: 14px;
            text-align: center;
            color: white;
        }

        .form-container a {
            text-decoration: none;
            color: skyblue;
        }

        .form-container a:hover {
            color: whitesmoke;
            text-decoration: underline;
        }

        .form-container button {
            padding: 10px;
            background-color: #007BFF;
            margin-top: 10px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .form-container button:hover {
            background-color: #0056b3;
        }

        .hidden {
            display: none;
        }

        .form-container.slide-in {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(50%);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>

<body>
    <div class="modal-content">
        <?php if (!isset($_COOKIE['device_id'])) { ?>
            <div id="sign-in-form" class="form-container">
                <form id="loginForm" method="POST">
                    <h2>Sign In</h2>
                    <label for="username">Username or E-mail</label>
                    <input id="username" name="username" type="text" placeholder="Email" autocomplete="username" />
                    <div id="username-error" class="error-message"></div>
                    <label for="password">Password</label>
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
                    <label for="name">Name</label>
                    <div id="name" class="name">
                        <input id="fname" name="firstname" type="text" placeholder="First name" style="width: 49%;" autocomplete="first name" />
                        <input id="lname" name="lastname" type="text" placeholder="Last name" style="width: 49%;" />
                    </div>
                    <div id="regName-error" class="error-message"></div>

                    <label for="signup-username">Username</label>
                    <input id="signup-username" name="username" type="text" placeholder="Username" autocomplete="username" />
                    <div id="regUsername-error" class="error-message"></div>

                    <label for="email">Email Address</label>
                    <input id="email" name="email" type="text" placeholder="Email" autocomplete="email" />
                    <div id="regEmail-error" class="error-message"></div>
                    
                    <label for="home-address">Home Address</label>
                    <input id="home-address" name="home-address" type="text" placeholder="e.g., Barangay, City, Provice, Country" autocomplete="email" />
                    <div id="regHome-error" class="error-message"></div>

                    <label for="pwd">Password</label>
                    <input id="pwd" name="pwd" type="password" placeholder="Password" />
                    <div id="regPassword-error" class="error-message"></div>

                    <label for="con-pwd">Confirm Password</label>
                    <input id="con-pwd" name="con-pwd" id="conPass" type="password" placeholder="Confirm password" />
                    <div id="regconPass-error" class="error-message"></div>

                    <button type="submit">Sign Up</button>
                </form>
                <p>Already have an Account? <a href="#" id="to-sign-in">Sign In</a></p>
            </div>
            <?php } else {
                try {
                    $stmt = $conn ->prepare('SELECT id FROM users WHERE device_id = ?');
                    $stmt ->execute([$_COOKIE['device_id']]);
                    $user_id = $stmt->fetchColumn();
                } catch (PDOException $e) { 
                    header("Location:index.php");
                    exit();
                }
            if (hasVisitedToday($conn, $id,$user_id)) { ?>
                <h1>You've already been to <?php echo $title ?> today.</h1>
            <?php } else {
                if (recordVisit($conn, $id, $user_id)) {
                    try {
                        $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
                        $stmt->execute([$user_id]);
                        $user = $stmt->fetchColumn();
                    } catch (PDOException $e) {
                        echo "Error: ". $e->getMessage();
                    }
                    createNotification($conn, $user_id, $id, "$user visits $title", "dashboard", "visits");
                } ?>
                <h1>Thank you for visiting <?php echo $title ?>.</h1>
        <?php }
        } ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const signInForm = document.getElementById('sign-in-form');
            const signUpForm = document.getElementById('sign-up-form');
            const openModalButtons = document.querySelectorAll('#open-modal');
            const toSignUpButton = document.getElementById('to-sign-up');
            const toSignInButton = document.getElementById('to-sign-in');

            function clearFormInputs(form) {
                form.reset();
            }

            const loginForm = document.getElementById('loginForm');
            loginForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(loginForm);

                $.ajax({
                    url: 'php/login.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        const data = JSON.parse(response);

                        document.getElementById('username-error').textContent = '';
                        document.getElementById('username').style.border = '1px solid #ddd';

                        document.getElementById('password-error').textContent = '';
                        document.getElementById('password').style.border = '1px solid #ddd';


                        if (data.success) {
                            $('.modal-content').load(location.href + '.modal-content > *');
                            setTimeout(function() {
                                window.location.href = data.redirect;
                            }, 5000);
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
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            });
            const signupForm = document.getElementById('signupForm');
            signupForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(signupForm);
                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }
                $.ajax({
                    url: 'php/register.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {},
                    success: function(response) {
                        ;
                        let data = JSON.parse(response);
                        document.getElementById('regName-error').textContent = '';
                        document.getElementById('fname').style.border = '1px solid #ddd';
                        document.getElementById('lname').style.border = '1px solid #ddd';
                        document.getElementById('regUsername-error').textContent = '';
                        document.getElementById('signup-username').style.border = '1px solid #ddd';
                        document.getElementById('regHome-error').textContent = '';
                        document.getElementById('home-address').style.border = '1px solid #ddd';
                        document.getElementById('regEmail-error').textContent = '';
                        document.getElementById('email').style.border = '1px solid #ddd';
                        document.getElementById('regPassword-error').textContent = '';
                        document.getElementById('pwd').style.border = '1px solid #ddd';
                        document.getElementById('regconPass-error').textContent = '';
                        document.getElementById('con-pwd').style.border = '1px solid #ddd';

                        if (data.success) {
                            $('.modal-content').load(location.href + '.modal-content > *');
                            setTimeout(function() {
                                window.location.href = data.redirect;
                            }, 5000);
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
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
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

                document.getElementById('regUsername-error').textContent = '';
                document.getElementById('regEmail-error').textContent = '';
                document.getElementById('regPassword-error').textContent = '';
                document.getElementById('regconPass-error').textContent = '';
            });
        });
    </script>
</body>

</html>