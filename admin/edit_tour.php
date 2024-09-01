<?php
include '../include/db_conn.php';
include '../func/user_func.php';
session_start();

$user_id = $_SESSION['user_id'];
$pp = $_SESSION['profile-pic'];

if (isset($_GET['id'])) {
    $tour_id = $_GET['id'];
    $tour = getTourById($conn, $tour_id);
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

        .status-container {
            display: flex;
            align-items: center;
            gap: 20px;
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
                    <h1>View Tours</h1>
                    <?php include 'includes/breadcrumb.php'; ?>
                </div>
            </div>
            <div id="map" style="height: 400px; width: 80%; margin-top: 20px;"></div>
            <div class="tour-container">
                <?php if (!empty($tour)) { ?>
                    <form id="editTour" action="update_tour.php" method="POST">
                        <input type="hidden" name="tour_id" value="<?php echo htmlspecialchars($tour['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <h1>
                            <input type="text" name="title" value="<?php echo htmlspecialchars($tour['title'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </h1>
                        <img src="../upload/Tour Images/<?php echo htmlspecialchars($tour['img'], ENT_QUOTES, 'UTF-8'); ?>" alt="Tour Image">
                        <p>
                            <strong>Address:</strong>
                            <input type="text" name="address" value="<?php echo htmlspecialchars($tour['address'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </p>
                        <p>
                            <strong>Type:</strong>
                            <select id="tour-type" name="type" required>
                                <option value="Mountain Resort" <?php echo ($tour['type'] == 'Mountain Resort') ? 'selected' : ''; ?>>Mountain Resort</option>
                                <option value="Beach Resort" <?php echo ($tour['type'] == 'Beach Resort') ? 'selected' : ''; ?>>Beach Resort</option>
                                <option value="Historical Landmark" <?php echo ($tour['type'] == 'Historical Landmark') ? 'selected' : ''; ?>>Historical Landmark</option>
                                <option value="Park" <?php echo ($tour['type'] == 'Park') ? 'selected' : ''; ?>>Park</option>
                            </select>
                        </p>
                        <p>
                            <strong>Description:</strong>
                            <input type="text" name="description" value="<?php echo htmlspecialchars($tour['description'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </p>
                        <p>
                            <strong>Status:</strong>
                            <div class="status-container">
                                <input type="radio" id="status1" name="status" value="1" <?php echo ($tour['status'] == 1) ? 'checked' : ''; ?>>
                                <label for="status1">Active</label>
                                <input type="radio" id="status2" name="status" value="2" <?php echo ($tour['status'] == 2) ? 'checked' : ''; ?>>
                                <label for="status2">Inactive</label>
                            </div>
                        </p>
                        <a href="#" class="btn-edit" onclick="document.getElementById('editTour').submit(); return false;">Save Edit</a>
                    </form>
                    <a href="view_tour.php?id=<?php echo htmlspecialchars($tour['id'], ENT_QUOTES, 'UTF-8'); ?>" class="btn-delete">Cancel</a>
                <?php } else { ?>
                    <p>Tour not found.</p>
                <?php } ?>
            </div>
        </main>
    </section>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
        });
    </script>
</body>
</html>