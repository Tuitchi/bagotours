<?php session_start() ?>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet" />
</head>
<style>
    main {
        height: 675px !important;
    }

    main {
    height: 675px !important;
}

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
    padding: 20px;
}

.editUser {
    flex: 1;
    max-width: 200px;
    padding-right: 20px;
    border-right: 1px solid #ddd;
}

.editUser ul {
    padding: 0;
    list-style: none;
    margin: 0;
}

.editUser ul li {
    margin: 10px 0;
}

.editUser ul li a {
    color: #333;
    font-weight: bold;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 5px;
}

.editUser ul li a:hover {
    color: #04AA6D;
}

aside {
    flex: 3;
    padding: 20px;
}

aside > div {
    display: none;
}

/* Profile Container */
.Account {
    display: flex;
    flex-direction: row;
    align-items: center;
    background-color: #f7f7f7;
    padding: 10px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 400px;
    margin: 20px auto;
}

/* Profile Image */
.profile-image {
    border-radius: 50%;
    width: 100px;
    height: 100px;
    object-fit: cover;
    margin-right: 15px;
    border: 2px solid #ddd;
}

/* Profile Details */
.profile-details {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* Profile Items */
.profile-item {
    font-size: 16px;
    color: #333;
    margin: 0;
}


.password-strength {
    color: red;
    margin-top: 5px;
}

/* Map Container */

/* Personal Details Container */
.personalDetails {
    background-color: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 550px;
    margin: 20px auto;
    font-family: Arial, sans-serif;
}

/* Section Heading */
.personalDetails h3 {
    margin-bottom: 15px;
    font-size: 18px;
    color: #333;
    text-align: center;
}

/* Labels */
.personalDetails label {
    display: block;
    margin-top: 10px;
    font-weight: bold;
    color: #555;
}

/* Input Fields */
.personalDetails input[type="text"],
.personalDetails input[type="email"],
.personalDetails input[type="file"] {
    width: calc(100% - 20px);
    padding: 10px;
    margin-top: 5px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-sizing: border-box;
}

/* Profile Picture Preview */
#profilePreview {
    display: block;
    margin: 10px 0;
    border-radius: 50%;
    width: 80px;
    height: 80px;
    object-fit: cover;
}

/* Submit Button */
.personalDetails input[type="submit"] {
    width: 100%;
    padding: 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    margin-top: 10px;
}

