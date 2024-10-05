<?php
include '../include/db_conn.php';
include '../func/user_func.php';
session_start();
$pageRole = "owner";
require_once '../php/accValidation.php';
$user_id = $_SESSION['user_id'];
$pp = $_SESSION['profile-pic'];
$id = $_SESSION['tour_id'];
$tour = getTourById($conn, $id);
$tourImage = getTourImageById($conn, $id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/owner.css">
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />
    <title>BaGoTours - View Tour</title>
    <style>
        .tour-container {
            width: 100%;
            margin: 25px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 15px;
        }
        .head {
            font-size: larger;
            display: flex;
            justify-content: space-between;
        }
        i {
            cursor: pointer;
        }
        i:hover {
            color: gray !important;
        }

        .tour-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .tour-container img {
            margin: 20px;
            float: left;
            width: 50%;
            border-radius: 10px;
        }

        .tour-container p {
            font-size: 1.2em;
        }

        .tour-container .btn-edit,
        .tour-container .btn-delete {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            text-align: center;
            border-radius: 5px;
            color: white;
            text-decoration: none;
        }

        .tour-container .btn-edit {
            background-color: #007bff;
        }

        .tour-container .btn-delete {
            background-color: #dc3545;
        }
    </style>
</head>

<body>
    <?php include 'includes/sidebar.php'; ?>
    <section id="content">
        <?php include 'includes/navbar.php'; ?>
        <main id="main">
            <div class="head-title">
                <div class="left">
                    <h1>View Tours</h1>
                    <?php include 'includes/breadcrumb.php'; ?>
                </div>
            </div>
            <div class="tour-container">
                <div class="head">
                    <h1><?php echo htmlspecialchars($tour['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                    <i class='bx bx-edit-alt' onclick="loadDoc()"></i>
                </div>
                <div class="detail-container">
                    <?php if (!empty($tour)) { ?>
                        <img src="../upload/Tour Images/<?php echo htmlspecialchars($tour['img'], ENT_QUOTES, 'UTF-8'); ?>" alt="Tour Image">
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($tour['address'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><strong>Type:</strong> <?php echo htmlspecialchars($tour['type'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($tour['description'], ENT_QUOTES, 'UTF-8')); ?></p>
                        <p><strong>Status:</strong> <?php echo $tour['status'] == 1 ? 'Active' : 'Inactive'; ?></p>
                    <?php } else { ?>
                        <p>Tour not found.</p>
                    <?php } ?>
                </div>
                <div id="tour-images">
                    <h2>Images</h2>
                    <div class="row">
                        <?php if (!empty($tourImage)) {
                            foreach ($tourImage as $img) { ?>
                                <div class="col-md-3">
                                    <img src="../upload/Tour Images/<?php echo htmlspecialchars($img['img'], ENT_QUOTES, 'UTF-8'); ?>" alt="Tour Image" style="width: 100%; border-radius: 10px;">
                                </div>
                            <?php }
                        } else { ?>
                            <p>No images found.</p>
                        <?php } ?>
                    </div>
                </div>
                <div id="map" style="height: 400px; width: 100%; margin-top: 20px;"></div>
                <button class="btn-edit" type="button" onclick="loadDoc()">Edit</button>
            </div>
            </div>
        </main>
    </section>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function loadDoc() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("main").innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "edit_tour.php", true);
            xhttp.send();
        }

        document.addEventListener('DOMContentLoaded', () => {
            mapboxgl.accessToken = 'pk.eyJ1Ijoibmlrb2xhaTEyMjIiLCJhIjoiY2x6d3pva281MGx6ODJrczJhaTJ4M2RmYyJ9.0sJ2ZGR2xpEza2j370y3rQ';

            const map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/streets-v12',
                center: [<?php echo htmlspecialchars($tour['longitude']); ?>, <?php echo htmlspecialchars($tour['latitude']); ?>],
                zoom: 15,
                interactive: false
            });

            const markerElement = document.createElement('div');
            markerElement.className = 'marker';
            markerElement.style.backgroundImage = 'url(../assets/icons/<?php echo htmlspecialchars(strtok($tour['type'], " ")); ?>.png)';
            markerElement.style.backgroundSize = 'contain';
            markerElement.style.width = '30px';
            markerElement.style.height = '30px';

            const marker = new mapboxgl.Marker(markerElement)
                .setLngLat([<?php echo htmlspecialchars($tour['longitude']); ?>, <?php echo htmlspecialchars($tour['latitude']); ?>])
                .addTo(map);

            map.dragPan.disable();
            map.scrollZoom.disable();
            map.touchZoomRotate.disable();
            map.rotate.disable();
        });
    </script>
</body>

</html>