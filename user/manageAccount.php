<?php
session_start();
include("../func/user_func.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Styles */
body {
    font-family: Arial, Helvetica, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
    transition: background-color 0.3s, color 0.3s;
}


/* Navigation Bar Styles */


/* Main Content and Sidebar Styles */
.container {
    display: flex;
    justify-content: center;
    margin: 20px auto;
    max-width: 1200px;
    border: #037d54 1px solid;
    width: 750px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.editUser {
    flex: 1;
    max-width: 200px;
    padding: 20px;
    border-right: 1px solid #ddd;
}

.editUser ul {
    list-style-type: none;
    padding: 0;
}

.editUser ul li {
    margin: 10px 0;
}

.editUser ul li a {
    color: #333;
    text-decoration: none;
    font-weight: bold;
}

.editUser ul li a:hover {
    color: #04AA6D;
}

/* Aside Styles */
aside {
    flex: 3;
    padding: 20px;
}

aside > div {
    display: none;
}

.Account, .changepassword, .personalDetails, .notifications, .loginAlerts, .booking, .upgrade {
    display: none;
}

.Account {
    display: block;
}

h3 {
    margin-top: 0;
}

input[type="file"] {
    display: block;
    margin-top: 10px;
}

img#profilePreview {
    max-width: 100px;
    margin-top: 10px;
}

form {
    margin: 20px 0;
}

input[type="text"], input[type="password"], input[type="email"] {
    width: calc(100% - 22px);
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

input[type="submit"], button {
    background-color: #04AA6D;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

input[type="submit"]:hover, button:hover {
    background-color: #037d54;
}

.error {
    color: red;
    display: none;
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 10px;
    text-align: left;
}

th {
    background-color: #f4f4f4;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4);
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

/* Responsive Styles */
@media (max-width: 768px) {
    .container {
        flex-direction: column;
        padding: 10px;
    }

    .editUser, aside {
        max-width: 100%;
        border: none;
    }

    .editUser {
        margin-bottom: 20px;
    }
}

@media (max-width: 480px) {
    .topnav {
        flex-direction: column;
        align-items: flex-start;
        height: auto;
    }

    .topnav a {
        display: block;
        width: 100%;
    }

    .topnav input[type=text] {
        width: 100%;
        margin-top: 10px;
    }

    .editUser ul li a {
        font-size: 16px;
    }
}

    </style>
</head>
<body>
    <main>
    <?php include('inc/topnav.php'); ?>
        <div class="container">
            <div class="editUser">  
                <h2>Profile</h2>
                <ul>
                    <li><a href="#" onclick="showContent('Account')"><i class="fas fa-user"></i> Account</a></li> 
                    <li><a href="#" onclick="showContent('changepassword')"><i class="fas fa-lock"></i> Change Password</a></li>
                    <li><a href="#" onclick="showContent('personalDetails')"><i class="fas fa-id-card"></i> Personal Details</a></li>
                    <li><a href="#" onclick="showContent('notifications')"><i class="fas fa-bell"></i> Notifications</a></li>
                    <li><a href="#" onclick="showContent('booking')"><i class="fas fa-calendar-check"></i> Booking</a></li>
                    <li><a href="#" onclick="showContent('upgrade')"><i class="fas fa-arrow-up"></i> Upgrade</a></li>
                </ul>
            </div>
            <aside>
                <div class="Account">
                    
                    <img src="../assets/gallery-1.jpg" alt="Profile Preview" style="width:100px; margin-top:10px;">
                    <p>Username: John Doe</p>
                    <p>Email: john@example.com</p>
                    <p>Phone: 1234567890</p>
                    
                </div>
                <div class="changepassword">
                    <h3>Change Password</h3>
                    <form onsubmit="return validateForm()">
                        <label for="oldPassword">Old Password:</label>
                        <input type="password" id="oldPassword" name="oldPassword" required><br><br>
                        <label for="newPassword">New Password:</label>
                        <input type="password" id="newPassword" name="newPassword" required onkeyup="checkPasswordStrength()"><br><br>
                        <div id="passwordStrength"></div><br>
                        <label for="confirmPassword">Confirm Password:</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" required><br><br>
                        <span class="error" id="passwordError">Passwords do not match!</span><br><br>
                        <input type="submit" value="Change">
                    </form>
                </div>
                <div class="personalDetails">
                    <h3>Personal Details</h3>
                    <form>
                    <h3>Account Information</h3>
                        <label for="profilePicture">Change Profile Picture:</label>
                        <input type="file" id="profilePicture" onchange="previewImage(event)">
                        <img id="profilePreview" src="../assets/gallery-1.jpg" alt="Profile Preview" style="width:100px; margin-top:10px;"> <br>
                        <label for="fullName">Full Name:</label>
                        <input type="text" id="fullName" name="fullName" value="John Doe" required><br><br>
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="john@example.com" required><br><br>
                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone" value="1234567890" required><br><br>
                        <input type="submit" value="Update Details">
                    </form>
                </div>
                <div class="notifications">
                    <h3>Notification Settings</h3>
                    <form>
                        <label><input type="checkbox" id="emailNotifications" name="emailNotifications" checked> Email Notifications</label><br>
                        <label><input type="checkbox" id="smsNotifications" name="smsNotifications"> SMS Notifications</label><br>
                        <label><input type="checkbox" id="appNotifications" name="appNotifications" checked> App Notifications</label><br>
                        <input type="submit" value="Save Settings">
                    </form>
                </div>
                <div class="booking">
                    <h3>Booking History</h3>
                    <table>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Destination</th>
                            <th>Price</th>
                        </tr>
                        <tr>
                            <td>12/01/2022</td>
                            <td>10:00 AM</td>
                            <td>Paris</td>
                            <td>$100</td>
                        </tr>
                        <tr>
                            <td>12/02/2022</td>
                            <td>12:00 PM</td>
                            <td>New York</td>
                            <td>$200</td>
                        </tr>
                    </table>
                </div>
                <div class="upgrade">
                    <h3>Upgrade Account to Owner</h3>
                    <p>Are you an Owner of a resort, Beach, Pools?</p>
                    <button onclick="showUpgradeModal()">Upgrade</button>
                </div>
                
            </aside>    
        </div>
    </main>
    <div id="upgradeModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUpgradeModal()">&times;</span>
            <h2>Upgrade Confirmation</h2>
            <p>Are you sure you want to upgrade your account to Owner?</p>
            <button onclick="confirmUpgrade()">Confirm</button>
            <button onclick="closeUpgradeModal()">Cancel</button>
        </div>
        <div id="resortOwnerModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeResortOwnerModal()">&times;</span>
            <h2>Resort Owner Details</h2>
            <form id="resortOwnerForm">
                <label for="resortName">Resort Name:</label>
                <input type="text" id="resortName" name="resortName" required><br><br>
                <label for="resortLocation">Location:</label>
                <input type="text" id="resortLocation" name="resortLocation" required><br><br>
                <label for="resortDescription">Description:</label>
                <textarea id="resortDescription" name="resortDescription" required></textarea><br><br>
                <input type="submit" value="Submit">
            </form>
        </div>
    </div>
    </div>
    <script>
        function showContent(section) {
            const sections = document.querySelectorAll('aside > div');
            sections.forEach((div) => div.style.display = 'none');
            document.querySelector('.' + section).style.display = 'block';
        }

        function validateForm() {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const errorElement = document.getElementById('passwordError');
            
            if (newPassword !== confirmPassword) {
                errorElement.style.display = 'block';
                return false;
            } else {
                errorElement.style.display = 'none';
                return true;
            }
        }

        function checkPasswordStrength() {
            const password = document.getElementById('newPassword').value;
            const strength = document.getElementById('passwordStrength');
            let strengthText = '';
            
            if (password.length >= 8) {
                strengthText = 'Strong';
                strength.style.color = 'green';
            } else if (password.length >= 4) {
                strengthText = 'Medium';
                strength.style.color = 'orange';
            } else {
                strengthText = 'Weak';
                strength.style.color = 'red';
            }
            
            strength.textContent = 'Password Strength: ' + strengthText;
        }

        function previewImage(event) {
            const preview = document.getElementById('profilePreview');
            preview.src = URL.createObjectURL(event.target.files[0]);
            preview.style.display = 'block';
        }

        function showUpgradeModal() {
            document.getElementById('upgradeModal').style.display = 'block';
        }

        function closeUpgradeModal() {
            document.getElementById('upgradeModal').style.display = 'none';
        }

        function confirmUpgrade() {
            closeUpgradeModal();
            document.getElementById('resortOwnerModal').style.display = 'block';
        }

        function closeResortOwnerModal() {
            document.getElementById('resortOwnerModal').style.display = 'none';
        }

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('upgradeModal')) {
                closeUpgradeModal();
            }
            if (event.target == document.getElementById('resortOwnerModal')) {
                closeResortOwnerModal();
            }
        }
    </script>
</body>
</html>
