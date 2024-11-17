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
    <link rel="stylesheet" href="assets/css/form.css">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet" />
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
                            <p style="font-size:smaller;">Insert your tour image below.</p>
                            <p id="image-error" style="color: red; display: none;">You can upload a maximum of 5 images.</p>
                            <p id="image-error" style="color: red; display: none;">Image must have a landscape view (16:10
                                aspect ratio recommended).
                            </p>
                            <div id="upload-notes"
                                style="background-color: #f9f9f9; border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; font-size: 14px; color: #555;">
                                <strong>Notes for Uploading Tour Images:</strong>
                                <ul style="margin: 0; padding-left: 20px;">
                                    <li>Upload a maximum of <strong>5 images</strong>.</li>
                                    <li>Images should have a <strong>landscape view</strong> (16:10 aspect ratio recommended).
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
                                    <div class="thumbnail-images">
                                        <!-- Thumbnails will be displayed here -->
                                    </div>
                                </div>
                                <input type="file" id="tour-images" name="tour-images[]" accept="image/*" multiple>
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
                                        <input type="checkbox" id="permit3" name="proof_permits[]" value="Environmental Compliance Certificate (ECC)">
                                        Environmental Compliance Certificate (ECC)
                                    </label>
                                </div>
                                <div class="permit">
                                    <label for="permit4">
                                        <input type="checkbox" id="permit4" name="proof_permits[]" value="Barangay Clearance">
                                        Barangay Clearance
                                    </label>
                                </div>
                                <div class="permit">
                                    <label for="permit5">
                                        <input type="checkbox" id="permit5" name="proof_permits[]" value="Fire Safety Inspection Certificate">
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
                                    </li>
                                </ul>
                            </div>
                            <p style="font-size:smaller;">Insert your proof documents below.</p>
                            <input type="file" id="fileInput" name="proofImage[]" multiple accept="image/*" required>

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
    <script src="https//code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/purok.js"></script>
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
            $form.on("submit", handleFormSubmit);

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

            // Map setup
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
                $.getJSON(url, function (data) {
                    const placeName = data.features[0]?.place_name || "Location not found";
                    $("#resortLocation").val(placeName);
                }).fail(function (err) {
                    console.error("Error in reverse geocoding:", err);
                });
            }

            map.on("click", function (e) {
                const lngLat = e.lngLat;
                if (marker) {
                    marker.setLngLat(lngLat);
                } else {
                    marker = new mapboxgl.Marker().setLngLat(lngLat).addTo(map);
                }
                reverseGeocode(lngLat.lng, lngLat.lat);
                $("#tour-longitude").val(lngLat.lng);
                $("#tour-latitude").val(lngLat.lat);
                $mapboxModal.removeClass('active');
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

            // Form submission
            function handleFormSubmit(e) {
                e.preventDefault();
                const formData = new FormData($form[0]);

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
                const data = JSON.parse(response);

                Swal.fire({
                    icon: data.success ? 'success' : 'error',
                    title: data.success ? 'Success' : 'Error',
                    text: data.success ? data.message : data.errors,
                    timer: 3000,
                    showConfirmButton: false,
                }).then(() => {
                    if (data.success) {
                        window.location.href = "../user/form?status=success";
                    }
                });
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

            // Image preview
            $('#tour-images').on('change', function (event) {
                const $fileInput = $(this);
                const files = event.target.files;

                // Check if the number of files exceeds the limit
                if (files.length > 5) {
                    $('#image-error').text('You can upload a maximum of 5 images.').show();
                    $fileInput.val(''); // Clear the input
                    return;
                } else if (files.length == 0) {
                    $imagesPreview.hide();
                }

                $('#image-error').hide(); // Hide error if the limit is satisfied

                const $imagesPreview = $('.image-preview-container');
                const $mainImagePreview = $('#main-image-preview');
                const $thumbnailContainer = $('.thumbnail-images');

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
                            if (aspectRatio < 1.3) {
                                $('#image-error').text(`Image ${index + 1} must have a landscape view (16:10 aspect ratio recommended).`).show();
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

                            $thumbnailContainer.append($img);

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
        });

    </script>
</body>

</html>