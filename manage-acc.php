<?php
session_start();
require_once 'func/user_func.php';
require_once 'func/func.php';
require_once 'include/db_conn.php';

$user_id = $_SESSION['user_id'] ?? 0;
$user = getUserById($conn, $user_id);
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo htmlspecialchars($webIcon, ENT_QUOTES); ?>">
    <title>BagoTours</title>
    <link rel="stylesheet" href="user.css">
    <style>
        .container {
            display: flex;
            justify-content: center;
            margin: 20px auto;
            width: 100%;
            height: auto;
            background: #fff;
            border: 1px solid #037d54;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .editUser {
            flex: 1;
            max-width: 250px;
            padding: 20px;
            border-right: 1px solid #ddd;
        }

        .editUser ul {
            padding: 0;
            list-style: none;
        }

        .editUser ul li {
            margin: 10px 0;
        }

        .editUser ul li a {
            color: #333;
            font-weight: bold;
            text-decoration: none;
            transition: color 0.3s ease;
            cursor: pointer;
            margin-bottom: 10px;
            padding: 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;

        }

        .editUser ul li a:hover {
            color: #04AA6D;
        }

        aside {
            flex: 3;
            padding: 20px;
        }

        aside>div {
            display: none;
        }

        .Account {
            display: block;
        }

        .profilepic {
            display: flex;
            flex-direction: column;
            align-items: center;

            position: relative;
        }

        .profilepic input[type="file"] {
            display: none;
            text-align: center;
        }

        .profilepic img {
            width: 100px;
            height: auto;
            border-radius: 50%;
            border: 2px solid #04AA6D;
        }

        form {
            margin: 20px 0;
        }

        .container input[type="text"],
        .container input[type="tel"],
        .container input[type="password"],
        .container input[type="email"],
        select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .container input[type="submit"],
        .button {
            text-decoration: none;
            background-color: #04AA6D;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .container input[type="submit"]:hover,
        .button:hover {
            background-color: #037d54;
        }

        .error {
            color: red;
            display: none;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 10px 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
        }
        .modal-content a{
            float: right;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            cursor: pointer;
        }

        @media (max-width: 568px) {
            .container {
                flex-direction: column;
                padding: 10px;
                width: 100%;
            }

            .editUser {
                flex-direction: row;
                /* Switch to row layout */
                justify-content: space-between;
                align-items: center;
                padding: 10px 0;
                max-width: 100%;
                /* Full width on mobile */
                border: none;
                /* Remove border for cleaner mobile design */
            }

            .editUser ul {
                display: flex;
                /* Flex layout for the list */
                flex-wrap: wrap;
                /* Wrap items if needed */
                /* Space between icons */
            }

            .editUser ul li {
                margin: 0;
                /* Remove margins */
            }

            .editUser ul li a {
                font-size: 0;
                /* Hide text */
            }

            .editUser ul li a i {
                font-size: 20px;
                /* Show only icons */
                color: #333;
                /* Icon color */
            }

            .editUser ul li a:hover i {
                color: #04AA6D;
                /* Change icon color on hover */
            }
        }
    </style>
</head>

<body>
    <?php include('nav/topnav.php'); ?>
    <div class="main-container">
        <?php include('nav/sidenav.php'); ?>
        <div class="main">
            <div class="container">
                <div class="editUser">
                    <h2>Profile</h2>
                    <ul>
                        <li><a href="#" data-section="Account"><i class="fas fa-user"></i> Account</a></li>
                        <li><a href="#" data-section="personalDetails"><i class="fas fa-id-card"></i> Personal
                                Details</a></li>
                        <li><a href="#" data-section="changepassword"><i class="fas fa-lock"></i> Change Password</a>
                        </li>
                        <li><a href="#" data-section="upgrade"><i class="fas fa-arrow-up"></i> Upgrade Account</a></li>
                    </ul>
                </div>
                <aside>
                    <div class="Account">
                        <?php if (!empty($user)) { ?>
                            <img src="upload/Profile Pictures/<?php echo htmlspecialchars($user['profile_picture'], ENT_QUOTES); ?>"
                                alt="Profile Preview" style="width:100px;">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?></p>
                            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?></p>
                            <p><strong>Email Address:</strong> <?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>
                            </p>
                            <p><strong>Home Address:</strong>
                                <?php echo htmlspecialchars($user['home_address'], ENT_QUOTES); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone_number'], ENT_QUOTES); ?>
                            </p>
                        <?php } else { ?>
                            <p>You are not logged in.</p>
                        <?php } ?>
                    </div>
                    <div class="changepassword">
                        <h3>Change Password</h3>
                        <form id="changePasswordForm">
                            <label for="oldPassword">Old Password:</label>
                            <input type="password" id="oldPassword" name="oldPassword" required>
                            <label for="newPassword">New Password:</label>
                            <input type="password" id="newPassword" name="newPassword" required
                                onkeyup="checkPasswordStrength()">
                            <div id="passwordStrength" style="color:red"></div>
                            <label for="confirmPassword">Confirm Password:</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" required>
                            <span class="error" id="passwordError">Passwords do not match!</span>
                            <input type="submit" value="Save">
                        </form>
                    </div>
                    <div class="personalDetails">
                        <h3>Personal Details</h3>
                        <form action="php/updateAcc.php" method="POST" enctype="multipart/form-data">
                            <div class="profilepic" id="profilePic">
                                <img id="profilePreview"
                                    src="upload/Profile Pictures/<?php echo htmlspecialchars($user['profile_picture'], ENT_QUOTES); ?>"
                                    alt="Profile Preview">
                                <label for="profilePicture" id="pp-icon"><i class="fa fa-camera"></i></label>
                                <input type="file" id="profilePicture" name="profilePicture">
                            </div>
                            <label for="fullName">Full Name</label>
                            <input type="text" id="fullName" name="fullName"
                                value="<?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?>">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" disabled
                                value="<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" disabled
                                value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone" maxlength="11" required
                                pattern="^(\+639|09)\d{9}$" placeholder="e.g. 09123456789"
                                value="<?php echo htmlspecialchars($user['phone_number'], ENT_QUOTES); ?>">
                            <label for="home-address">Home Address</label>
                            <input type="text" id="home-address" name="home-address"
                                value="<?php echo htmlspecialchars($user['home_address'], ENT_QUOTES); ?>">
                            <input type="submit" value="Update">
                        </form>
                    </div>

                    <div class="upgrade">
                        <h3>Upgrade Subscription</h3>
                        <button id="upgradeButton" class="button">Upgrade Account</button>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <div class="modal" id="upgradeModal" role="dialog" aria-hidden="true" aria-labelledby="upgradeTitle">
        <div class="modal-content">
            <span class="close" role="button" aria-label="Close">&times;</span>
            <h2 id="upgradeTitle">Upgrade Account</h2>
            <p>Are you sure you want to upgrade your account as an <strong>owner</strong>?</p>
            <a href="form" id="upgradeConfirm" class="button">Upgrade</a>
        </div>
    </div>
    </div>
    <?php require "include/login-registration.php"; ?>
    <script src="index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            const links = $('.editUser a');
            const sections = $('aside > div');

            links.on('click', function(event) {
                event.preventDefault();
                sections.hide();
                const target = $(this).data('section');
                $('.' + target).show();
            });
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
            <?php if (isset($_SESSION['status'])): ?>
                Toast.fire({
                    icon: "<?php echo $_SESSION['status'] === 'success' ? "success" : "error"; ?>",
                    title: "<?php echo $_SESSION['status'] === 'success' ? "Your account details have been successfully updated." : "Something went wrong. Please try again."; ?>"
                });
            <?php endif;
            unset($_SESSION['status']); ?>

            var $modal = $("#upgradeModal");
            var $btn = $("#upgradeButton");
            var $span = $(".close");

            $btn.on("click", function() {
                $modal.addClass('active');
            });

            // Close the modal when the "x" button is clicked
            $span.on("click", function() {
                $modal.removeClass('active');
            });

            // Close the modal when clicking outside of the modal content
            $(window).on("click", function(event) {
                if ($(event.target).is($modal)) {
                    $modal.removeClass('active');
                }
            });

            // Optional: Add keypress event to close modal with ESC key
            $(window).on("keydown", function(event) {
                if (event.key === "Escape") {
                    $modal.removeClass('active');
                }
            });



            var $passwordForm = $('#changePasswordForm');
            var $passwordError = $('#passwordError');
            var $passwordStrength = $('#passwordStrength');

            $passwordForm.on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                var oldPassword = $('#oldPassword').val();
                var newPassword = $('#newPassword').val();
                var confirmPassword = $('#confirmPassword').val();

                // Validate passwords match
                if (newPassword !== confirmPassword) {
                    $passwordError.text('Passwords do not match!').show();
                    return;
                } else {
                    $passwordError.hide();
                }

                // Perform AJAX request to update the password
                $.ajax({
                    url: 'php/change_password.php', // Path to your PHP script
                    type: 'POST',
                    data: {
                        oldPassword: oldPassword,
                        newPassword: newPassword
                    },
                    success: function(response) {
                        // Handle the response
                        var result = JSON.parse(response);
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Password Updated',
                                text: 'Your password has been successfully updated!',
                                timer: 3000,
                                showConfirmButton: false
                            });
                            $passwordForm[0].reset(); // Reset the form
                            $passwordStrength.text('');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: result.message,
                                showConfirmButton: true
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong. Please try again later.',
                            showConfirmButton: true
                        });
                    }
                });
            });

            $('#newPassword').on('input', function() {
                var newPassword = $(this).val();
                var strongPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#\$%\^&\*])(?=.{8,})/;

                if (newPassword.length < 8) {
                    $strength.css('color', 'red').text('Too short');
                } else if (strongPassword.test(newPassword)) {
                    $strength.css('color', 'green').text('Strong');
                } else {
                    $strength.css('color', 'orange').text('Weak');
                }
            });
        });
    </script>
</body>