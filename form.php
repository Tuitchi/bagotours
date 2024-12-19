<?php
session_start();
include 'include/db_conn.php';

$toast = '';
$user_id = $_SESSION['user_id'];

require_once('func/user_func.php');
$status = registerStatus($conn, $user_id);

if (isset($_GET['process'])) {
    $toast = $_GET['process'];

}
?>
<!DOCTYPE html>
<html lang="en">

<hea>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Tourist Attraction</title>
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo $webIcon ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="assets/css/form.css">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet" />
    <style>
        .announcement-card {
            width: 100%;
            max-width: 400px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .announcement-card:hover {
            transform: scale(1.03);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .announcement-header {
            font-size: 1.8em;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .announcement-subtext {
            font-size: 1em;
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .action-button {
            display: inline-block;
            padding: 12px 24px;
            font-size: 1.1em;
            color: #fff;
            background-color: #ff9800;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        .action-button:hover {
            background-color: #e68900;
        }

        .status-indicator {
            display: inline-block;
            margin: 20px 0;
            padding: 8px 15px;
            background-color: #f4f3f2;
            color: #fff;
            font-size: 1em;
            border-radius: 25px;
            font-weight: bold;
        }

        .approval {
            background-color: #e68900;
            padding: 8px 15px;
            border-radius: 15px;
        }

        .waiting-image {
            width: 120px;
            height: auto;
            margin: 20px 0;
        }

        /* Mobile responsiveness */
        @media screen and (max-width: 600px) {
            .announcement-card {
                padding: 20px;
                max-width: 70%;
            }

            .announcement-header {
                font-size: 1.5em;
            }

            .announcement-subtext {
                font-size: 0.9em;
            }

            .waiting-image {
                width: 100px;
            }

            .status-indicator {
                font-size: 0.9em;
                padding: 6px 12px;
            }

            .action-button {
                padding: 10px 20px;
                font-size: 1em;
            }
        }

        .approve-card {
            width: 100%;
            max-width: 400px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .approve-card:hover {
            transform: scale(1.03);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .header-text {
            font-size: 1.8em;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .description-text {
            font-size: 1em;
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .cta-button {
            display: inline-block;
            padding: 12px 24px;
            font-size: 1.1em;
            color: #fff;
            background-color: #28a745;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        .cta-button:hover {
            background-color: #218838;
        }

        .status-label {
            display: inline-block;
            margin: 20px 0;
            padding: 8px 15px;
            background-color: #f4f3f2;
            color: #fff;
            font-size: 1em;
            border-radius: 25px;
            font-weight: bold;
        }

        .approved-status {
            background-color: #28a745;
            padding: 8px 15px;
            border-radius: 15px;
        }

        .status-image {
            width: 120px;
            height: auto;
            margin: 20px 0;
        }

        /* Mobile responsiveness */
        @media screen and (max-width: 600px) {
            .approve-card {
                padding: 20px;
                max-width: 80%;
            }

            .header-text {
                font-size: 1.5em;
            }

            .description-text {
                font-size: 0.9em;
            }

            .status-image {
                width: 100px;
            }

            .status-label {
                font-size: 0.9em;
                padding: 6px 12px;
            }

            .cta-button {
                padding: 10px 20px;
                font-size: 1em;
            }
        }

         .rejection-card {
            width: 100%;
            max-width: 400px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .rejection-card:hover {
            transform: scale(1.03);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .rejection-header {
            font-size: 1.8em;
            font-weight: bold;
            color: #d9534f;
            margin-bottom: 15px;
        }

        .rejection-subtext {
            font-size: 1em;
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .action-button {
            display: inline-block;
            padding: 12px 24px;
            font-size: 1.1em;
            color: #fff;
            background-color: #d9534f;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        .action-button:hover {
            background-color: #c9302c;
        }

        .status-indicator {
            display: inline-block;
            margin: 20px 0;
            padding: 8px 15px;
            background-color: #f4f3f2;
            color: #fff;
            font-size: 1em;
            border-radius: 25px;
            font-weight: bold;
        }

        .rejection {
            background-color: #d9534f;
            padding: 8px 15px;
            border-radius: 15px;
        }

        .rejection-image {
            width: 120px;
            height: auto;
            margin: 20px 0;
        }

        /* Mobile responsiveness */
        @media screen and (max-width: 600px) {
            .rejection-card {
                padding: 20px;
                max-width: 80%;
            }

            .rejection-header {
                font-size: 1.5em;
            }

            .rejection-subtext {
                font-size: 0.9em;
            }

            .rejection-image {
                width: 100px;
            }

            .status-indicator {
                font-size: 0.9em;
                padding: 6px 12px;
            }

            .action-button {
                padding: 10px 20px;
                font-size: 1em;
            }
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

                                <label for="bookable">Is booking available?</label>
                                <div class="radio-group" id="bookable">
                                    <div class="radio-input">
                                        <input type="radio" id="bookableYes" name="bookable" value="1" required>
                                        <label for="bookableYes">Yes</label>
                                    </div>
                                    <div class="radio-input">
                                        <input type="radio" id="bookableNo" name="bookable" value="0" required>
                                        <label for="bookableNo">No</label>
                                    </div>
                                </div>
                                <label for="description">Description:</label>
                                <textarea id="description" name="description" rows="4" required></textarea>

                                <div class="step-buttons" style="float:right">
                                    <button type="button" class="next-btn">Next</button>
                                </div>
                            </div>

                            <div class="step" id="step2">
                                <h2>Tourist Attraction Image</h2>
                                <p style="font-size:smaller;">Insert your tour image below.</p>
                                <p id="tour-image-error" style="color: red; display: none;">You can upload a maximum of 5
                                    images.
                                </p>
                                <p id="tour-image-error" style="color: red; display: none;">Image must have a landscape view
                                    (16:10
                                    aspect ratio recommended).</p>
                                <div id="upload-notes"
                                    style="background-color: #f9f9f9; border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; font-size: 14px; color: #555;">
                                    <strong>Notes for Uploading Tour Images:</strong>
                                    <ul style="margin: 0; padding-left: 20px;">
                                        <li>Upload a maximum of <strong>5 images</strong>.</li>
                                        <li>Images should have a <strong>landscape view</strong> (16:10 aspect ratio
                                            recommended).
                                        </li>
                                        <li>Accepted formats: <strong>JPEG, PNG</strong>.</li>
                                        <li>Ensure each image size does not exceed <strong>5MB</strong>.</li>
                                    </ul>
                                </div>
                                <div class="tourImages">
                                    <div class="image-preview-container">
                                        <div class="main-image">
                                            <img id="main-image-preview" src="" alt="Main Image Preview">
                                        </div>
                                        <div class="thumbnail-images tour">
                                            <!-- Thumbnails will be displayed here -->
                                        </div>
                                    </div>
                                    <input type="file" id="tour-images" name="tour-images[]" accept="image/*" multiple required>
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
                                <p id="permit-note" style="color: red; font-size: smaller; display: none;">
                                    You can select a maximum of 3 proof documents.
                                </p>
                                <div class="permit-container">
                                    <div class="permit">
                                        <label for="permit1">
                                            <input type="checkbox" id="permit1" name="proof_permits[]" value="Building Permit">
                                            Building Permit
                                        </label>
                                    </div>
                                    <div class="permit">
                                        <label for="permit2">
                                            <input type="checkbox" id="permit2" name="proof_permits[]" value="Business Permit">
                                            Business Permit
                                        </label>
                                    </div>
                                    <div class="permit">
                                        <label for="permit3">
                                            <input type="checkbox" id="permit3" name="proof_permits[]"
                                                value="Environmental Compliance Certificate (ECC)">
                                            Environmental Compliance Certificate (ECC)
                                        </label>
                                    </div>
                                    <div class="permit">
                                        <label for="permit4">
                                            <input type="checkbox" id="permit4" name="proof_permits[]"
                                                value="Barangay Clearance">
                                            Barangay Clearance
                                        </label>
                                    </div>
                                    <div class="permit">
                                        <label for="permit5">
                                            <input type="checkbox" id="permit5" name="proof_permits[]"
                                                value="Fire Safety Inspection Certificate">
                                            Fire Safety Inspection Certificate
                                        </label>
                                    </div>
                                </div>

                                <div id="upload-notes"
                                    style="background-color: #f9f9f9; border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; font-size: 14px; color: #555;">
                                    <strong>Notes for Uploading Proof Documents:</strong>
                                    <ul style="margin: 0; padding-left: 20px;">
                                        <li>Ensure the document is clear and legible.</li>
                                        <li>Acceptable file formats: <strong>PDF, JPEG, PNG</strong>.</li>
                                        <li>Maximum file size: <strong>5MB per document</strong>.</li>
                                        <li>Document must be valid and up-to-date.</li>
                                        <br>
                                        <strong>Tips:</strong>
                                        <ul>
                                            <li>Use a flat, well-lit surface to photograph or scan the document.</li>
                                            <li>Ensure all edges of the document are visible.</li>
                                        </ul>
                                    </ul>
                                </div>
                                <p id="permit-note" style="color: red; font-size: smaller; display: none;">
                                    Please select at least one proof document.
                                </p>
                                <p style="font-size:smaller;">Insert your proof documents below.</p>
                                <div class="proofImages">
                                    <div class="image-preview-container" id="proof-previews">
                                        <div class="main-image">
                                            <img id="main-proof-preview" src="" alt="Main Image Preview">
                                        </div>
                                        <div class="thumbnail-images proof"></div>
                                    </div>
                                    <input type="file" id="proof-images" name="proof-images[]" multiple accept="image/*"
                                        required>
                                    <p id="proof-image-error" style="color: red; display: none;">
                                        You can upload a maximum of 0 images.
                                    </p>
                                </div>
                                <div class="step-buttons">
                                    <button type="button" class="prev-btn">Previous</button>
                                    <input type="submit" value="Submit" id="submitbtn">
                                </div>
                            </div>
                        </form>
                        <?php
                        break;
                    case 'Pending':
                        ?>
                        <div class="announcement-card">
                            <div class="announcement-header">
                                <p>Pending Upgrade</p>
                            </div>

                            <div class="status-indicator">
                                <img src="php/pending.png" alt="Waiting" class="waiting-image">
                                <p class="approval">
                                    Awaiting Approval
                                </p>
                            </div>

                            <p class="announcement-subtext">
                                Your request for a resort owner account upgrade is currently pending. Please wait for admin
                                approval.
                            </p>
                        </div>
                        <?php
                        break;
                    case 'Confirmed': ?>
                        <div class="approve-card">
                            <div class="header-text">
                                <p>Your registration has been approved!</p>
                            </div>

                            <div class="status-label">
                                <img src="assets/approved.png" alt="Approved" class="status-image">
                                <p class="approved-status">
                                    Approved
                                </p>
                            </div>

                            <p class="description-text">
                                Are you ready to take the next step and become a tourist attraction owner? <br>Click the button
                                below to proceed.
                            </p>

                            <a href="php/updateUserStatus.php" class="cta-button">Click here!</a>
                        </div>
                        <?php
                        break;
                    case 'Rejected':
                        ?>
                        <div class="rejection-card">
                            <div class="rejection-header">
                                <p>Your registration has been rejected!</p>
                            </div>

                            <div class="status-indicator">
                                <img src="assets/rejected.png" alt="Rejected" class="rejection-image">
                                <p class="rejection">
                                    Rejected
                                </p>
                            </div>

                            <p class="rejection-subtext">
                                Unfortunately, your request to become a tourist attraction owner has been rejected.
                                Please review the requirements and try again in 7days.
                            </p>

                        </div>
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
        <script src="assets/js/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="assets/js/purok.js"></script><!-- Include Turf.js library -->
        <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6.5.0"></script>

        <script>
            $(document).ready(function () {
                const $barangaySelect = $('#barangay');
                const $purokSelect = $('#purok');
                const $steps = $(".step");
                const $progressBars = $(".progress");
                const $nextBtns = $(".next-btn");
                const $prevBtns = $(".prev-btn");
                const $mapboxModal = $("#mapboxModal");
                const $btnSetLocation = $("#resortLoc");
                const $closeMapBtn = $(".close-map");
                const $form = $("#resortOwnerForm");

                let currentStep = 0;
                let marker;

                // Barangay select event
                $barangaySelect.on('change', function () {
                    const barangay = $(this).val();
                    $purokSelect.html('<option value="none" selected disabled hidden>Select a Purok</option>');

                    if (puroksByBarangay[barangay]) {
                        puroksByBarangay[barangay].forEach(purok => {
                            $purokSelect.append(new Option(purok, purok));
                        });
                    }
                });

                // Step navigation
                $nextBtns.on("click", () => handleStepChange(1));
                $prevBtns.on("click", () => handleStepChange(-1));


                function handleStepChange(direction) {
                    if (direction === 1 && !validateStep(currentStep)) return;
                    currentStep += direction;
                    showStep(currentStep);
                }

                function showStep(stepIndex) {
                    $steps.each(function (index) {
                        $(this).toggleClass("active", index === stepIndex);
                    });

                    $progressBars.each(function (index) {
                        $(this).toggleClass("active", index <= stepIndex);
                    });
                }

                function validateStep() {
                    const $inputs = $steps.eq(currentStep).find("input[required], select[required], textarea[required]");
                    let valid = true;

                    $inputs.each(function () {
                        if (!$(this).val() || $(this).val() === "none") {
                            valid = false;
                            $(this).css("borderColor", "red");
                        } else {
                            $(this).css("borderColor", "");
                        }
                    });

                    return valid;
                }
                mapboxgl.accessToken = 'pk.eyJ1Ijoibmlrb2xhaTEyMjIiLCJhIjoiY20xemJ6NG9hMDRxdzJqc2NqZ3k5bWNlNiJ9.tAsio6eF8LqzAkTEcPLuSw';
                const map = new mapboxgl.Map({
                    container: "map",
                    style: "mapbox://styles/mapbox/streets-v11",
                    center: [122.9413, 10.4998],
                    zoom: 10.2,
                    attributionControl: false
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
                    $.getJSON(url, function (data) {
                        if (data.features && data.features.length > 0) {
                            const placeName = data.features[0]?.place_name || "Location not found";
                            $("#resortLocation").val(placeName);
                            // Check if the place name contains "Bago"
                            if (placeName.toLowerCase().includes("bago") && placeName.toLowerCase().includes("city")) {
                                handleValidLocation(lng, lat, placeName); // Handle valid Bago City location
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Location Outside Bago City',
                                    text: 'Please select a location within Bago City.',
                                    timer: 3000,
                                    showConfirmButton: true,
                                });
                            }
                        } else {
                            console.error("No location found in reverse geocoding.");
                        }
                    }).fail(function (err) {
                        console.error("Error in reverse geocoding:", err);
                    });
                }

                function handleValidLocation(lng, lat, placeName) {
                    // Handle valid Bago City location (e.g., place marker, update form)
                    if (marker) {
                        marker.setLngLat([lng, lat]);
                    } else {
                        marker = new mapboxgl.Marker().setLngLat([lng, lat]).addTo(map);
                    }
                    $("#tour-longitude").val(lng);
                    $("#tour-latitude").val(lat);
                    $mapboxModal.removeClass('active');
                }

                map.on("click", function (e) {
                    const lngLat = e.lngLat;
                    reverseGeocode(lngLat.lng, lngLat.lat); // Perform reverse geocoding when clicking on the map
                });

                $btnSetLocation.on("click", function () {
                    $mapboxModal.addClass('active');
                    setTimeout(() => {
                        map.resize();
                        map.flyTo({
                            center: [122.9413, 10.4998],
                            essential: true,
                        });
                    }, 200);
                });

                $closeMapBtn.on("click", function () {
                    $mapboxModal.removeClass('active');
                });

                $(window).on("click", function (event) {
                    if ($(event.target).is($mapboxModal)) {
                        $mapboxModal.removeClass('active');
                    }
                });


                function handleFormSubmit(e) {
                    e.preventDefault();

                    // Get the form and the submit button
                    const formData = new FormData($form[0]);
                    const $submitBtn = $form.find("#submitbtn");

                    // Change button text to "Processing..." and disable it
                    $submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...')
                        .prop('disabled', true);

                    // Perform AJAX request
                    $.ajax({
                        url: "php/register_owner.php",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            handleAjaxSuccess(response);
                        },
                        error: function (xhr, status, error) {
                            handleAjaxError(xhr, status, error);
                        },
                        complete: function () {
                            $submitBtn.html("Submit").prop('disabled', false);
                        }
                    });
                }

                function handleAjaxSuccess(response) {
                    console.log("Registration Response:", response);
                    try {
                        const data = JSON.parse(response);

                        Swal.fire({
                            icon: data.success ? 'success' : 'error',
                            title: data.success ? 'Success' : 'Error',
                            text: data.success ? data.message : data.errors,
                            showConfirmButton: true, // Show the confirm button
                        }).then(() => {
                            location.reload(); // Reload immediately after clicking "OK"
                        });


                    } catch (error) {
                        console.error("Response Parsing Error:", error);
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Invalid response format from the server.",
                            timer: 3000,
                            showConfirmButton: false,
                        });
                    }
                }

                function handleAjaxError(xhr, status, error) {
                    console.error("AJAX Error:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "An error occurred while processing your request.",
                        timer: 3000,
                        showConfirmButton: false,
                    });
                }

                // Attach the event listener to the form
                $form.on("submit", handleFormSubmit);


                // Image preview
                const maxTouristImageLimit = 5;

                // Function to update checked count dynamically for permits
                function updateCheckedCount() {
                    return $('.permit input[type="checkbox"]:checked').length;
                }

                // Handle file input changes for tourist images
                $('#tour-images').on('change', function (event) {
                    const $fileInput = $(this);
                    const files = event.target.files;

                    // Check if the number of files exceeds the limit
                    if (files.length > maxTouristImageLimit) {
                        $('#tour-image-error').text('You can upload a maximum of 5 images.').show();
                        $fileInput.val(''); // Clear the input
                        return;
                    } else if (files.length === 0) {
                        $('.image-preview-container').hide();
                    }

                    $('#tour-image-error').hide(); // Hide error if the limit is satisfied

                    const $imagesPreview = $('.image-preview-container');
                    const $mainImagePreview = $('#main-image-preview');
                    const $thumbnailContainer = $('.tour');

                    $imagesPreview.show();
                    $thumbnailContainer.empty();
                    $mainImagePreview.attr('src', '');

                    $.each(files, function (index, file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const image = new Image();
                            image.onload = function () {
                                const aspectRatio = image.width / image.height;

                                // Validate aspect ratio (16:10 is approximately 1.6)
                                if (aspectRatio < 1.3) {  // Adjusted for 16:10 (aspect ratio > 1.3)
                                    $('#tour-image-error').text(`Image ${index + 1} must have a landscape view (16:10 aspect ratio recommended).`).show();
                                    $fileInput.val(''); // Clear the input
                                    $imagesPreview.hide();
                                    return;
                                }

                                const $img = $('<img>', {
                                    src: e.target.result,
                                    alt: `Image ${index + 1}`,
                                });

                                $img.on('click', function () {
                                    $mainImagePreview.attr('src', e.target.result);
                                    $('.thumbnail-images img').removeClass('selected');
                                    $img.addClass('selected');
                                });

                                // Append to thumbnail container
                                $thumbnailContainer.append($img);

                                // Set the first image as the main image
                                if (index === 0) {
                                    $mainImagePreview.attr('src', e.target.result);
                                    $img.addClass('selected');
                                }
                            };

                            image.src = e.target.result; // Set the image source for validation
                        };
                        reader.readAsDataURL(file);
                    });
                });

                // Handle file input changes for proof images
                $('#proof-images').on('change', function (event) {
                    const $fileInput = $(this);
                    const files = event.target.files;
                    const $imagesPreview = $('#proof-previews');
                    const $mainImagePreview = $('#main-proof-preview');
                    const $proofContainer = $('.proof');

                    const checkedCount = updateCheckedCount();  // Recalculate checked count

                    // Check if the number of files exceeds the limit based on checkedCount
                    if (files.length > checkedCount || files.length < checkedCount) {
                        $('#proof-image-error').text(`Upload ${checkedCount} images to complete the proof application.`).show();
                        $imagesPreview.hide();
                        $fileInput.val('');
                        return;
                    } else if (files.length === 0) {
                        $imagesPreview.hide();
                    }

                    $('#proof-image-error').hide(); // Hide error if the limit is satisfied

                    // Show preview container
                    $proofContainer.empty();
                    $mainImagePreview.attr('src', '');

                    $.each(files, function (index, file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const image = new Image();
                            image.onload = function () {
                                const $img = $('<img>', {
                                    src: e.target.result,
                                    alt: `Image ${index + 1}`,
                                });

                                // Click on thumbnail to set as the main image
                                $img.on('click', function () {
                                    $mainImagePreview.attr('src', e.target.result);
                                    $('.thumbnail-proofs img').removeClass('selected');
                                    $img.addClass('selected');
                                });

                                // Append to thumbnail container
                                $proofContainer.append($img);

                                // Set the first image as the main image
                                if (index === 0) {
                                    $mainImagePreview.attr('src', e.target.result);
                                    $img.addClass('selected');
                                }
                            };

                            image.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    });

                    $imagesPreview.show();
                });

            });

        </script>
    </body>

</html>