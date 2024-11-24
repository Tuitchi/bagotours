<?php
include '../include/db_conn.php';
session_start();


$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../func/func.php';

    $codestmt = $conn->prepare("SELECT event_code FROM events ORDER BY created_at DESC LIMIT 1");
    $codestmt->execute();
    $last_event_code = $codestmt->fetchColumn();
    if ($last_event_code === false) {
        $last_event_code = 202411040001;
    }
    $event_name = $_POST['event_name'];
    if (!addEventValidator($conn, $event_name)) {
        try {
            $event_type = $_POST['event_type'];
            $tags = $_POST['tags'];
            $event_description = $_POST['event_description'];
            $event_date_start = $_POST['event_date_start'];
            $event_date_end = $_POST['event_date_end'];
            $registration_deadline = $_POST['registration_deadline'] ?? null; // Optional
            $event_location = $_POST['event_location'];
            $organizer_name = $_POST['organizer_name'] ?? null; // Optional
            $organizer_contact = $_POST['organizer_contact'] ?? null; // Optional
            $sponsor = $_POST['sponsor'] ?? null; // Optional
            $latitude = $_POST['latitude'];
            $longitude = $_POST['longitude'];
            $event_code = $last_event_code + 1;

            if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['event_image']['tmp_name'];
                $fileName = $_FILES['event_image']['name'];
                $fileSize = $_FILES['event_image']['size'];
                $fileType = $_FILES['event_image']['type'];
                $fileNameParts = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameParts));

                // Specify the path where the image will be stored
                $uploadFileDir = '../upload/Event/';
                $newFileName = uniqid('', true) . '.' . $fileExtension;
                $dest_path = $uploadFileDir . $newFileName;

                // Move the file to the specified directory
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $event_image = $newFileName; // Set the event_image variable

                    // Prepare the SQL statement
                    $stmt = $conn->prepare("INSERT INTO events (event_code, event_image, event_name, event_type, tags, event_description, 
                        event_date_start, event_date_end, registration_deadline, event_location, organizer_name, 
                        organizer_contact, sponsor, latitude, longitude) 
                        VALUES (:event_code, :event_image, :event_name, :event_type, :tags, :event_description, 
                        :event_date_start, :event_date_end, :registration_deadline, :event_location, :organizer_name, 
                        :organizer_contact, :sponsor, :latitude, :longitude)");

                    // Bind parameters
                    $stmt->bindParam(':event_code', $event_code);
                    $stmt->bindParam(':event_image', $event_image);
                    $stmt->bindParam(':event_name', $event_name);
                    $stmt->bindParam(':event_type', $event_type);
                    $stmt->bindParam(':tags', $tags);
                    $stmt->bindParam(':event_description', $event_description);
                    $stmt->bindParam(':event_date_start', $event_date_start);
                    $stmt->bindParam(':event_date_end', $event_date_end);
                    $stmt->bindParam(':registration_deadline', $registration_deadline);
                    $stmt->bindParam(':event_location', $event_location);
                    $stmt->bindParam(':organizer_name', $organizer_name);
                    $stmt->bindParam(':organizer_contact', $organizer_contact);
                    $stmt->bindParam(':sponsor', $sponsor);
                    $stmt->bindParam(':latitude', $latitude);
                    $stmt->bindParam(':longitude', $longitude);

                    // Execute the statement
                    if ($stmt->execute()) {
                        $successMessage = "Event added successfully.";
                        $notifMessage = "🌟 Heads up, Bago City! A new event is happening soon. See what’s in store and grab your spot! 🏙️🎟️";
                        createNotificationForAllUsers($conn, $event_code, $notifMessage, "view-event?event=" . base64_encode($event_code), "event");
                    } else {
                        $errorMessage = "Error adding event.";
                        error_log("SQL Error: " . implode(", ", $stmt->errorInfo())); // Log SQL error details
                    }
                } else {
                    $errorMessage = "Error on uploading image.";
                }
            } else {
                $errorMessage = "No file uploaded or there was an upload error.";
            }

        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $errorMessage = "An error occurred while processing your request.";
        }
    } else {
        $errorMessage = "Already created this event.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon"
        href="../assets/icons/<?php echo htmlspecialchars($webIcon, ENT_QUOTES, 'UTF-8'); ?>">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/add.css">

    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet" />
    </style>
    <title>BaGoTours || Add Event</title>
</head>

<body>
    <?php include 'includes/sidebar.php'; ?>
    <section id="content">
        <?php include 'includes/navbar.php'; ?>
        <main>
            <div class="head-title">
                <div class="left">
                    <?php include 'includes/breadcrumb.php'; ?>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <h2>Add New Event</h2>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="section-header">
                            <hr class="section-divider">
                            <h3 class="section-title">Event Information</h3>
                            <hr class="section-divider">
                        </div>
                        <label for="event_image">Event Image <span>required</span></label>
                        <div class="image-preview" id="image-preview">
                            <img id="preview-image" src="" alt="Event Image Preview">
                        </div>
                        <input type="file" id="event_image" name="event_image" accept="image/*" max="5" required>
                        <p id="image-error" style="color: red; display: none;">Image must have a landscape view (16:10
                            aspect ratio recommended).
                        </p>

                        <label for="event_name">Event Name <span>required</span></label>
                        <input type="text" id="event_name" name="event_name" required>

                        <div class="form-group">
                            <div class="input-group">
                                <label for="event_type">Event Type <span>required</span></label>
                                <input type="text" id="event_type" name="event_type" required>
                            </div>

                            <div class="input-group">
                                <label for="tags">Tags <span>required</span></label>
                                <input type="text" id="tags" name="tags" placeholder="e.g., outdoor, family-friendly">
                            </div>
                        </div>

                        <label for="event_description">Event Description <span>required</span></label>
                        <textarea id="event_description" name="event_description" rows="4" required></textarea>

                        <div class="form-group">
                            <div class="input-group">
                                <label for="event_date_start">Start Date & Time <span>required</span></label>
                                <input type="datetime-local" id="event_date_start" name="event_date_start" required>
                            </div>
                            <div class="input-group">
                                <label for="event_date_end">End Date & Time <span>required</span></label>
                                <input type="datetime-local" id="event_date_end" name="event_date_end" required>
                            </div>
                            <div class="input-group">
                                <label for="registration_deadline">Registration Deadline <span
                                        class="opt">optional</span></label>
                                <input type="datetime-local" id="registration_deadline" name="registration_deadline">
                            </div>
                        </div>

                        <label for="event_location">Event Location <span>required</span></label>
                        <input type="text" id="event_location" name="event_location" onclick="openMap()" required
                            readonly>

                        <div class="section-header">
                            <hr class="section-divider">
                            <h3 class="section-title">Organizer Information <span class="opt">optional</span></h3>
                            <hr class="section-divider">
                        </div>

                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" id="organizer_name" name="organizer_name"
                                    placeholder="Organizer Name">
                            </div>
                            <div class="input-group">
                                <input type="text" id="organizer_contact" name="organizer_contact"
                                    placeholder="Organizer Contact (email, phone number or etc.)">
                            </div>
                        </div>

                        <label for="sponsor">Sponsor</label>
                        <input type="text" id="sponsor" name="sponsor">

                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">
                        <button type="submit" class="btn-submit">Add Event</button>
                    </form>
                </div>
            </div>
            <div id="mapModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeMap()">&times;</span>
                    <h2>Select Event Location</h2>
                    <div id="map"></div>
                    <button id="confirm-location" class="btn">Confirm Location</button>
                </div>
            </div>
        </main>
    </section>
    <script src="https//code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/script.js"></script>
    <script>
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
        <?php if (!empty($successMessage)): ?>
            Toast.fire({
                icon: "success",
                title: "<?php echo $successMessage ?>"
            });
        <?php elseif (!empty($errorMessage)): ?>
            Toast.fire({
                icon: "error",
                title: "<?php echo $errorMessage ?>"
            });
        <?php endif; ?>
        document.addEventListener('DOMContentLoaded', () => {
            mapboxgl.accessToken = 'pk.eyJ1Ijoibmlrb2xhaTEyMjIiLCJhIjoiY20xemJ6NG9hMDRxdzJqc2NqZ3k5bWNlNiJ9.tAsio6eF8LqzAkTEcPLuSw';

            const mapContainer = 'map';
            let map;
            let marker;
            let lngLat;

            function initializeMap() {
                map = new mapboxgl.Map({
                    container: mapContainer,
                    style: 'mapbox://styles/mapbox/navigation-night-v1',
                    center: [122.8313, 10.5338],
                    zoom: 11
                });

                map.addControl(new mapboxgl.NavigationControl());
                map.addControl(new mapboxgl.GeolocateControl({
                    positionOptions: {
                        enableHighAccuracy: true,
                    },
                    trackUserLocation: true,
                    showUserHeading: true,
                }));

                map.on("click", (e) => {
                    lngLat = e.lngLat;
                    if (marker) {
                        marker.setLngLat(lngLat);
                    } else {
                        marker = new mapboxgl.Marker().setLngLat(lngLat).addTo(map);
                    }
                });
            }

            window.openMap = function () {
                document.getElementById('mapModal').style.display = 'block';
                if (!map) {
                    initializeMap();
                }
            };

            window.closeMap = function () {
                document.getElementById('mapModal').style.display = 'none';
            };

            document.getElementById('confirm-location').addEventListener('click', function () {
                if (lngLat) {
                    reverseGeocode(lngLat.lng, lngLat.lat);
                    document.getElementById("longitude").value = lngLat.lng;
                    document.getElementById("latitude").value = lngLat.lat;
                    closeMap();
                } else {
                    alert("Please select a location on the map before confirming.");
                }
            });

            function reverseGeocode(lng, lat) {
                const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json?access_token=${mapboxgl.accessToken}`;
                fetch(url)
                    .then((response) => response.json())
                    .then((data) => {
                        const placeName = data.features[0]?.place_name || "Location not found";
                        document.getElementById("event_location").value = placeName;
                    })
                    .catch((err) => console.error("Error in reverse geocoding: ", err));
            }

            $('#event_image').on('change', function (event) {
                const $previewContainer = $('#image-preview');
                const $previewImage = $('#preview-image');
                const $imageError = $('#image-error');
                const file = event.target.files[0];

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $previewImage.attr('src', e.target.result);
                        const img = new Image();
                        img.src = e.target.result;

                        img.onload = function () {
                            const aspectRatio = img.width / img.height;
                            if (aspectRatio < 1.3) {
                                $('#event_image').val(''); // Clear the file input
                                $imageError.show();
                                $previewContainer.hide();
                            } else {
                                $imageError.hide();
                                $previewContainer.show();
                            }
                        };
                    };
                    reader.readAsDataURL(file);
                }
            });

        });

        function validateForm() {
            const startDate = new Date(document.getElementById("event_date_start").value);
            const endDate = new Date(document.getElementById("event_date_end").value);

            if (startDate >= endDate) {
                alert("End date must be after the start date.");
                return false;
            }
            return true;
        }
    </script>
</body>

</html>