/* Submit Button Hover */
.personalDetails input[type="submit"]:hover {
    background-color: #45a049;
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
/* changepassword */
.changepassword {
    background-color: #fff;
    padding: 25px;
    border: 1px solid #ddd;
    border-radius: 8px;
    max-width: 450px;
    margin: 0 auto;
}

.changepassword h3 {
    font-size: 24px;
    margin-bottom: 20px;
    color: #333;
}

.changepassword label {
    display: block;
    font-weight: bold;
    margin-bottom: 8px;
    color: #444;
}

.changepassword input[type="password"] {
    width: calc(100% - 22px);
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

.changepassword .password-strength {
    margin-bottom: 15px;
    font-size: 14px;
    color: #e74c3c;
}

.changepassword .error {
    display: block;
    margin-top: 5px;
    color: #e74c3c;
    font-size: 14px;
}

.changepassword input[type="submit"] {
    width: 100%;
    padding: 12px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

.changepassword input[type="submit"]:hover {
    background-color: #0056b3;
}


.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.4);
    overflow-y: auto;
    padding-top: 60px;
}

.modal-content {
    background-color: #fefefe;
    margin: auto;
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

.error {
    color: red;
    font-size: 12px;
    display: none;
    margin-top: 5px;
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
                <img src="../assets/gallery-1.jpg" alt="Profile Preview" class="profile-image">
                <div class="profile-details">
                    <p class="profile-item">Username: John Doe</p>
                    <p class="profile-item">Email: john@example.com</p>
                    <p class="profile-item">Phone: 1234567890</p>
                </div>
            </div>

            <div class="changepassword">
                <h3>Change Password</h3>
                <form onsubmit="return validateForm()">
                    <label for="oldPassword">Old Password:</label>
                    <input type="password" id="oldPassword" name="oldPassword" required>

                    <label for="newPassword">New Password:</label>
                    <input type="password" id="newPassword" name="newPassword" required onkeyup="checkPasswordStrength()">
                    <div id="passwordStrength" class="password-strength"></div>

                    <label for="confirmPassword">Confirm Password:</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                    <span class="error" id="passwordError">Passwords do not match!</span>

                    <input type="submit" value="Change">
                </form>
            </div>

            <div class="personalDetails">
                <h3>Personal Details</h3>
                <form>
                <img id="profilePreview" src="../assets/gallery-1.jpg" alt="Profile Preview" style="width:100px;">
                    <label for="profilePicture">Change Profile Picture:</label>
                    <input type="file" id="profilePicture" onchange="previewImage(event)">
                    

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
                <p>Are you an <strong>Owner</strong> of a Resorts, Beach Resort, Swimming pool or etc.?</p>
                <button onclick="showUpgradeModal()">Upgrade</button>
            </div>
        </aside>
    </div>
</main>
<?php include 'inc/footer.php' ?>

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
        <h2>Tourist Attraction Owner Details</h2>
        <form id="resortOwnerForm">
            <label for="resortName">Tourist Attraction Name:</label>
            <input type="text" id="resortName" name="resortName" required>
            <label for="proof">Type of Attraction:</label>
            <select name="type" id="type">
                <option value="none" selected disabled hidden>Select an Option</option>
                <option value="Beach Resort">Beach Resort</option>
                <option value="Campsite">Campsite</option>
                <option value="Falls">Falls</option>
                <option value="Historical Landmark">Historical Landmark</option>
                <option value="Mountain Resort">Mountain Resort</option>
                <option value="Park">Park</option>
                <option value="Swimming Pool">Swimming Pool</option>
            </select>
            <label for="resortLocation">Location:</label>
            <div style="clear:both;">
                <input type="text" id="resortLocation" name="resortLocation" readonly required>
                <button id="resortLoc" type="button"><i class="fa fa-map-marker"></i></button>
                <select id="barangay" name="barangay" style="width: 44%; float: left;">
                    <option value="none" selected disabled hidden>Select a Barangay</option>
                    <option value="Abuanan">Abuanan</option>
                    <option value="Alianza">Alianza</option>
                    <option value="Atipuluan">Atipuluan</option>
                    <option value="Bacong-Montilla">Bacong-Montilla</option>
                    <option value="Bagroy">Bagroy</option>
                    <option value="Balingasag">Balingasag</option>
                    <option value="Banago">Banago</option>
                    <option value="Binubuhan">Binubuhan</option>
                    <option value="Busay">Busay</option>
                    <option value="Calumangan">Calumangan</option>
                    <option value="Caridad">Caridad</option>
                    <option value="Dulao">Dulao</option>
                    <option value="Ilijan">Ilijan</option>
                    <option value="Lag-asan">Lag-asan</option>
                    <option value="Ma-ao Barrio">Ma-ao Barrio</option>
                    <option value="Mailum">Mailum</option>
                    <option value="Malingin">Malingin</option>
                    <option value="Napoles">Napoles</option>
                    <option value="Pacol">Pacol</option>
                    <option value="Poblacion">Poblacion</option>
                    <option value="Sampinit">Sampinit</option>
                    <option value="Tabunan">Tabunan</option>
                    <option value="Taloc">Taloc</option>
                    <option value="Tampalon">Tampalon</option>
                </select>
                <select id="purok" name="purok" style="width: 44%;float: left;">
                    <option value="none" selected disabled hidden>Select a Purok</option>
                </select>
                <input type="hidden" id="tour-latitude" name="latitude">
                <input type="hidden" id="tour-longitude" name="longitude">
            </div><br style="clear:both;" />
            <label for="proof">Proof:</label>
            <select name="proof" id="proof">
                <option value="none" selected disabled hidden>Select an Option</option>
                <option value="Business permit">Business Permit</option>
                <option value="Occupancy permit">Occupancy Permit</option>
                <option value="Building Permit">Building Permit</option>
                <option value="Mayor's Permit">Mayor's Permit</option>
                <option value="Barangay Permit">Barangay Permit</option>
            </select>
            <p style="font-size:smaller;">Insert your proof image below.</p>
            <div class="upload-area" id="uploadArea">
                <p>Drag & Drop or Click to Upload File</p>
                <input type="file" id="fileInput" hidden>
            </div>
            <input type="submit" value="Submit">
        </form>
    </div>
</div>
<!-- Mapbox Modal -->
<div id="mapboxModal" class="modal">
    <div class="modal-content">
        <span class="close-map">&times;</span>
        <div id="map" style="height: 94%; width:100%"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Profile section toggle logic
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

        // Password validation and strength checking
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
            const strengthBar = document.getElementById('passwordStrength');
            const password = document.getElementById('newPassword').value;
            const strength = password.length > 8 ? 'Strong' : 'Weak';
            strengthBar.textContent = `Password Strength: ${strength}`;
        }

        // Image Preview for profile picture
        function previewImage(event) {
            const profilePreview = document.getElementById('profilePreview');
            profilePreview.src = URL.createObjectURL(event.target.files[0]);
        }

        // Mapbox initialization for resort location modal
        const mapboxModal = document.getElementById("mapboxModal");
        const btnSetLocation = document.getElementById("resortLoc");
        const closeMapBtn = document.querySelector(".close-map");

        mapboxgl.accessToken = 'pk.eyJ1Ijoibmlrb2xhaTEyMjIiLCJhIjoiY2x6d3pva281MGx6ODJrczJhaTJ4M2RmYyJ9.0sJ2ZGR2xpEza2j370y3rQ';
        const map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [122.9413, 10.4998],
            zoom: 10.2
        });
        map.addControl(new mapboxgl.NavigationControl());
        map.addControl(new mapboxgl.GeolocateControl({
            positionOptions: {
                enableHighAccuracy: true
            },
            trackUserLocation: true,
            showUserHeading: true
        }));

        let marker;

        function reverseGeocode(lng, lat) {
            const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json?access_token=${mapboxgl.accessToken}`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const placeName = data.features[0]?.place_name || "Location not found";
                    document.getElementById('resortLocation').value = placeName;
                })
                .catch(err => console.error("Error in reverse geocoding: ", err));
        }

        map.on('click', function(e) {
            const lngLat = e.lngLat;
            if (marker) {
                marker.setLngLat(lngLat);
            } else {
                marker = new mapboxgl.Marker().setLngLat(lngLat).addTo(map);
            }
            reverseGeocode(lngLat.lng, lngLat.lat);
            document.getElementById('tour-longitude').value = lngLat.lng;
            document.getElementById('tour-latitude').value = lngLat.lat;
            mapboxModal.style.display = "none";
        });

        btnSetLocation.onclick = function() {
            mapboxModal.style.display = "block";
            map.resize(); // Ensure the map resizes properly when the modal opens
        };

        closeMapBtn.onclick = function() {
            mapboxModal.style.display = "none";
        };

        window.onclick = function(event) {
            if (event.target == mapboxModal) {
                mapboxModal.style.display = "none";
            }
        };
    });

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
</script>