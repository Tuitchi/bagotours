<?php
session_start();
require_once '../include/db_conn.php';
$user_id = $_SESSION['user_id'];

try {
	$query = "SELECT id, title, latitude, longitude, type, address, img, status FROM tours WHERE status NOT IN ('Pending', 'Rejected', 'Confirmed', '')";
	$stmt = $conn->prepare($query);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$touristSpots = [];

	if ($result) {
		foreach ($result as $row) {
			$image_column = $row['img']; // Example: "image1.jpg,image2.jpg,image3.jpg"
			$image_array = explode(',', $image_column); // Convert to an array

			$touristSpots[] = [
				'id' => $row['id'],
				'title' => $row['title'],
				'latitude' => $row['latitude'],
				'longitude' => $row['longitude'],
				'type' => $row['type'],
				'image' => $image_array[0],
				'address' => $row['address'],
				'status' => $row['status']
			];
		}
	}

	$touristSpotsJson = json_encode($touristSpots);
} catch (PDOException $e) {
	error_log("Error fetching tours: " . $e->getMessage());
	$touristSpotsJson = json_encode(['error' => 'Unable to fetch tourist spots.']);
}
try {
	$query = "SELECT event_code, event_name, latitude, longitude, event_type, event_image FROM events WHERE status = 'upcoming' AND event_date_end > NOW()";
	$stmt = $conn->prepare($query);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$events = []; // Initialize an empty array to store events

	// Check if the result set is not empty
	if ($result) {
		foreach ($result as $row) {
			// Create an associative array for each event
			$events[] = [
				'event_code' => $row['event_code'],    // Correct mapping of 'event_code'
				'title' => $row['event_name'],         // Correct mapping of 'event_name'
				'latitude' => $row['latitude'],        // Correct mapping of 'latitude'
				'longitude' => $row['longitude'],      // Correct mapping of 'longitude'
				'type' => $row['event_type'],          // Correct mapping of 'event_type'
				'image' => $row['event_image'],        // Correct mapping of 'event_image'
				// 'address' => $row['address']          // Optional, if applicable
			];
		}
	}

	// Encode the array to JSON format
	$eventsJson = json_encode($events); // Store events in JSON format
} catch (PDOException $e) {
	// Log the error message
	error_log("Error fetching events: " . $e->getMessage());
	// Return an error message in JSON format
	$eventsJson = json_encode(['error' => 'Unable to fetch tourist spots.']);
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
	<link rel="stylesheet" href="../assets/css/mapbox-gl.css">
	<!-- Mapbox -->
	<script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>

	<title>BaGoTours || Home</title>

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
	<script src="../assets/js/script.js"></script>
	<script src="../assets/js/jquery-3.7.1.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', () => {
			fetch('../php/map_usage.php', {
					method: 'POST'
				})
				.then(response => response.json())
				.then(data => {
					if (data.allowMap) {
						initializeMap();
					} else {
						alert('Map access has been temporarily disabled due to usage limits.');
					}
				})
				.catch(error => {
					console.error('Error checking map usage:', error);
				});

			function initializeMap() {
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
						<p>${spot.status}</p>
						<h3>${spot.title}</h3>
						<p>${spot.address}</p>
					</div>
					`;

					const popup = new mapboxgl.Popup({
							closeOnClick: false,
							offset: 25,
							closeButton: false
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
						window.location.href = `edit-tour?id=${spot.id}`;
					});
				});
				const events = <?php echo $eventsJson; ?>;

				events.forEach(event => {
					const el = document.createElement('div');
					el.className = 'marker event';
					el.style.backgroundImage = `url(../assets/icons/stars.png)`;

					const marker = new mapboxgl.Marker(el)
						.setLngLat([event.longitude, event.latitude])
						.addTo(map);

					const popupContent = `
							<div class="popup-content event">
								<img src="../upload/Event/${event.image}" alt="${event.title}" class="popup-image">
								<h3 class="popup-title">${event.title}</h3>
								<p class="popup-type">${event.type}</p>
								<a href="#" class="popup-link">Learn More</a>
							</div>
`;

					const popup = new mapboxgl.Popup({
							closeOnClick: false,
							offset: 25,
							closeButton: false
						})
						.setHTML(popupContent);

					marker.getElement().addEventListener('mouseenter', () => {
						popup.addTo(map);
						popup.setLngLat([event.longitude, event.latitude]);
					});

					marker.getElement().addEventListener('mouseleave', () => {
						popup.remove();
					});

					marker.getElement().addEventListener('click', () => {
						window.location.href = `view-event?event=${event.event_code}`;
					});

				});
			}
		});
		<?php
		if (isset($_SESSION['loginSuccess']) && $_SESSION['loginSuccess'] == true) {
			echo "
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                icon: 'success',
                title: 'Admin access granted. You are now logged in.',
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire();
            ";
			unset($_SESSION['loginSuccess']);
		}
		?>
	</script>
</body>

</html>