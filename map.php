<?php
session_start();
include 'func/user_func.php';
require 'include/db_conn.php';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $spotDirection = null;

    if (isset($_GET['id'])) {
        $decrypted_id_raw = base64_decode($_GET['id']);
        $decrypted_id = preg_replace(sprintf('/%s/', $salt), '', $decrypted_id_raw);
        $touristSpots = getTourById($conn, $decrypted_id);
        if ($touristSpots) {
            $touristSpots['average_rating'] = getAverageRating($conn, $touristSpots['id']);
            $spotDirection = [
                'longitude' => $touristSpots['longitude'],
                'latitude' => $touristSpots['latitude'],
                'title' => $touristSpots['title'],
                'type' => $touristSpots['type']
            ];
        }
    } elseif (isset($_GET['event'])) {
        $decrypted_id_raw = base64_decode($_GET['event']);
        $decrypted_id = preg_replace(sprintf('/%s/', $salt), '', $decrypted_id_raw);
        $touristSpots = getEventbyCode($conn, $decrypted_id);
        if ($touristSpots) {
            $spotDirection = [
                'longitude' => $touristSpots['longitude'],
                'latitude' => $touristSpots['latitude'],
                'title' => $touristSpots['event_name'],
                'type' => 'stars'
            ];
        }
    } else {
        $touristSpots = getAllTours($conn);
        foreach ($touristSpots as &$spot) {
            $spot['average_rating'] = getAverageRating($conn, $spot['id']);
        }
        unset($spot);
    }
} else {
    header('Location: home?login=true');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BagoTours</title>
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="assets/css/map.css">
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo $webIcon ?>">
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.8.0/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.8.0/mapbox-gl.js"></script>
    <style>
        .distance-display {
            display: none;
            position: absolute;
            left: 50%;
            bottom: 20px;
            /* Position it 20px from the bottom */
            transform: translateX(-50%);
            /* Center it horizontally */
            background: rgba(255, 255, 255, 0.8);
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            font-family: Arial, sans-serif;
            font-size: 16px;
            z-index: 999;
        }


        .instructions-container {
            display: none;
            position: absolute;
            /* Use absolute positioning */
            top: 20px;
            /* Adjust this value to position vertically */
            left: 20px;
            /* Adjust this value to position horizontally */
            background-color: rgba(255, 255, 255, 0.9);
            /* Semi-transparent background */
            padding: 10px;
            /* Padding around the content */
            border-radius: 8px;
            /* Rounded corners */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            /* Slight shadow for depth */
            z-index: 999;
            /* Ensure it appears above other elements */
            max-width: 250px;
            /* Optional: limit width of instructions */
            overflow-y: auto;
            /* Allow scrolling if content is too long */
            height: 300px;
            /* Optional: limit height of instructions */
        }

        /* Additional styles for the step instruction */
        .step-instruction {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            /* Divider between steps */
        }

        .step-instruction:last-child {
            border-bottom: none;
            /* Remove border for last item */
        }

        .step-instruction {
            margin: 5px;
            padding: 8px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .loader {
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 3;
            pointer-events: none;
            /* Ensure the loader doesn't block map interactions */
        }

        .loader::after {
            content: '';
            width: 50px;
            height: 50px;
            border: 6px solid #ddd;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <?php include 'nav/topnav.php'; ?>
    <div class="main-container">
        <?php include 'nav/sidenav.php'; ?>
        <div class="main">

            <div class="map"><?php if ($spotDirection == null): ?>
                    <h3>Tours Map</h3>
                <?php endif; ?>

                <div id="map">
                    <div id="loader" class="loader"></div>
                    <div class="distance-display"></div>
                    <div class="instructions-container">
                        <h3>Route Instructions</h3>
                        <div id="instructions-list"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="index.js"></script>
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script>
        mapboxgl.accessToken = 'pk.eyJ1Ijoibmlrb2xhaTEyMjIiLCJhIjoiY20xemJ6NG9hMDRxdzJqc2NqZ3k5bWNlNiJ9.tAsio6eF8LqzAkTEcPLuSw';
        const spotDirection = <?php echo json_encode($spotDirection); ?>;
        let map, userMarker;
        const distanceDisplay = document.querySelector('.distance-display');
        const instructionDisplay = document.querySelector('.instructions-container');
        let instructionsList, currentStepIndex = 0,
            isSpeaking = false;

        const geolocateControl = new mapboxgl.GeolocateControl({
            positionOptions: {
                enableHighAccuracy: true
            },
            trackUserLocation: true,
            showUserHeading: true
        });

        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                position => {
                    const userLocation = [position.coords.longitude, position.coords.latitude];
                    const heading = position.coords.heading;

                    if (!map) {
                        setupMap(userLocation);
                    } else {
                        animateMarker(userMarker, userLocation);

                        if (heading !== null) {
                            map.setBearing(heading);
                        }
                    }
                    if (spotDirection) {
                        const destination = [spotDirection.longitude, spotDirection.latitude];
                        updateDistanceAndRoute(userLocation, destination);
                    }
                },
                () => setupMap([122.8313, 10.5338]), {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
            );
        } else {
            console.log('Geolocation is not supported by your browser.');
            alert('Geolocation is not supported by your browser');
            window.location.href = 'home';
        }

        function setupMap(center) {
            if (!map) {
                map = new mapboxgl.Map({
                    container: 'map',
                    style: 'mapbox://styles/mapbox/navigation-night-v1',
                    center: center,
                    zoom: 11
                });

                map.addControl(geolocateControl);
                map.addControl(new mapboxgl.FullscreenControl());
                map.addControl(new mapboxgl.NavigationControl());

                map.on('load', () => {
                    const loader = document.getElementById('loader');
                    if (loader) loader.style.display = 'none';
                    if (spotDirection) {
                        geolocateControl.trigger();
                    }
                });
            }

            if (!userMarker) {
                userMarker = new mapboxgl.Marker().setLngLat(center).addTo(map);
            } else {
                userMarker.setLngLat(center);
            }

            if (spotDirection) {
                map.on('load', () => geolocateControl.trigger());
                distanceDisplay.style.display = "flex";
                instructionDisplay.style.display = "block";
                const destination = [spotDirection.longitude, spotDirection.latitude];
                addDestinationMarker(destination, spotDirection.type);
                updateDistanceAndRoute(center, destination, spotDirection.title);
            } else {
                loadTouristSpots();
            }
        }

        function addDestinationMarker(destination, type) {
            const destinationMarkerEl = document.createElement('div');
            destinationMarkerEl.className = 'marker';
            destinationMarkerEl.style.backgroundImage = `url(assets/icons/${type.split(' ')[0]}.png)`;
            destinationMarkerEl.style.width = '30px';
            destinationMarkerEl.style.height = '30px';
            new mapboxgl.Marker(destinationMarkerEl).setLngLat(destination).addTo(map);
        }

        async function updateDistanceAndRoute(start, end, title) {
            const distance = calculateDistance(start, end);
            updateDistanceDisplay(distance, title);
            map.flyTo({
                center: start,
                zoom: 15
            });
            await getRoute(start, end);
        }

        function calculateDistance(coord1, coord2) {
            const R = 6371; // Radius of Earth in kilometers
            const dLat = toRad(coord2[1] - coord1[1]);
            const dLon = toRad(coord2[0] - coord1[0]);
            const a = Math.sin(dLat / 2) ** 2 +
                Math.cos(toRad(coord1[1])) * Math.cos(toRad(coord2[1])) * Math.sin(dLon / 2) ** 2;
            return R * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))); // distance in km
        }

        function toRad(value) {
            return value * Math.PI / 180;
        }

        function updateDistanceDisplay(distance, title) {
            distanceDisplay.textContent = `Distance to ${title} - ${distance < 1 ? (distance * 1000).toFixed(0) + ' meters' : distance.toFixed(2) + ' km'}`;
        }

        async function getRoute(start, end) {
            try {
                const response = await fetch(`https://api.mapbox.com/directions/v5/mapbox/driving/${start[0]},${start[1]};${end[0]},${end[1]}?steps=true&geometries=geojson&access_token=${mapboxgl.accessToken}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const json = await response.json();
                if (json.routes.length === 0) return console.error('No routes found');

                const route = json.routes[0].geometry.coordinates;
                const steps = json.routes[0].legs[0].steps;

                const geojson = {
                    type: 'Feature',
                    geometry: {
                        type: 'LineString',
                        coordinates: route
                    }
                };

                if (map.getSource('route')) {
                    map.getSource('route').setData(geojson);
                } else {
                    map.addLayer({
                        id: 'route',
                        type: 'line',
                        source: {
                            type: 'geojson',
                            data: geojson
                        },
                        layout: {
                            'line-join': 'round',
                            'line-cap': 'round'
                        },
                        paint: {
                            'line-color': '#34f934',
                            'line-width': 10,
                            'line-opacity': 0.75
                        }
                    });
                }

                displayInstructions(steps, start);
            } catch (error) {
                console.error('Error fetching directions:', error);
            }
        }

        function displayInstructions(steps, start) {
            instructionsList = document.getElementById('instructions-list');
            instructionsList.innerHTML = '';

            steps.forEach((step, index) => {
                const distanceToStep = calculateDistance(start, step.maneuver.location) * 1000;
                const distanceText = distanceToStep < 1000 ? `${distanceToStep.toFixed(0)} meters` : `${(distanceToStep / 1000).toFixed(2)} km`;

                const instructionText = `${index + 1}: ${step.maneuver.instruction} (Distance: ${distanceText})`;
                const stepDiv = document.createElement('div');
                stepDiv.className = 'step-instruction';
                stepDiv.textContent = instructionText;
                instructionsList.appendChild(stepDiv);
            });
        }

        function animateMarker(marker, newPosition, duration = 500) {
            const start = marker.getLngLat();
            const end = new mapboxgl.LngLat(newPosition[0], newPosition[1]);
            const startTime = performance.now();

            function animate(time) {
                const progress = Math.min((time - startTime) / duration, 1);
                marker.setLngLat([start.lng + (end.lng - start.lng) * progress, start.lat + (end.lat - start.lat) * progress]);

                if (progress < 1) requestAnimationFrame(animate);
            }
            requestAnimationFrame(animate);
        }

        function loadTouristSpots() {
            const touristSpots = <?php echo json_encode($touristSpots); ?>;

            touristSpots.forEach(spot => {
                const {
                    type,
                    longitude,
                    latitude,
                    img,
                    title,
                    address,
                    id,
                    average_rating
                } = spot;

                const markerEl = document.createElement('div');
                markerEl.className = 'marker';
                markerEl.style.backgroundImage = `url(assets/icons/${type.split(' ')[0]}.png)`;
                markerEl.style.width = '30px';
                markerEl.style.height = '30px';

                const marker = new mapboxgl.Marker(markerEl).setLngLat([longitude, latitude]).addTo(map);
                const mainImage = img.split(',')[0];
                const popupContent = `
                <div class="popup-content" style="border-radius:26px;">
                    <img src="upload/Tour Images/${mainImage}" alt="${title}" style="width: 100%; height: 80%;">
                    <h3>${title}</h3>
                    <p>${address}</p>
                    <p>⭐ ${(Math.floor(average_rating * 100) / 100).toFixed(1)} / 5</p>
                </div>
            `;

                const popup = new mapboxgl.Popup({
                    closeOnClick: false,
                    offset: 25,
                    closeButton: false
                })
                    .setLngLat([longitude, latitude])
                    .setHTML(popupContent);

                markerEl.addEventListener('mouseenter', () => popup.addTo(map));
                markerEl.addEventListener('mouseleave', () => popup.remove());
                markerEl.addEventListener('click', () => {
                    const salt = '<?php echo $salt; ?>';
                    const combinedString = id + salt;
                    const base64Combined = btoa(combinedString);
                    window.location.href = `tour?id=${base64Combined}`;
                });
            });
        }
    </script>

</body>

</html>