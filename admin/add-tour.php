<?php
include '../include/db_conn.php';
session_start();

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
    $type = htmlspecialchars($_POST['type'], ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($_POST['location'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $bookable = isset($_POST['bookable']) ? $_POST['bookable'] : 0;
    if (tourAlreadyExists($conn, $title)) {
        $errorMessage = "Tour already exist, please fill up a new tour.";
    } else {
        $uploaded_images = [];
        if (isset($_FILES['tour-images']) && !empty($_FILES['tour-images']['name'][0])) {
            $images = $_FILES['tour-images'];
            $image_error = false;

            foreach ($images['name'] as $key => $image_name) {
                $image_tmp_name = $images['tmp_name'][$key];
                $image_type = $images['type'][$key];
                $image_size = $images['size'][$key];
                $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);

                // Validate image (Check for allowed types and size)
                if (!in_array($image_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $image_error = true;
                    $error_message = "Invalid image type. Only jpg, jpeg, png, and gif are allowed.";
                    break;
                }
                if ($image_size > 5000000) { // Max size of 5MB
                    $image_error = true;
                    $error_message = "Image size exceeds 5MB.";
                    break;
                }

                // Generate a unique name for the image
                $new_image_name = uniqid() . '.' . $image_ext;
                $target_path = "../upload/Tour Images/" . $new_image_name;

                if (move_uploaded_file($image_tmp_name, $target_path)) {
                    $uploaded_images[] = $new_image_name; // Store the image name for DB insertion
                } else {
                    $image_error = true;
                    $error_message = "Error uploading image.";
                    break;
                }
            }

            if ($image_error) {
                echo '<script>alert("' . $error_message . '");</script>';
            }
        }

        // If no errors, insert the tour into the database
        if (!$image_error) {
            // Convert the array of uploaded images to a comma-separated string for the database
            $image_paths = implode(',', $uploaded_images);

            // Insert tour details into the database
            $query = "INSERT INTO tours (title, type, description, address, latitude, longitude, img, bookable, status, user_id) 
                  VALUES (:title, :type, :description, :address, :latitude, :longitude, :img, :bookable, 1, :user_id)";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':latitude', $latitude);
            $stmt->bindParam(':longitude', $longitude);
            $stmt->bindParam(':img', $image_paths);
            $stmt->bindParam(':bookable', $bookable);
            $stmt->bindParam(':user_id', $user_id);

            if ($stmt->execute()) {
                $successMessage = "Tour added successfully!";
            } else {
                $errorMessage = "Failed to add the tour. Please try again.";
            }
        }
    }
}

function tourAlreadyExists($conn, $title)
{
    $query = "SELECT COUNT(*) as count FROM tours WHERE title = :title AND status = 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/add.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Mapbox -->
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />
    <title>BaGoTours || Add Tour</title>
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
                    <div class="title">

                        <h2>Add New Tour</h2>
                        <p>Fill out the details below to create a new tour. Ensure all information is accurate,
                            especially the images and location, to provide the best experience for visitors.</p>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="section-header">
                            <hr class="section-divider">
                            <h3 class="section-title">Tour Information</h3>
                            <hr class="section-divider">
                        </div>
                        <label for="image-preview-container">Tour Image <span>required</span></label>
                        <div class="image-preview-container">
                            <div class="main-image">
                                <img id="main-image-preview" src="" alt="Main Image Preview">
                            </div>
                            <div class="thumbnail-images">
                                <!-- Thumbnails will be displayed here -->
                            </div>
                        </div>
                        <input type="file" id="tour-images" name="tour-images[]" accept="image/*" multiple>
                        <p id="image-error" style="color: red; display: none;">Image must have a landscape view (16:10
                            aspect ratio recommended).
                        </p>



                        <div class="form-group">
                            <div class="input-group" style="width:65%">
                                <label for="title">Tours Name <span>required</span></label>
                                <input type="text" id="title" name="title" required>
                            </div>
                            <div class="input-group" style="width:35%">
                                <label for="type">Tour Type <span>required</span></label>
                                <input type="text" id="type" name="type" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <label for="location">Tour Location <span>required</span></label>
                                <input type="text" id="location" name="location" onclick="openMap()" required readonly>
                            </div>
                            <div class="input-group">
                                <label for="bookable">Bookable <span>required</span></label>
                                <div class="radio-group">
                                    <div class="radio">
                                        <input type="radio" id="bookable-yes" name="bookable" valisue="1">
                                        <label for="bookable-yes">Yes</label>
                                    </div>
                                    <div class="radio">
                                        <input type="radio" id="bookable-no" name="bookable" value="0">
                                        <label for="bookable-no">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <label for="description">Tour Description <span>required</span></label>
                        <textarea id="description" name="description" rows="4" required></textarea>

                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">
                        <button type="submit" class="btn-submit">Add Tour</button>
                    </form>
                </div>
            </div>
            <div id="mapModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeMap()">&times;</span>
                    <h2>Select Tourist Spot Location</h2>
                    <div id="map"></div>
                    <button id="confirm-location" class="btn">Confirm Location</button>
                </div>
            </div>
        </main>
    </section>



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
                        document.getElementById("location").value = placeName;
                    })
                    .catch((err) => console.error("Error in reverse geocoding: ", err));
            }

            $('#tour-images').on('change', function (event) {
                const files = event.target.files;
                const $imagesPreview = $('.image-preview-container');
                const $mainImagePreview = $('#main-image-preview');
                const $thumbnailContainer = $('.thumbnail-images');

                // Clear existing image previews and thumbnails
                $imagesPreview.toggle();
                $thumbnailContainer.empty();
                $mainImagePreview.attr('src', '');

                $.each(files, function (index, file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
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
                    reader.readAsDataURL(file);
                });
            });
        });
    </script>
</body>

</html>