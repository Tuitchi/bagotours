<?php session_start() ?>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
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

    input[type="file"],
    img#profilePreview {
        margin-top: 10px;
    }

    form {
        margin: 20px 0;
    }

    input[type="text"],
    input[type="password"],
    input[type="email"], select {
        width: calc(100% - 22px);
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    #proof {
        width: 50%;
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
    .upload-area img{
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .upload-area:hover {
        background-color: #f4f4f4;
    }
</style>

<?php include('inc/topnav.php'); ?>

<main>
    <div class="container">
        <div class="editUser">
            <h2>Profile</h2>
            <ul>
                <li><a href="#" data-section="Account"><i class="fas fa-user"></i> Account</a></li>
                <li><a href="#" data-section="personalDetails"><i class="fas fa-id-card"></i> Personal Details</a></li>
                <li><a href="#" data-section="changepassword"><i class="fas fa-lock"></i> Change Password</a></li>
                <li><a href="#" data-section="notifications"><i class="fas fa-bell"></i> Notifications</a></li>
                <li><a href="#" data-section="upgrade"><i class="fas fa-arrow-up"></i> Upgrade</a></li>
            </ul>
        </div>
        <aside>
            <div class="Account">
                <img src="../assets/gallery-1.jpg" alt="Profile Preview" style="width:100px;">
                <p>Username: John Doe</p>
                <p>Email: john@example.com</p>
                <p>Phone: 1234567890</p>
            </div>
            <div class="changepassword">
                <h3>Change Password</h3>
                <form onsubmit="return validateForm()">
                    <label for="oldPassword">Old Password:</label>
                    <input type="password" id="oldPassword" name="oldPassword" required>
                    <label for="newPassword">New Password:</label>
                    <input type="password" id="newPassword" name="newPassword" required onkeyup="checkPasswordStrength()">
                    <div id="passwordStrength" style="color:red"></div>
                    <label for="confirmPassword">Confirm Password:</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                    <span class="error" id="passwordError">Passwords do not match!</span>
                    <input type="submit" value="Change">
                </form>
            </div>
            <div class="personalDetails">
                <h3>Personal Details</h3>
                <form>
                    <label for="profilePicture">Change Profile Picture:</label>
                    <input type="file" id="profilePicture" onchange="previewImage(event)">
                    <img id="profilePreview" src="../assets/gallery-1.jpg" alt="Profile Preview" style="width:100px;">
                    <label for="fullName">Full Name:</label>
                    <input type="text" id="fullName" name="fullName" value="John Doe" required>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="john@example.com" required>
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" value="1234567890" required>
                    <input type="submit" value="Update Details">
                </form>
            </div>
            <div class="notifications">
                <h3>Notification Settings</h3>
                <form>
                    <label><input type="checkbox" id="emailNotifications" name="emailNotifications" checked> Email Notifications</label>
                    <label><input type="checkbox" id="smsNotifications" name="smsNotifications"> SMS Notifications</label>
                    <label><input type="checkbox" id="appNotifications" name="appNotifications" checked> App Notifications</label>
                    <input type="submit" value="Save Settings">
                </form>
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
</div>

<div id="resortOwnerModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeResortOwnerModal()">&times;</span>
        <h2>Resort Owner Details</h2>
        <form id="resortOwnerForm">
            <label for="resortName">Resort Name:</label>
            <input type="text" id="resortName" name="resortName" required>
            <label for="proof">Type of resort:</label>
            <select name="type" id="type">
                <option value="none" selected disabled hidden>Select an Option</option>
                <option value="Mountain Resort">Mountain Resort</option>
                <option value="Beach Resort">Beach Resort</option>
                <option value="Park">Park</option>
                <option value="Historical Landmark">Historical Landmark</option>
                <option value="Falls">Falls</option>
            </select>
            <label for="resortLocation">Location:</label>
            <input type="text" id="resortLocation" name="resortLocation" required>
            <label for="proof">Proof:</label>
            <select name="proof" id="proof">
                <option value="none" selected disabled hidden>Select an Option</option>
                <option value="Business permit">Business Permit</option>
                <option value="Occupancy permit">Occupancy Permit</option>
                <option value="Building Permit">Building Permit</option>
                <option value="Mayor's Permit">Mayor's Permit</option>
                <option value="Barangay Permit">Barangay Permit</option>
            </select>
            <div class="upload-area" id="uploadArea">
                <p>Drag & Drop or Click to Upload File</p>
                <input type="file" id="fileInput" hidden>
            </div>
            <input type="submit" value="Submit">
        </form>
    </div>
</div>

<script>
    // Toggle sections
    document.querySelectorAll('.editUser ul li a').forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            const section = this.getAttribute('data-section');
            document.querySelectorAll('aside > div').forEach(div => {
                div.style.display = 'none';
            });
            document.querySelector('.' + section).style.display = 'block';
        });
    });

    function checkPasswordStrength() {
        const strengthBar = document.getElementById('passwordStrength');
        const password = document.getElementById('newPassword').value;
        const strength = password.length > 8 ? 'Strong' : 'Weak';
        strengthBar.textContent = `Password Strength: ${strength}`;
    }

    // Validate password form
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

    function previewImage(event) {
        const profilePreview = document.getElementById('profilePreview');
        profilePreview.src = URL.createObjectURL(event.target.files[0]);
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
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');

    uploadArea.addEventListener('click', () => fileInput.click());

    uploadArea.addEventListener('dragover', (event) => {
        event.preventDefault();
        uploadArea.style.backgroundColor = '#f4f4f4';
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.style.backgroundColor = '#fff';
    });

    uploadArea.addEventListener('drop', (event) => {
        event.preventDefault();
        const file = event.dataTransfer.files[0];
        let fileType = file.type;
        let validExtensions = ['image/jpeg', 'image/jpg', 'image/png', 'image/png'];
        if (validExtensions.includes(fileType)) {
            let fileReader = new FileReader();
            fileReader.onload = () => {
                uploadArea.style.backgroundColor = '#fff';
                const img = document.createElement('img');
                let fileUrl = fileReader.result;
                let imgTag = `<img src="${fileUrl}" alt="">`;
                uploadArea.innerHTML = imgTag;
            };
            fileReader.readAsDataURL(file);
        } else {
            alert('Invalid file type. Only JPEG, JPG, PNG images are allowed.');
        }
    });

    fileInput.addEventListener('change', () => {
        handleFiles(fileInput.files);
    });

    function handleFiles(files) {
        console.log('Files Uploaded:', files);
    }
</script>