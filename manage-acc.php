<?php
session_start();
require_once 'func/user_func.php';
require_once 'func/func.php';
require_once 'include/db_conn.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: home?login=true');
    exit;
}
$user_id = $_SESSION['user_id'];
$user = getUserById($conn, $user_id);
$addressParts = array_map('trim', explode(',', $user['home_address']));

// Handle different cases based on the number of parts in $addressParts
if (count($addressParts) === 1) {
    // Only one part, assume it's the country
    $user['city'] = null;
    $user['province'] = null;
    $user['country'] = $addressParts[0];
} elseif (count($addressParts) === 3) {
    // All three parts present: city, province, and country
    $user['city'] = $addressParts[0];
    $user['province'] = $addressParts[1];
    $user['country'] = $addressParts[2];
} else {
    // Unexpected format, default to null
    $user['city'] = null;
    $user['province'] = null;
    $user['country'] = null;
}

// Example: Default country if critical
// $user['country'] = $user['country'] ?? 'Philippines';


?>
<!DOCTYPE html>
<html lang="en">

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

    .modal-content a {
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

    .name {
        width: calc(100% - 22px);
        display: flex;
        gap: 0.5em;
        flex-direction: row;
    }

    /* Message Display Container */
    .message-display {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 15px;
        margin: 10px auto;
        max-width: 500px;
        border-radius: 8px;
        background-color: #f8d7da;
        /* Light red background */
        color: #721c24;
        /* Dark red text */
        border: 1px solid #f5c6cb;
        /* Border matching the background */
        font-size: 14px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        /* Slight shadow for elevation */
        position: relative;
        animation: fadeIn 0.5s ease-in-out;
    }

    /* Close Button */
    .message-display .close {
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
        color: #721c24;
        border: none;
        background: none;
        margin-left: 10px;
    }

    /* Close Button Hover Effect */
    .message-display .close:hover {
        color: #d9534f;
        /* Slightly darker red on hover */
        transition: color 0.3s ease-in-out;
    }

    /* Message Content */
    .message-display .message-content {
        flex: 1;
        margin: 0;
    }

    /* Fade In Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
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
                        <li><a href="#" data-section="changepassword"><i class="fas fa-lock"></i> Change
                                Password</a>
                        </li>
                        <li><a href="#" data-section="upgrade"><i class="fas fa-arrow-up"></i> Upgrade Account</a>
                        </li>
                    </ul>
                </div>
                <aside>

                    <div class="changepassword">
                        <?php if (checkIfPasswordIsNull($conn, $user_id)) { ?>
                            <h3>Add Password</h3>
                            <form id="addPasswordForm">
                                <div class="message-display" style="display:none">
                                    <p class="message-content"></p>
                                </div>

                                <label for="password">Password:</label>
                                <input type="password" id="password" name="password" onkeyup="checkPasswordStrength()">
                                <div id="passwordStrength" style="color:red"></div>
                                <label for="confirm_password">Confirm Password:</label>
                                <input type="password" id="confirm_password" name="confirm_password">
                                <input type="submit" value="Save">
                            </form>
                        <?php } else { ?>
                            <h3>Change Password</h3>
                            <form id="changePasswordForm">

                                <div class="message-display" style="display:none">
                                    <p class="message-content"></p>
                                </div>

                                <label for="oldPassword">Old Password:</label>
                                <input type="password" id="oldPassword" name="oldPassword">
                                <label for="newPassword">New Password:</label>
                                <input type="password" id="newPassword" name="newPassword"
                                    onkeyup="checkPasswordStrength()">
                                <div id="passwordStrength" style="color:red"></div>
                                <label for="confirmPassword">Confirm Password:</label>
                                <input type="password" id="confirmPassword" name="confirmPassword">
                                <span class="error" id="passwordError">Passwords do not match!</span>
                                <input type="submit" value="Save">
                            </form>
                        <?php } ?>
                    </div>
                    <div class="Account">
                        <h3>Personal Details</h3>
                        <form action="php/updateAcc.php" method="POST" enctype="multipart/form-data">
                            <div class="profilepic" id="profilePic">
                                <img id="profilePreview"
                                    src="<?php echo htmlspecialchars($user['profile_picture'], ENT_QUOTES); ?>"
                                    alt="Profile Preview">
                                <label for="profilePicture" id="pp-icon"><i class="fa fa-camera"></i></label>
                                <input type="file" id="profilePicture" name="profilePicture">
                            </div>
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" disabled
                                value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>">
                            <label for="fullName">Full Name</label>
                            <div class="name">
                                <input type="text" id="fullName" name="firstname"
                                    value="<?php echo htmlspecialchars($user['firstname'], ENT_QUOTES); ?>"
                                    placeholder="First Name">
                                <input type="text" id="fullName" name="lastname"
                                    value="<?php echo htmlspecialchars($user['lastname'], ENT_QUOTES); ?>"
                                    placeholder="Last Name">
                            </div>
                            <div class="name">

                            </div>
                            <label for="username" style="margin-right:50%;">Username</label>
                            <label for="gender">Gender</label>
                            <div class="name">
                                <input type="text" id="username" name="username" disabled placeholder="Username"
                                    value="<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>" style="width:150%;">
                                <select name="gender" id="gender" required>
                                    <option value="" disabled>Select Gender</option>
                                    <option value="male" <?php echo ($user['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="female" <?php echo ($user['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                                </select>

                            </div>
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone" maxlength="11" required
                                pattern="^(\+639|09)\d{9}$" placeholder="e.g. 09123456789"
                                value="<?php echo htmlspecialchars($user['phone_number'], ENT_QUOTES); ?>">
                            <label for="home-address">Home Address</label>
                            <div class="name">
                                <select name="city" id="city">
                                    <?php
                                    if (isset($user['city'])) {
                                        echo '<option value="' . $user['city'] . '" selected>' . $user['city'] . '</option>';
                                    } else {
                                        echo '<option value="" selected disabled>Select City/Municipality</option>';
                                    }; ?>
                                    <!-- Auto Generated country throu JS -->
                                </select>
                                <select name="province" id="province">
                                    <?php
                                    if (isset($user['province'])) {
                                        echo '<option value="' . $user['province'] . '" selected>' . $user['province'] . '</option>';
                                    } else {
                                        echo '<option value="" selected disabled>Select Province</option>';
                                    }; ?>
                                    <!-- Auto Generated country throu JS -->
                                </select>
                                <select name="country" id="country" required>
                                    <option value="" selected disabled>Select Country</option>
                                    <?php
                                    if (isset($user['country'])) {
                                        echo '<option value="' . $user['country'] . '" selected>' . $user['country'] . '</option>';
                                    } else {
                                    }; ?>
                                    <!-- Auto Generated country throu JS -->
                                </select>
                            </div>
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
            var $addPasswordForm = $('#addPasswordForm');
            var $passwordError = $('#passwordError');
            var $passwordStrength = $('#passwordStrength');

            $passwordForm.on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                var oldPassword = $('#oldPassword').val();
                var newPassword = $('#newPassword').val();
                var confirmPassword = $('#confirmPassword').val();

                $.ajax({
                    url: 'php/changePass.php',
                    type: 'POST',
                    data: {
                        oldPassword: oldPassword,
                        newPassword: newPassword,
                        confirmPassword: confirmPassword
                    },
                    success: function(response) {
                        $('.message-display').show();
                        var result = JSON.parse(response);
                        if (result.status == 'success') {
                            $('.message-display .message-content').text(result.message);
                            $('.message-display').css({
                                'background-color': ' #a9dc90', // Set background color
                                'color': '#365b4c' // Set text color
                            });

                            setTimeout(() => {
                                window.location.href = window.location.href;
                            }, 1500);
                            $passwordForm[0].reset(); // Reset the form
                            $passwordStrength.text('');
                        } else {
                            $('.message-display').text(result.message);

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
            $addPasswordForm.on('submit', function(e) {
                e.preventDefault();

                var password = $('#password').val();
                var confirm_password = $('#confirm_password').val();

                $.ajax({
                    url: 'php/addPassword.php',
                    type: 'POST',
                    data: {
                        password: password,
                        confirm_password: confirm_password
                    },
                    success: function(response) {
                        // Handle the response
                        var result = JSON.parse(response);
                        $('.message-display').show();
                        if (result.success) {
                            $('.message-display').text(result.message);

                            setTimeout(() => {
                                window.location.href = window.location.href;
                            }, 1500);

                            $('#addPasswordForm').reset();
                            $passwordStrength.text('');
                        } else {
                            $('.message-display').text(result.message);

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
            const countries = [
                "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria",
                "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia",
                "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodia",
                "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo (Congo-Brazzaville)",
                "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Democratic Republic of the Congo", "Denmark", "Djibouti", "Dominica",
                "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini", "Ethiopia",
                "Fiji", "Finland", "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea",
                "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel",
                "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea (North)", "Korea (South)", "Kuwait", "Kyrgyzstan",
                "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Madagascar", "Malawi",
                "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova",
                "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand",
                "Nicaragua", "Niger", "Nigeria", "North Macedonia", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea",
                "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia",
                "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles",
                "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Sudan", "Spain", "Sri Lanka",
                "Sudan", "Suriname", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tonga",
                "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom",
                "United States of America", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"
            ];

            // Dropdown elements
            const $countryDropdown = $('#country');
            const $provinceDropdown = $('#province');
            const $cityDropdown = $('#city');

            // Populate country dropdown
            $.each(countries, function(_, country) {
                $countryDropdown.append(`<option value="${country}">${country}</option>`);
            });

            // Handle country change event
            $countryDropdown.on('change', function() {
                const selectedCountry = $(this).val();

                if (selectedCountry === "Philippines") {
                    $provinceDropdown.prop('required', true);
                    $cityDropdown.prop('required', true);

                    // Fetch provinces
                    $.ajax({
                        url: 'php/getProvinces.php',
                        method: 'GET',
                        dataType: 'json',
                        success: function(provinces) {
                            $('.input-group.province').css('display', 'block');
                            $provinceDropdown.html('<option value="" selected disabled>Select Province</option>');
                            $.each(provinces, function(_, province) {
                                $provinceDropdown.append(`<option value="${province.name}" data-code="${province.code}">${province.name}</option>`);
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching provinces:", error);
                        }
                    });
                } else {
                    $provinceDropdown.prop('disabled', true).html('<option value="" selected disabled>Select Province</option>');
                    $cityDropdown.prop('disabled', true).html('<option value="" selected disabled>Select City/Municipality</option>');
                }
            });

            // Handle province change event
            $provinceDropdown.on('change', function() {
                const provinceId = $(this).find(':selected').data('code');

                if (provinceId) {
                    $cityDropdown.prop('disabled', false);

                    // Fetch cities
                    $.ajax({
                        url: 'php/getCities.php',
                        method: 'GET',
                        data: {
                            provinceId: provinceId
                        },
                        dataType: 'json',
                        success: function(cities) {
                            $('.input-group.city').css('display', 'block');
                            $cityDropdown.html('<option value="" selected disabled>Select City/Municipality</option>');
                            $.each(cities, function(_, city) {
                                $cityDropdown.append(`<option value="${city.name}">${city.name}</option>`);
                            });
                        },
                        error: function(xhr, status, error) {
                            const errorMessage = xhr.responseText || error;
                            Toast.fire({
                                icon: 'error',
                                title: `Error: ${errorMessage}`
                            });
                        }
                    });
                } else {
                    $cityDropdown.prop('disabled', true).html('<option value="" selected disabled>Select City/Municipality</option>');
                }
            });
        });
    </script>
</body>

</html>