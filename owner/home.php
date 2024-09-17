<?php
session_start();

if (!isset($_SESSION['user_id'])) {
	header("Location: ../login.php?action=Invalid");
	exit();
}
require_once '../include/db_conn.php';
$user_id = $_SESSION['user_id'];
$pp = $_SESSION['profile-pic'];

$query = "SELECT id, title, latitude, longitude, type, address, img FROM tours";
$result = $conn->query($query);

$touristSpots = [];

if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">
	<!-- My CSS -->
	<link rel="stylesheet" href="../assets/css/admin.css">
	<!-- Mapbox -->
	<script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
	<link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />

	<title>BaGoTours. Home</title>
	<style>
		#map {
			width: 100%;
			height: 500px;
		}

		.marker {
			background-size: cover;
			width: 30px;
			height: 30px;
			border-radius: 50%;
			cursor: pointer;
		}

		.mapboxgl-popup {
			max-width: 200px;
			font: 12px/20px 'Helvetica Neue', Arial, Helvetica, sans-serif;
		}

		.mapboxgl-popup-content {
			text-align: center;
			font-weight: bold;
		}

		.marker:hover {
			transform: scale(1.1);
		}

		.marker:active {
			transform: scale(1.2);
		}

		.popup-content {
			background-color: #fff;
			padding: 20px;
			border-radius: 5px;
		}

		.popup-content img {
			width: 180px;
		}

		.popup-close {
			color: black;
			font-size: 20px;
			cursor: pointer;
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
					<h1>Home</h1>
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

	<script src="../assets/js/script.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', () => {
			mapboxgl.accessToken = 'pk.eyJ1Ijoibmlrb2xhaTEyMjIiLCJhIjoiY2x6d3pva281MGx6ODJrczJhaTJ4M2RmYyJ9.0sJ2ZGR2xpEza2j370y3rQ';

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
						offset: 25
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
					window.location.href = `tourist_spot_details.php?id=${spot.id}`;
				});
			});
		});
	</script>
</body>

</html>