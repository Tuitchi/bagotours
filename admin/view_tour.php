<?php
include '../include/db_conn.php';
include '../func/user_func.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?action=Invalid");
    exit();
}
$user_id = $_SESSION['user_id'];
$pp = $_SESSION['profile-pic'];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $tour = getTourById($conn, $id);
} else {
    header("Location: tours.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo $webIcon ?>">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <!-- Mapbox -->
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />
    <title>BaGoTours - View Tour</title>
    <style>
        .tour-container {
            margin-top: 20px;
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .tour-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .tour-container img {
            width: 100%;
            border-radius: 10px;
        }

        .tour-container p {
            font-size: 1.2em;
            margin: 10px 0;
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
            <div id="map" style="height: 400px; width: 80%; margin-top: 20px;"></div>
            <div class="tour-container">
                <?php if (!empty($tour)) { ?>
                    <h1><?php echo htmlspecialchars($tour['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                    <img src="../upload/Tour Images/<?php echo htmlspecialchars($tour['img'], ENT_QUOTES, 'UTF-8'); ?>" alt="Tour Image">
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($tour['address'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($tour['type'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($tour['description'], ENT_QUOTES, 'UTF-8')); ?></p>
                    <p><strong>Status:</strong> <?php echo $tour['status'] == 1 ? 'Active' : 'Inactive'; ?></p>
                    <button class="btn-edit" type="button" onclick="loadDoc()">Edit</button>
                    <a href="#" class="btn-delete" data-tour-id="<?php echo $tour['id']; ?>">Delete</a>

                <?php } else { ?>
                    <p>Tour not found.</p>
                <?php } ?>
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
            xhttp.open("GET", "edit_tour?id=<?php echo $tour['id']?>", true);
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

        document.querySelector('.btn-delete').addEventListener('click', function(e) {
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
                                    window.location.href = 'tours.php';
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
    </script>
</body>

</html>