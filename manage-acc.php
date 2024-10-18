<?php session_start();
require_once 'func/user_func.php';
require_once 'func/func.php';
require_once 'include/db_conn.php';



$user_id = $_SESSION['user_id'] ?? 0;
$user = getUserById($conn, $user_id);
?>

<head> <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo $webIcon ?>">
    <title>BagoTours</title>
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<style>
    .container {
        display: flex;
        justify-content: center;
        margin: 20px auto;
        max-width: 1200px;
        width: 750px;
        background: #fff;
        border: 1px solid #037d54;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .editUser {
        flex: 1;
        max-width: 200px;
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

    h3 {
        margin-top: 0;
    }

    main input[type="file"],
    img#profilePreview {
        margin-top: 10px;
        margin-left: 25%;
    }

    .profilepic img {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 20px;
    }

    form {
        margin: 20px 0;
    }

    main input[type="text"],
    main input[type="tel"],
    main input[type="password"],
    main input[type="email"],
    main select {
        width: calc(100% - 22px);
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    input[type="submit"],
    button {
        background-color: #04AA6D;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    input[type="submit"]:hover,
    button:hover {
        background-color: #037d54;
    }

    .error {
        color: red;
        display: none;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table,
    th,
    td {
        border: 1px solid #ddd;
    }

    th {
        background-color: #f4f4f4;
        padding: 10px;
    }

    td {
        padding: 10px;
        text-align: left;
    }

    .modal {
        overflow-y: scroll;
        display: none;
        position: absolute;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        padding-top: 60px;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        border-radius: 8px;
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
        text-decoration: none;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .container {
            flex-direction: column;
            padding: 10px;
        }

        .editUser {
            margin-bottom: 20px;
            border: none;
        }

        aside {
            max-width: 100%;
        }
    }

    @media (max-width: 480px) {
        .editUser ul li a {
            font-size: 16px;
        }
    }

    .upload-area {
        width: 95%;
        height: 200px;
        border: 2px dashed #04AA6D;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        text-align: center;
    }

    .upload-area img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .upload-area:hover {
        background-color: #f4f4f4;
    }

    #mapboxModal .modal-content {
        width: 50%;
        height: 80%;
        max-width: 80%;
        max-height: 100%;
    }

    #pp-icon {
        margin-left: -13%;
        cursor: pointer;
        background-color: silver;
        border-radius: 50%;
    }
</style>

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
                                Details</a>
                        </li>
                        <li><a href="#" data-section="changepassword"><i class="fas fa-lock"></i> Change Password</a>
                        </li>
                        <li><a href="#" data-section="notifications"><i class="fas fa-bell"></i> Notifications</a></li>
                        <li><a href="#" data-section="upgrade"><i class="fas fa-arrow-up"></i> Upgrade</a></li>
                    </ul>
                </div>
                <aside>
                    <div class="Account">
                        <?php if (!empty($user)) { ?>
                            <img src="../upload/Profile Pictures/<?php echo $user['profile_picture'] ?>"
                                alt="Profile Preview" style="width:100px;">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                            <p><strong>Username:</strong>
                                <?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p><strong>Email
                                    Address:</strong><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                            <p><strong>Home
                                    Address:</strong><?php echo htmlspecialchars($user['home_address'], ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                            <p><strong>Phone:</strong><?php echo htmlspecialchars($user['phone_number'], ENT_QUOTES, 'UTF-8'); ?>
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

                            <span class="error" id="passwordError" style="display: none;">Passwords do not match!</span>

                            <input type="submit" value="Save">
                        </form>
                    </div>
                    <div class="personalDetails">
                        <h3>Personal Details</h3>
                        <form action="../php/updateAcc.php" method="POST" enctype="multipart/form-data">
                            <div class="profilepic" id="profilePic">
                                <img id="profilePreview"
                                    src="../upload/Profile Pictures/<?php echo $user['profile_picture'] ?>"
                                    alt="Profile Preview">
                                <input type="file" id="profilePicture" name="profilePicture">
                                <label for="profilePicture" id="pp-icon"><i class="fa fa-camera"
                                        aria-hidden="true"></i></label>
                            </div>
                            <label for="fullName">Full Name:</label>
                            <input type="text" id="fullName" name="fullName"
                                value="<?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?>">
                            <label for="username">username:</label>
                            <input type="text" id="username" name="username"
                                value="<?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>">
                            <label for="email">Email Address:</label>
                            <input type="email" id="email" name="email"
                                value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>">
                            <label for="phone">Phone:</label>
                            <input type="tel" id="phone" name="phone" maxlength="11" required
                                pattern="^(09|\+639)\d{9}$" placeholder="e.g. 09123456789"
                                value="<?php echo htmlspecialchars($user['phone_number'], ENT_QUOTES, 'UTF-8'); ?>">
                            <label for="home-address">Home Address:</label>
                            <input type="text" id="home-address" name="home-address"
                                value="<?php echo htmlspecialchars($user['home_address'], ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="submit" value="Update">
                        </form>
                    </div>
                    <div class="notifications">
                        <h3>Notification Settings</h3>
                        <form>
                            <div class="notif" style="margin-bottom: 20px;">
                                <label><input type="checkbox" id="emailNotifications" name="emailNotifications" checked>
                                    Email Notifications</label>
                                <label><input type="checkbox" id="smsNotifications" name="smsNotifications"> SMS
                                    Notifications</label>
                                <label><input type="checkbox" id="appNotifications" name="appNotifications" checked> App
                                    Notifications</label>
                            </div>
                            <input type="submit" value="Save">
                        </form>
                    </div>
                    <div class="upgrade">
                        <h3>Become an Tourist Attraction Owner</h3>
                        <p>Are you an <strong>Owner</strong> of a Resorts, Beach Resort, Swimming pool or etc.?</p>
                        <button onclick="showUpgradeModal()">Upgrade</button>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</body>

<div id="upgradeModal" class="modal">
    <div class="modal-content">
        <div class="float-right">
            <span class="close" onclick="closeUpgradeModal()">&times;</span>
        </div>
        <h2>Upgrade Confirmation</h2>
        <p>Are you sure you want to upgrade your account to Owner?</p>
        <div class="d-flex justify-content-end">
            <button class="mr-3 btn btn-success" onclick="confirmUpgrade()">Confirm</button>
            <button class="btn btn-danger" onclick="closeUpgradeModal()">Cancel</button>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById("changePasswordForm").addEventListener("submit", function (event) {
            event.preventDefault();

            const oldPassword = document.getElementById("oldPassword").value;
            const newPassword = document.getElementById("newPassword").value;
            const confirmPassword = document.getElementById("confirmPassword").value;

            const passwordError = document.getElementById("passwordError");
            if (newPassword !== confirmPassword) {
                passwordError.style.display = "block";
                passwordError.innerHTML = "Passwords do not match!";
                return;
            } else {
                passwordError.style.display = "none";
            }

            const formData = new FormData();
            formData.append("oldPassword", oldPassword);
            formData.append("newPassword", newPassword);
            formData.append("confirmPassword", confirmPassword);

            fetch("../php/changePass.php", {
                method: "POST",
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "error") {
                        passwordError.style.display = "block";
                        passwordError.innerHTML = data.message;
                    } else if (data.status === "success") {
                        alert("Password updated successfully!");
                        passwordError.style.display = "none";
                    }
                })
                .catch(error => console.error("Error:", error));
        });
        document.querySelectorAll('.editUser ul li a').forEach(link => {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                const section = this.getAttribute('data-section');
                document.querySelectorAll('aside > div').forEach(div => {
                    div.style.display = 'none';
                });
                document.querySelector('.' + section).style.display = 'block';
            });
        });

        function validateForm() {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const errorSpan = document.getElementById('passwordError');
            if (newPassword !== confirmPassword) {
                errorSpan.style.display = 'block';
                return false;
            } else {
                errorSpan.style.display = 'none';
                return true;
            }
        }

        function checkPasswordStrength() {
            const newPassword = document.getElementById("newPassword").value;
            const passwordStrength = document.getElementById("passwordStrength");

            if (newPassword.length < 6) {
                passwordStrength.innerHTML = "Weak";
                passwordStrength.style.color = "red";
            } else if (newPassword.length >= 6 && newPassword.length < 10) {
                passwordStrength.innerHTML = "Medium";
                passwordStrength.style.color = "orange";
            } else {
                passwordStrength.innerHTML = "Strong";
                passwordStrength.style.color = "green";
            }
        }

        function previewImage(event) {
            const profilePreview = document.getElementById('profilePreview');
            const file = event.target.files[0];
            if (file) {
                const fileReader = new FileReader();
                fileReader.onload = function () {
                    profilePreview.src = fileReader.result;
                };
                fileReader.readAsDataURL(file);
            }
        }

        document.getElementById('profilePicture').addEventListener('change', previewImage);

    });

    function showUpgradeModal() {
        document.getElementById('upgradeModal').style.display = 'block';
    }

    function closeUpgradeModal() {
        document.getElementById('upgradeModal').style.display = 'none';
    }

    function confirmUpgrade() {
        window.location.href = "form.php";
    }
</script>