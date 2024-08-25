<?php
include '../include/db_conn.php';
include '../func/user_func.php';
session_start();

session_regenerate_id();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?action=Invalid");
    exit();
}
$user_id = $_SESSION['user_id'];
$pp = $_SESSION['profile-pic'];

$tour = getAllTours($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <!-- Mapbox -->
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />
    <title>BaGoTours. Tours</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
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

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .btn-submit {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        .btn-submit:hover {
            background-color: #45a049;
        }

        #mapboxModal .modal-content {
            width: 50%;
            height: 80%;
            max-width: 80%;
            max-height: 100%;
        }

        .data {
            display: flex;
            flex-wrap: nowrap;
            gap: 15px;
            align-items: flex-start;
            justify-content: flex-start;
            border-bottom: 1px solid #ccc;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .data .img {
            width: 200px;
            height: 200px;
            overflow: hidden;
            border-radius: 15%;
        }

        .data .img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .data .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .data .content h4 {
            margin: 0 0 10px;
            font-size: 1.5em;
        }

        .data .content p {
            margin: 0 0 5px;
        }

        .data .content .btn-edit,
        .data .content .btn-delete {
            margin-top: 10px;
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        .data .content .btn-delete {
            background-color: #dc3545;
        }

        .content {
            position: relative;
            flex-grow: 1;
            padding-right: 60px;
        }

        .action-buttons {
            font-size: 12px;
            position: absolute;
            bottom: 10px;
            right: 10px;
            display: flex;
            gap: 10px;
        }

        .btn-edit,
        .btn-delete {
            width: 50px;
            height: 30px;
            text-align: center;
            line-height: 30px;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-delete {
            background-color: #dc3545;
        }
    </style>
</head>

<body>
    <?php include 'includes/sidebar.php'; ?>
    <section id="content">
        <?php include 'includes/navbar.php'; ?>
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Tours</h1>
                    <?php include 'includes/breadcrumb.php'; ?>
                </div>
                <button class="btn-download" id="btn-download">
                    <i class='bx bx-plus'></i>Add tours
                </button>
            </div>
            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Tourist Spot List</h3>
                        <i class='bx bx-search'></i>
                        <i class='bx bx-filter'></i>
                    </div>
                    <?php
                    if (!empty($tour)) {
                        foreach ($tour as $row) {
                            if ($row['status'] == 1){
                                $status = 'Active';
                            }elseif ($row['status'] == 2){
                                $status = 'Inactive';
                            }
                            $images = explode(',', $row['img']);
                            echo '<div class="data">';
                            foreach ($images as $image) {
                                echo '<div class="img">';
                                echo '<img src="../upload/Tour Images/' . $image . '" alt="Tour Image">';
                                echo '</div>';
                            }
                            echo '<div class="content">';
                            echo '<h4>' . htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') . '</h4>';
                            echo '<p>Address: ' . htmlspecialchars($row['address'], ENT_QUOTES, 'UTF-8') . '</p>';
                            echo '<p>Type: ' . htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8') . '</p>';
                            echo '<p>Description: ' . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . '</p>';
                            echo '<p>Status: ' . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . '</p>';
                            echo '<div class="action-buttons">';
                            echo '<a href="edit-tour?id=' . urlencode($row['id']) . '" class="btn-edit">Edit</a>';
                            echo '<a href="#" class="btn-delete" data-tour-id="' . urlencode($row['id']) . '">Delete</a>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No tours found.</p>';
                    }
                    ?>

                </div>
            </div>
        </main>
    </section>
    <div id="addTourModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Tour</h2>
            <form action="../php/add_tour.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="tour-title">Title:</label>
                    <input type="text" id="tour-title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="tour-address">Address:</label>
                    <input type="text" id="tour-address" name="address" required>
                </div>
                <div class="form-group">
                    <label for="tour-type">Type:</label>
                    <select id="tour-type" name="type" required>
                        <option value="Mountain Resort">Mountain Resort</option>
                        <option value="Beach Resort">Beach Resort</option>
                        <option value="Historical Landmark">Historical Landmark</option>
                        <option value="Park">Park</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tour-description">Description:</label>
                    <textarea id="tour-description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="tour-images">Image:</label>
                    <input type="file" id="tour-images" name="img" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="tour-longitude">Longitude:</label>
                    <input type="text" id="tour-longitude" name="longitude" readonly required>
                </div>
                <div class="form-group">
                    <label for="tour-latitude">Latitude:</label>
                    <input type="text" id="tour-latitude" name="latitude" readonly required>
                </div>
                <button type="button" id="set-location">Set Location on Map</button>
                <button type="submit" class="btn-submit">Add Tour</button>
            </form>
        </div>
    </div>


    <div id="mapboxModal" class="modal">
        <div class="modal-content">
            <span class="close-map">&times;</span>
            <div id="map" style="height: 94%; width:100%"></div>
        </div>
    </div>


    <script src="../assets/js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById("addTourModal");
            var mapboxModal = document.getElementById("mapboxModal");
            var btn = document.getElementById("btn-download");
            var btnSetLocation = document.getElementById("set-location");
            var closeBtn = document.querySelector(".close");
            var closeMapBtn = document.querySelector(".close-map");

            // Initialize Mapbox
            mapboxgl.accessToken = 'pk.eyJ1Ijoibmlrb2xhaTEyMjIiLCJhIjoiY2x6d3pva281MGx6ODJrczJhaTJ4M2RmYyJ9.0sJ2ZGR2xpEza2j370y3rQ';
            var map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/streets-v11',
                center: [122.9413, 10.4998],
                zoom: 10.2
            });

            var marker;

            function resizeMap() {
                map.resize();
            }
            map.on('click', function(e) {
                var lngLat = e.lngLat;
                if (marker) {
                    marker.setLngLat(lngLat);
                } else {
                    marker = new mapboxgl.Marker()
                        .setLngLat(lngLat)
                        .addTo(map);
                }
                document.getElementById('tour-longitude').value = lngLat.lng;
                document.getElementById('tour-latitude').value = lngLat.lat;
                mapboxModal.style.display = "none";
            });
            btn.onclick = function() {
                modal.style.display = "block";
            }
            btnSetLocation.onclick = function() {
                mapboxModal.style.display = "block";
                resizeMap();
            }

            closeBtn.onclick = function() {
                modal.style.display = "none";
            }

            closeMapBtn.onclick = function() {
                mapboxModal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == mapboxModal) {
                    mapboxModal.style.display = "none";
                }
            }
        });
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const tourId = this.getAttribute('data-tour-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('../php/delete_tour.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: new URLSearchParams({
                                    'tour_id': tourId
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log(data);
                                if (data.success) {
                                    Swal.fire('Deleted!', data.message, 'success').then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Error!', data.message, 'error');
                                }
                            })
                            .catch(error => {
                                Swal.fire('Error!', 'An error occurred while deleting the tour.', 'error');
                            });
                    }
                });
            });
        });
    </script>
</body>

</html>