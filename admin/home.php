<?php
session_start();


$pageRole = "admin";
require_once '../php/accValidation.php';
require_once '../include/db_conn.php';
$user_id = $_SESSION['user_id'];
$pp = $_SESSION['profile-pic'];

try {
    $query = "SELECT id, title, latitude, longitude, type, address, img FROM tours WHERE status = 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $touristSpots = [];

    if ($result) {
        foreach ($result as $row) {
            $touristSpots[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude'],
                'type' => $row['type'],
                'image' => $row['img'],
                'address' => $row['address']
            ];
        }
    }

    $touristSpotsJson = json_encode($touristSpots);
} catch (PDOException $e) {
    error_log("Error fetching tours: " . $e->getMessage());
    $touristSpotsJson = json_encode(['error' => 'Unable to fetch tourist spots.']);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<!-- My CSS -->
	<link rel="stylesheet" href="assets/css/admin.css">
	<link rel="stylesheet" href="../assets/css/map.css">
	<!-- Mapbox -->
	<script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
	<link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />

	<title>BaGoTours. Home</title>
	
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
					<div class="head">
						<h3>Map</h3>
						<i class='bx bx-search'></i>
						<i class='bx bx-filter'></i>
					</div>
					<div id='map'></div>
				</div>
			</div>
		</main>
	</section>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="../assets/js/script.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', () => {
			mapboxgl.accessToken = 'pk.eyJ1Ijoibmlrb2xhaTEyMjIiLCJhIjoiY20xemJ6NG9hMDRxdzJqc2NqZ3k5bWNlNiJ9.tAsio6eF8LqzAkTEcPLuSw';

			const map = new mapboxgl.Map({
				container: 'map',
				style: 'mapbox://styles/mapbox/streets-v12',
				center: [122.9413, 10.4998],
				zoom: 10.6
			});

			const touristSpots = <?php echo $touristSpotsJson; ?>;

			touristSpots.forEach(spot => {
				const el = document.createElement('div');
				el.className = 'marker';
				el.style.backgroundImage = `url(../assets/icons/${spot.type.split(' ')[0]}.png)`;

				const marker = new mapboxgl.Marker(el)
					.setLngLat([spot.longitude, spot.latitude])
					.addTo(map);

				const popupContent = `
					<div class="popup-content">
						<img src="../upload/Tour Images/${spot.image}" alt="${spot.name}">
						<h3>${spot.title}</h3>
						<p>${spot.address}</p>
					</div>
				`;

				const popup = new mapboxgl.Popup({
						closeOnClick: false,
						offset: 25,
						closeButton:false
					})
					.setHTML(popupContent);

				marker.getElement().addEventListener('mouseenter', () => {
					popup.addTo(map);
					popup.setLngLat([spot.longitude, spot.latitude]);
				});

				marker.getElement().addEventListener('mouseleave', () => {
					popup.remove();
				});

				marker.getElement().addEventListener('click', () => {
					window.location.href = `view_tour?id=${spot.id}`;
				});
			});
		});
	</script>
</body>

</html>