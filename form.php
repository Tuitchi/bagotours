<?php
session_start();
include 'include/db_conn.php';

$toast = '';
$user_id = $_SESSION['user_id'];

require_once('func/user_func.php');
registerExpiry($conn, $user_id);
$status = registerStatus($user_id);

if (isset($_GET['process'])) {
    $toast = $_GET['process'];

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Tourist Attraction</title>
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo $webIcon ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="user.css">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet" />
    <style>
        .main {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        #resortOwnerForm {
            margin:auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 80vh;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        main input[type="text"],
        main select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #resortLocation {
            float: left;
            width: 70%;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        #resortLoc {
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 15%;
            float: left;
            height: 40px;
            cursor: pointer;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }


        .upload-area,
        .tourImages {
            width: 100%;
            height: auto;
            border: 2px dashed #ccc;
            text-align: center;
            cursor: pointer;
        }

        .tourImages {
            display: flex;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .MainTour {
            border: 1px solid #ccc;
            width: 100%;
            height: 200px;
        }

        .upload-areaTour {
            display: flex;
            border: 1px solid #ccc;
            width: 32.83%;
            height: 100px;
        }


        .upload-area:hover {
            background-color: #f9f9f9;
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
        }

        .step-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .prev-btn,
        .next-btn,
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .prev-btn:hover,
        .next-btn:hover,
        input[type="submit"]:hover {
            background-color: #218838;
        }

        .progress-container {
            display: flex;
            margin-bottom: 20px;
        }

        .progress {
            flex: 1;
            height: 10px;
            background-color: #e0e0e0;
            border-radius: 5px;
            margin-right: 5px;
        }

        .progress.active {
            background-color: #28a745;
            transition: background-color 0.5s ease;
        }

        .progress:last-child {
            margin-right: 0;
        }

        .upload-area img,
        .tourImages img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .modal {
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            position: relative;
            margin: 10% auto;
            padding: 20px;
            background-color: white;
            width: 100vh;
            height: 85vh;
        }

        .close-map {
            position: absolute;
            top: 0;
            right: 10px;
            font-size: 28px;
            z-index: 100;
            cursor: pointer;
        }

        #map {
            border-radius: 4px;
            width: 100%;
            height: 100%;
        }

        textarea {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }
        .main input[type="text"], .main select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <?php include 'nav/topnav.php' ?>
    <div class="main-container">

        <?php include 'nav/sidenav.php' ?>
        <div class="main">
            <?php
            switch ($status) {
                case null:
                    ?>
                    <form id="resortOwnerForm" enctype="multipart/form-data">
                        <div class="progress-container">
                            <div class="progress active"></div>
                            <div class="progress"></div>
                            <div class="progress"></div>
                            <div class="progress"></div>
                        </div>

                        <div class="step active" id="step1">
                            <h2>Tourist Attraction Details</h2>
                            <label for="resortName">Tourist Attraction Name:</label>
                            <input type="text" id="resortName" name="title" required>

                            <label for="type">Type of Attraction:</label>
                            <select name="type" id="type" required>
                                <option value="none" selected disabled hidden>Select an Option</option>
                                <option value="Beach Resort">Beach Resort</option>
                                <option value="Campsite">Campsite</option>
                                <option value="Falls">Falls</option>
                                <option value="Historical Landmark">Historical Landmark</option>
                                <option value="Mountain Resort">Mountain Resort</option>
                                <option value="Park">Park</option>
                                <option value="Swimming Pool">Swimming Pool</option>
                            </select>

                            <label for="description">Description:</label>
                            <textarea id="description" name="description" rows="4" required></textarea>

                            <div class="step-buttons" style="float:right">
                                <button type="button" class="next-btn">Next</button>
                            </div>
                        </div>

                        <div class="step" id="step2">
                            <h2>Tourist Attraction Image</h2>
                            <label for="img">Image:</label>
                            <p style="font-size:smaller;">Insert your tour image below.</p>
                            <div class="tourImages">
                                <input type="file" id="fileInputTour" name="img" accept="image/*" required>
                                <div class="MainTour" id="uploadAreaMainTour"></div>
                                <input type="file" id="fileInputTours" name="tourImage[]" accept="image/*" multiple required
                                    style="width: 100%;">
                                <div class="upload-areaTour" id="uploadAreaTour1"></div>
                                <div class="upload-areaTour" id="uploadAreaTour2"></div>
                                <div class="upload-areaTour" id="uploadAreaTour3"></div>
                            </div>

                            <div class="step-buttons">
                                <button type="button" class="prev-btn">Previous</button>
                                <button type="button" class="next-btn">Next</button>
                            </div>
                        </div>

                        <div class="step" id="step3">
                            <h2>Location Details</h2>
                            <label for="resortLocation">Location:</label>
                            <p style="font-size:smaller;">Available only in the Bago City area.</p>
                            <div style="clear:both;">
                                <input type="text" id="resortLocation" name="address" required readonly>
                                <button id="resortLoc" type="button"><i class="fa fa-map-marker"></i></button>
                                <input type="hidden" id="tour-latitude" name="latitude">
                                <input type="hidden" id="tour-longitude" name="longitude"></select>
                                <select id="barangay" name="barangay" style="width: 44%; float: left;margin-right:10px"
                                    required>
                                    <option value="none" selected disabled hidden>Select a Barangay</option>
                                    <option value="Abuanan">Abuanan</option>
                                    <option value="Alianza">Alianza</option>
                                    <option value="Atipuluan">Atipuluan</option>
                                    <option value="Bacong">Bacong</option>
                                    <option value="Bagroy">Bagroy</option>
                                    <option value="Balingasag">Balingasag</option>
                                    <option value="Binubuhan">Binubuhan</option>
                                    <option value="Busay">Busay</option>
                                    <option value="Calumangan">Calumangan</option>
                                    <option value="Caridad">Caridad</option>
                                    <option value="Dulao">Dulao</option>
                                    <option value="Ilijan">Ilijan</option>
                                    <option value="Lag-asan">Lag-asan</option>
                                </select>
                                <select id="purok" name="purok" style="width: 44%; float: left;" required>
                                    <option value="none" selected disabled hidden>Select a Purok</option>
                                </select>
                            </div><br style="clear:both;" />

                            <div class="step-buttons">
                                <button type="button" class="prev-btn">Previous</button>
                                <button type="button" class="next-btn">Next</button>
                            </div>
                        </div>

                        <div id="mapboxModal" class="modal">
                            <div class="modal-content">
                                <span class="close-map">&times;</span>
                                <div id="map"></div>
                            </div>
                        </div>

                        <div class="step" id="step4">
                            <h2>Proof of Permits</h2>

                            <label for="proof">Proof:</label>
                            <select name="proof" id="proof" required>
                                <option value="none" selected disabled hidden>Select an Option</option>
                                <option value="Business permit">Business Permit</option>
                                <option value="Occupancy permit">Occupancy Permit</option>
                                <option value="Building Permit">Building Permit</option>
                                <option value="Mayor's Permit">Mayor's Permit</option>
                                <option value="Barangay Permit">Barangay Permit</option>
                            </select>

                            <p style="font-size:smaller;">Insert your proof image below.</p>
                            <input type="file" id="fileInput" name="proofImage[]" multiple accept="image/*" required>
                            <div class="upload-area" id="proofUploadArea1"></div>
                            <div class="upload-area" id="proofUploadArea2"></div>
                            <div class="upload-area" id="proofUploadArea3"></div>

                            <div class="step-buttons">
                                <button type="button" class="prev-btn">Previous</button>
                                <input type="submit" value="Submit">
                            </div>
                        </div>

                    </form>
                    <?php
                    break;
                case 0:
                    ?>
                    <p>Your registration is pending approval. You will be notified once approved.</p>
                    <?php
                    break;
                case 1:
                    ?>
                    <p>Your registration has been approved. Are you sure you want to become a <strong>tourist attraction
                            owner?</strong>
                        <a href="../php/updateUserStatus.php">Click here!</a>
                    </p>
                    <?php
                    break;
                case 2:
                    ?>
                    <p>Your registration has been denied. Please try again next week.</p>
                    <?php
                    break;
                default:
                    ?>
                    <p>An unknown error occurred. Please try again later.</p>
                <?php
            }
            ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/purok.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const barangaySelect = document.getElementById('barangay');
            const purokSelect = document.getElementById('purok');
            const steps = document.querySelectorAll(".step");
            const progressBars = document.querySelectorAll(".progress");
            const nextBtns = document.querySelectorAll(".next-btn");
            const prevBtns = document.querySelectorAll(".prev-btn");
            const mapboxModal = document.getElementById("mapboxModal");
            const btnSetLocation = document.getElementById("resortLoc");
            const closeMapBtn = document.querySelector(".close-map");
            const form = document.getElementById("resortOwnerForm");

            let currentStep = 0;
            let marker;

            // Event Listener for Barangay Select
            barangaySelect.addEventListener('change', function () {
                const barangay = this.value;
                purokSelect.innerHTML = '<option value="none" selected disabled hidden>Select a Purok</option>';

                if (puroksByBarangay[barangay]) {
                    puroksByBarangay[barangay].forEach(purok => {
                        const option = new Option(purok, purok);
                        purokSelect.appendChild(option);
                    });
                }
            });

            // STEPS
            nextBtns.forEach(btn => btn.addEventListener("click", () => handleStepChange(1)));
            prevBtns.forEach(btn => btn.addEventListener("click", () => handleStepChange(-1)));

            form.addEventListener("submit", handleFormSubmit);

            function handleStepChange(direction) {
                if (direction === 1 && !validateStep(currentStep)) return;
                currentStep += direction;
                showStep(currentStep);
            }

            function showStep(stepIndex) {
                steps.forEach((step, index) => {
                    step.classList.toggle("active", index === stepIndex);
                    progressBars[index].classList.toggle("active", index <= stepIndex);
                });
            }

            function validateStep() {
                const inputs = steps[currentStep].querySelectorAll("input[required], select[required], textarea[required]");
                let valid = true;

                inputs.forEach(input => {
                    if (!input.value || input.value === "none") {
                        valid = false;
                        input.style.borderColor = "red";
                    } else {
                        input.style.borderColor = "";
                    }
                });
                return valid;
            }

            // MAP
            mapboxgl.accessToken = 'pk.eyJ1Ijoibmlrb2xhaTEyMjIiLCJhIjoiY20xemJ6NG9hMDRxdzJqc2NqZ3k5bWNlNiJ9.tAsio6eF8LqzAkTEcPLuSw';
            const map = new mapboxgl.Map({
                container: "map",
                style: "mapbox://styles/mapbox/streets-v11",
                center: [122.9413, 10.4998],
                zoom: 10.2,
            });
            map.addControl(new mapboxgl.NavigationControl());
            map.addControl(
                new mapboxgl.GeolocateControl({
                    positionOptions: {
                        enableHighAccuracy: true,
                    },
                    trackUserLocation: true,
                    showUserHeading: true,
                })
            );

            function reverseGeocode(lng, lat) {
                const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json?access_token=${mapboxgl.accessToken}`;
                fetch(url)
                    .then((response) => response.json())
                    .then((data) => {
                        const placeName = data.features[0]?.place_name || "Location not found";
                        document.getElementById("resortLocation").value = placeName;
                    })
                    .catch((err) => console.error("Error in reverse geocoding: ", err));
            }

            map.on("click", function (e) {
                const lngLat = e.lngLat;
                if (marker) {
                    marker.setLngLat(lngLat);
                } else {
                    marker = new mapboxgl.Marker().setLngLat(lngLat).addTo(map);
                }
                reverseGeocode(lngLat.lng, lngLat.lat);
                document.getElementById("tour-longitude").value = lngLat.lng;
                document.getElementById("tour-latitude").value = lngLat.lat;
                
                mapboxModal.classList.remove('active');// Hide the modal when a location is selected
            });

            btnSetLocation.onclick = function () {
                
                mapboxModal.classList.add('active');
                setTimeout(() => {
                    map.resize();
                    map.flyTo({
                        center: [122.9413, 10.4998],
                        essential: true,
                    });
                }, 200);
            };

            closeMapBtn.onclick = function () {
                mapboxModal.classList.remove('active'); // Hide the modal on close
            };

            window.onclick = function (event) {
                if (event.target == mapboxModal) {
                    mapboxModal.classList.remove('active'); // Hide the modal when clicking outside
                }
            };

            // Remaining functions for form submission, AJAX handling, and image previews
            function handleFormSubmit(e) {
                e.preventDefault();
                const formData = new FormData(form);
                console.log('Form Data:');
                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }

                $.ajax({
                    url: "../php/register_owner.php",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: handleAjaxSuccess,
                    error: handleAjaxError,
                });
            }

            function handleAjaxSuccess(response) {
                console.log("Registration Response:", response);
                let data = JSON.parse(response);
                Swal.fire({
                    icon: data.success === true ? 'success' : 'error',
                    title: data.success === true ? 'success' : 'error',
                    text: data.success === true ? data.message : data.errors,
                    timer: 3000,
                    showConfirmButton: false,
                }).then(() => {
                    if (data.success) {
                        window.location.href = "../user/form?status=success";
                    }
                });
            }

            function handleAjaxError(xhr, status, error) {
                console.error("There was a problem with the AJAX operation:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "An error occurred while processing your request.",
                    timer: 3000,
                    showConfirmButton: false,
                });
            }

            const imageUploadAreas = [{
                input: document.getElementById("fileInputTour"),
                area: document.getElementById("uploadAreaMainTour")
            },
            {
                input: document.getElementById("fileInput"),
                area: [
                    document.getElementById("proofUploadArea1"),
                    document.getElementById("proofUploadArea2"),
                    document.getElementById("proofUploadArea3"),
                ],
            },
            {
                input: document.getElementById("fileInputTours"),
                area: [
                    document.getElementById("uploadAreaTour1"),
                    document.getElementById("uploadAreaTour2"),
                    document.getElementById("uploadAreaTour3"),
                ],
            }];

            imageUploadAreas.forEach(({ input, area }) => {
                input.addEventListener("change", () => showMultipleImagePreview(input, area));
            });

            function showMultipleImagePreview(input, area) {
                const files = input.files;

                if (files.length > 3) {
                    alert(`You can only upload up to 3 images.`);
                    input.value = "";
                    area.forEach(a => a.innerHTML = "No files chosen");
                    return;
                }

                if (Array.isArray(area)) {
                    area.forEach(a => a.innerHTML = "");
                } else {
                    area.innerHTML = "";
                }

                Array.from(files).forEach((file, index) => {
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = () => {
                            if (Array.isArray(area)) {
                                if (index < area.length) {
                                    area[index].innerHTML = `<img src="${reader.result}" alt="Image Preview">`;
                                }
                            } else {
                                area.innerHTML = `<img src="${reader.result}" alt="Image Preview">`;
                            }
                        };
                        reader.readAsDataURL(file);
                    } else {
                        if (Array.isArray(area)) {
                            area.forEach(a => a.innerHTML = "No file chosen");
                        } else {
                            area.innerHTML = "No file chosen";
                        }
                    }
                });
            }
        });

    </script>
</body>

</html>