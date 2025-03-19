<?php
session_start();
include 'include/db_conn.php';
$tour_id = $_GET['tour_id'] ?? null;
$user_id = $_GET['user_id'] ?? null;
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


            if ($tour_id && $user_id) {
                // Fetch existing data
                $stmt = $conn->prepare("SELECT * FROM tours WHERE id = :tour_id AND user_id = :user_id");
                $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $tour = $stmt->fetch(PDO::FETCH_ASSOC);

                $tourImages = explode(',', $tour['img']);
                $proofImages = explode(',', $tour['proof_image']);
                $addressParts = explode(',', $tour['address']);

                if (count($addressParts) >= 2) {
                    $purok = trim($addressParts[0]);  // First part (Purok)
                    $barangay = trim($addressParts[1]);  // Second part (Barangay)
                } else {
                    $purok = '';
                    $barangay = '';
                }

                if ($tour) {
                    ?>
                    <div id="existing-images" data-images='<?= json_encode($tourImages); ?>'></div>
                    <div id="proofExisting-images" data-images='<?= json_encode($proofImages); ?>'></div>
                    <form id="resortOwnerForm" enctype="multipart/form-data">
                        <input type="hidden" name="tour_id" value="<?= $tour['id']; ?>">
                        <input type="hidden" name="user_id" value="<?= $tour['user_id']; ?>">

                        <!-- Step 1: Tourist Attraction Details -->
                        <div class="progress-container">
                            <div class="progress active"></div>
                            <div class="progress"></div>
                            <div class="progress"></div>
                            <div class="progress"></div>
                        </div>

                        <div class="step active" id="step1">
                            <h2>Tourist Attraction Details</h2>
                            <label for="resortName">Tourist Attraction Name:</label>
                            <input type="text" id="resortName" name="title" value="<?= $tour['title']; ?>" required>

                            <label for="type">Type of Attraction:</label>
                            <select name="type" id="type" required>
                                <option value="none" disabled hidden>Select an Option</option>
                                <?php
                                $types = ["Beach Resort", "Campsite", "Falls", "Historical Landmark", "Mountain Resort", "Park", "Swimming Pool"];
                                foreach ($types as $type) {
                                    $selected = ($tour['type'] == $type) ? "selected" : "";
                                    echo "<option value='$type' $selected>$type</option>";
                                }
                                ?>
                            </select>

                            <label for="bookable">Is booking available?</label>
                            <div class="radio-group" id="bookable">
                                <div class="radio-input">
                                    <input type="radio" id="bookableYes" name="bookable" value="1" <?= $tour['bookable'] == 1 ? 'checked' : ''; ?> required>
                                    <label for="bookableYes">Yes</label>
                                </div>
                                <div class="radio-input">
                                    <input type="radio" id="bookableNo" name="bookable" value="0" <?= $tour['bookable'] == 0 ? 'checked' : ''; ?> required>
                                    <label for="bookableNo">No</label>
                                </div>
                            </div>

                            <label for="description">Description:</label>
                            <textarea id="description" name="description" rows="4"
                                required><?= htmlspecialchars($tour['description']); ?></textarea>

                            <div class="step-buttons" style="float:right">
                                <button type="button" class="next-btn">Next</button>
                            </div>
                        </div>

                        <!-- Step 2: Tourist Attraction Image -->
                        <div class="step" id="step2">
                            <h2>Tourist Attraction Image</h2>
                            <p style="font-size:smaller;">Insert your tour image below.</p>
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

                                    </div>
                                </div>
                                <input type="file" id="tour-images" name="tour-images[]" accept="image/*" multiple>
                            </div>
                            <div class="step-buttons">
                                <button type="button" class="prev-btn">Previous</button>
                                <button type="button" class="next-btn">Next</button>
                            </div>
                        </div>

                        <!-- Step 3: Location Details -->
                        <div class="step" id="step3">
                            <h2>Location Details</h2>
                            <label for="resortLocation">Location:</label>
                            <input type="text" id="resortLocation" name="address"
                                value="<?= htmlspecialchars($tour['address']); ?>" readonly required>
                            <div>

                                <button id="resortLoc" type="button"><i class="fa fa-map-marker"></i></button>
                                <input type="hidden" id="tour-latitude" name="latitude" value="<?= $tour['latitude']; ?>">
                                <input type="hidden" id="tour-longitude" name="longitude" value="<?= $tour['longitude']; ?>">
                            </div>
                            <div class="step-buttons" style="clear:both;">
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
                        <!-- Step 4: Proof of Permits -->
                        <div class="step" id="step4">
                            <h2>Proof of Permits</h2>
                            <p id="permit-note" style="color: red; font-size: smaller; display: none;">
                                You can select a maximum of 3 proof documents.
                            </p>
                            <div class="permit-container">
                                <div class="permit">
                                    <label for="permit1">
                                        <input type="checkbox" id="permit1" name="proof_permits[]" value="Building Permit"
                                            checked disabled>
                                        Building Permit
                                    </label>
                                </div>
                                <div class="permit">
                                    <label for="permit2">
                                        <input type="checkbox" id="permit2" name="proof_permits[]" value="Business Permit"
                                            checked disabled>
                                        Business Permit
                                    </label>
                                </div>
                                <div class="permit">
                                    <label for="permit3">
                                        <input type="checkbox" id="permit3" name="proof_permits[]"
                                            value="Environmental Compliance Certificate (ECC)" checked disabled>
                                        Environmental Compliance Certificate (ECC)
                                    </label>
                                </div>
                                <div class="permit">
                                    <label for="permit4">
                                        <input type="checkbox" id="permit4" name="proof_permits[]" value="Barangay Clearance"
                                            checked disabled>
                                        Barangay Clearance
                                    </label>
                                </div>
                                <div class="permit">
                                    <label for="permit5">
                                        <input type="checkbox" id="permit5" name="proof_permits[]"
                                            value="Fire Safety Inspection Certificate" checked disabled>
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
                                <input type="file" id="proof-images" name="proof-images[]" multiple accept="image/*">
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
                } else {
                    echo "<p>Tour data not found.</p>";
                }
            } else {
                echo "<p>Invalid request.</p>";
            }
            ?>

        </div>
    </div>
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/purok.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6.5.0"></script>

    <script>
        $(document).ready(function () {

            const existingImages = $('#existing-images').data('images');
            const proofImages = $('#proofExisting-images').data('images');
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

            const maxTouristImageLimit = 5;
            const $fileInput = $('#tour-images');
            const $imagesPreview = $('.image-preview-container');
            const $mainImagePreview = $('#main-image-preview');
            const $thumbnailContainer = $('.thumbnail-images.tour');

            let currentStep = 0;
            let marker;


            // Pre-select the barangay if it was already selected
            const selectedBarangay = "<?= $barangay ?>"; // PHP variable
            const selectedPurok = "<?= $purok ?>"; // PHP variable

            // Set the barangay select value
            $barangaySelect.val(selectedBarangay);

            // Trigger the change event to populate the purok dropdown with options
            $barangaySelect.trigger('change');

            // Function to update the purok options based on selected barangay
            $barangaySelect.on('change', function () {
                const barangay = $(this).val();
                $purokSelect.html('<option value="none" selected disabled hidden>Select a Purok</option>');

                if (puroksByBarangay[barangay]) {
                    puroksByBarangay[barangay].forEach(function (purok) {
                        const $option = new Option(purok, purok); // Create option element
                        $purokSelect.append($option);

                        // Pre-select the purok if it matches
                        if (purok === selectedPurok) {
                            $option.prop('selected', true);
                        }
                    });
                }
            });

            // Step navigation
            $nextBtns.on("click", () => handleStepChange(1));
            $prevBtns.on("click", () => handleStepChange(-1));

            // Initialize Mapbox
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

            // Handle file input change for tour images
            $fileInput.on('change', function (event) {
                const files = event.target.files;

                // Ensure the total number of files doesn't exceed the max limit
                const totalFiles = existingImages.length + files.length;
                if (totalFiles > maxTouristImageLimit) {
                    $('#tour-image-error').text('You can upload a maximum of 5 images.').show();
                    $fileInput.val(''); // Clear the input
                    return;
                }

                $('#tour-image-error').hide(); // Hide error message

                // Clear existing thumbnails for new images
                $thumbnailContainer.empty();

                // Handle each file
                $.each(files, function (index, file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const image = new Image();
                        image.onload = function () {
                            const aspectRatio = image.width / image.height;

                            // Validate aspect ratio (16:10 is approximately 1.6)
                            if (aspectRatio < 1.3) {
                                $('#tour-image-error').text(`Image ${index + 1} must have a landscape view (16:10 aspect ratio recommended).`).show();
                                $fileInput.val(''); // Clear the input
                                return;
                            }

                            const $img = $('<img>', {
                                src: e.target.result,
                                alt: `Uploaded Image ${index + 1}`,
                                class: 'image-thumb',
                            });

                            // Set the first uploaded image as the main image
                            if (index === 0 && !existingImages.length) {
                                $mainImagePreview.attr('src', e.target.result);
                                $img.addClass('selected');
                            }

                            // Handle click to update main image preview
                            $img.on('click', function () {
                                $mainImagePreview.attr('src', e.target.result);
                                $('.thumbnail-images img').removeClass('selected');
                                $img.addClass('selected');
                            });

                            // Append to thumbnail container
                            $thumbnailContainer.append($img);
                        };
                        image.src = e.target.result; // Validate and load the image
                    };
                    reader.readAsDataURL(file); // Read the file as Data URL
                });
            });

            // Existing images preview
            if (existingImages.length) {
                $imagesPreview.show();
                $thumbnailContainer.empty();
                existingImages.forEach((image, index) => {
                    const $img = $('<img>', {
                        src: `upload/Tour Images/` + image,
                        alt: `Existing Image ${index + 1}`,
                        class: 'image-thumb',
                    });

                    // Select the first image as the main image
                    if (index === 0) {
                        $mainImagePreview.attr('src', `upload/Tour Images/` + image);
                        $img.addClass('selected'); // Highlight the selected image
                    }

                    // Click to select an image as the main preview
                    $img.on('click', function () {
                        $mainImagePreview.attr('src', image);
                        $('.thumbnail-images img').removeClass('selected');
                        $img.addClass('selected');
                    });

                    // Append to thumbnail container
                    $thumbnailContainer.append($img);
                });
            }
            $(document).ready(function () {
                const $proofFileInput = $('#proof-images'); // Input for uploading proof images
                const $proofImagesPreview = $('#proof-previews'); // Container for the proof images preview
                const $mainProofPreview = $('#main-proof-preview'); // Main preview image element
                const $proofThumbnailContainer = $('.thumbnail-images.proof'); // Container for proof thumbnails



                // Function to display proof images
                function displayProofImages() {
                    $proofThumbnailContainer.empty(); // Clear the thumbnails container

                    if (proofImages.length > 0) {
                        proofImages.forEach((image, index) => {
                            const imageUrl = `upload/Permits/${image}`; // Adjust path based on your setup
                            const $img = $('<img>', {
                                src: imageUrl,
                                class: 'thumbnail-proof',
                                alt: `Proof Image ${index + 1}`,
                            });

                            // Add the first image as the main preview
                            if (index === 0) {
                                $mainProofPreview.attr('src', imageUrl);
                                $img.addClass('selected');
                            }

                            // Set up click handler for thumbnails
                            $img.on('click', function () {
                                $mainProofPreview.attr('src', imageUrl); // Update main preview
                                $('.thumbnail-proof').removeClass('selected');
                                $img.addClass('selected');
                            });

                            $proofThumbnailContainer.append($img);
                        });
                    } else {
                        // No existing proof images, set a default placeholder
                        $mainProofPreview.attr('src', 'assets/images/default-placeholder.png');
                    }
                }

                // Display existing proof images on load
                displayProofImages();

                // Handle new proof image uploads
                $proofFileInput.on('change', function () {
                    const files = this.files;

                    if (files.length !== 5) {
                        $proofFileInput.val('');
                        alert(`You must upload exactly 5 proof documents.`);
                        return; 
                    }
                    // Clear existing thumbnails
                    $proofThumbnailContainer.empty();

                    // Read and preview uploaded files
                    Array.from(files).forEach((file, index) => {
                        const reader = new FileReader();

                        reader.onload = function (e) {
                            const imageUrl = e.target.result;

                            const $img = $('<img>', {
                                class: 'thumbnail-proof',
                                src: imageUrl,
                                alt: `Uploaded Proof ${index + 1}`,
                            });

                            // Add the first uploaded image as the main preview
                            if (index === 0) {
                                $mainProofPreview.attr('src', imageUrl);
                                $img.addClass('selected');
                            }

                            // Click to update main preview
                            $img.on('click', function () {
                                $mainProofPreview.attr('src', imageUrl);
                                $('.thumbnail-proof').removeClass('selected');
                                $img.addClass('selected');
                            });

                            $proofThumbnailContainer.append($img);
                        };

                        reader.readAsDataURL(file);
                    });
                });
            });

            // Handle form submission
            // Attach the event listener to the form
            $form.on("submit", function (e) {
                e.preventDefault(); // Prevent the default form submission

                // Get the form and the submit button
                const formData = new FormData($form[0]);
                const $submitBtn = $form.find("#submitbtn");

                // Change button text to "Processing..." and disable it
                $submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...')
                    .prop('disabled', true);

                // Perform the AJAX request
                $.ajax({
                    url: "php/update-form.php",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log("Registration Response:", response);
                        try {
                            // Show success or error message based on the response
                            Swal.fire({
                                icon: response.success ? 'success' : 'error',
                                title: response.success ? 'Success' : 'Error',
                                text: response.success ? response.message : response.errors,
                                showConfirmButton: true, // Show the confirm button
                            }).then(() => {
                                location.href = 'form'; // Redirect to the 'form' page
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
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", error);
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "An error occurred while processing your request.",
                            timer: 3000,
                            showConfirmButton: false,
                        });
                    },
                    complete: function () {
                        // Revert button to original state after AJAX completes
                        $submitBtn.html("Submit").prop('disabled', false);
                    }
                });
            });

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

        });
    </script>

</body>

</html>