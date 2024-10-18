<?php
session_start();
include 'func/user_func.php';
require 'include/db_conn.php';

$user_id = $_SESSION['user_id'] ?? null;

$spotDirection = null;

if (isset($_GET['id'])) {
    $touristSpots = getTourById($conn, $_GET['id']);
    if ($touristSpots) {
        $touristSpots['average_rating'] = getAverageRating($conn, $touristSpots['id']);
        $spotDirection = [
            'longitude' => $touristSpots['longitude'],
            'latitude' => $touristSpots['latitude'],
            'title' => $touristSpots['title'],
            'type' => $touristSpots['type']
        ];
    }
} else {
    $touristSpots = getAllTours($conn);
    foreach ($touristSpots as &$spot) {
        $spot['average_rating'] = getAverageRating($conn, $spot['id']);
    }
    unset($spot);
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
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.css" rel="stylesheet" />
    <style>
        /* Add this CSS to your user.css or another appropriate stylesheet */
        .distance-display {
            display: flex;
            position:absolute;
            left: 50%;
            margin-top: 20px;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.8);
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            font-family: Arial, sans-serif;
            font-size: 16px;
            z-index: 999;
        }
    </style>
</head>

<body>
    <?php include 'nav/topnav.php'; ?>
    <div class="main-container">
        <?php include 'nav/sidenav.php'; ?>
        <div class="main">
            <div class="map">
                <h3>Tours Map</h3>
                <div id="map">
                    <div class="distance-display"></div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            mapboxgl.accessToken = 'pk.eyJ1Ijoibmlrb2xhaTEyMjIiLCJhIjoiY20xemJ6NG9hMDRxdzJqc2NqZ3k5bWNlNiJ9.tAsio6eF8LqzAkTEcPLuSw';
            const spotDirection = <?php echo json_encode($spotDirection); ?>;
            let userMarker, map;
            const distanceDisplay = document.querySelector('.distance-display');

            // Check for geolocation support
            if ('geolocation' in navigator) {
                navigator.geolocation.watchPosition(successLocation, errorLocation, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            } else {
                console.log('Geolocation is not supported by your browser.');
            }

            function successLocation(position) {
                const userLocation = [position.coords.longitude, position.coords.latitude];
                if (!map) {
                    setupMap(userLocation);
                } else {
                    animateMarkerMovement(userMarker, userMarker.getLngLat(), userLocation);
                }

                if (spotDirection) {
                    const destination = [spotDirection.longitude, spotDirection.latitude];
                    getRoute(userLocation, destination);
                    const distance = calculateDistance(userLocation, destination);
                    updateDistanceDisplay(distance);
                }
            }

            function errorLocation() {
                const defaultCenter = [122.8313, 10.5338];
                alert('Unable to get your location, using default location.');
                setupMap(defaultCenter);

                if (spotDirection) {
                    const destination = [spotDirection.longitude, spotDirection.latitude];
                    const distance = calculateDistance(defaultCenter, destination);
                    updateDistanceDisplay(distance);
                    getRoute(defaultCenter, destination);
                }
            }

            function setupMap(center) {
                map = new mapboxgl.Map({
                    container: 'map',
                    style: 'mapbox://styles/mapbox/streets-v12',
                    center: center,
                    zoom: 11
                });

                // User marker
                userMarker = new mapboxgl.Marker().setLngLat(center).addTo(map);
                const scale = new mapboxgl.ScaleControl({
                    maxWidth: 80,
                    unit: 'imperial'
                });
                map.addControl(scale);

                scale.setUnit('metric');
                map.addControl(new mapboxgl.FullscreenControl());
                map.addControl(new mapboxgl.NavigationControl());
                map.addControl(new mapboxgl.GeolocateControl({
                    positionOptions: { enableHighAccuracy: true },
                    trackUserLocation: true,
                    showUserHeading: true
                }));

                if (spotDirection) {
                    const destination = [spotDirection.longitude, spotDirection.latitude];
                    addDestinationMarker(destination, spotDirection.type);
                    getRoute(center, destination);
                }

                loadTouristSpots();
            }

            function addDestinationMarker(destination, type) {
                const destinationMarkerEl = document.createElement('div');
                destinationMarkerEl.className = 'marker';
                destinationMarkerEl.style.backgroundImage = `url(assets/icons/${type.split(' ')[0]}.png)`;
                destinationMarkerEl.style.width = '30px';
                destinationMarkerEl.style.height = '30px';
                new mapboxgl.Marker(destinationMarkerEl).setLngLat(destination).addTo(map);
            }

            function animateMarkerMovement(marker, start, end, duration = 1000) {
                let startTime = null;
                const [startLng, startLat] = [start.lng, start.lat];
                const [endLng, endLat] = end;

                function step(timestamp) {
                    if (!startTime) startTime = timestamp;
                    const elapsed = timestamp - startTime;
                    const t = Math.min(elapsed / duration, 1);
                    marker.setLngLat([startLng + (endLng - startLng) * t, startLat + (endLat - startLat) * t]);

                    if (t < 1) requestAnimationFrame(step);
                }

                requestAnimationFrame(step);
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

            function updateDistanceDisplay(distance) {
                distanceDisplay.textContent = `Distance to destination: ${distance.toFixed(2)} km`;
            }

            async function getRoute(start, end) {
                try {
                    const response = await fetch(`https://api.mapbox.com/directions/v5/mapbox/driving/${start[0]},${start[1]};${end[0]},${end[1]}?steps=true&geometries=geojson&access_token=${mapboxgl.accessToken}`);
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                    const json = await response.json();
                    if (json.routes.length === 0) return console.error('No routes found');

                    const route = json.routes[0].geometry.coordinates;
                    const geojson = {
                        type: 'Feature',
                        geometry: { type: 'LineString', coordinates: route }
                    };

                    if (map.getSource('route')) {
                        map.getSource('route').setData(geojson);
                    } else {
                        map.addLayer({
                            id: 'route',
                            type: 'line',
                            source: { type: 'geojson', data: geojson },
                            layout: { 'line-join': 'round', 'line-cap': 'round' },
                            paint: { 'line-color': '#3887be', 'line-width': 5, 'line-opacity': 0.75 }
                        });
                    }
                } catch (error) {
                    console.error('Error fetching directions:', error);
                }
            }

            function loadTouristSpots() {
                const touristSpots = <?php echo json_encode($touristSpots); ?>;
                if (touristSpots.length > 0) {
                    touristSpots.forEach(({ type, longitude, latitude, img, title, address, id, average_rating }) => {
                        const markerEl = document.createElement('div');
                        markerEl.className = 'marker';
                        markerEl.style.backgroundImage = `url(assets/icons/${type.split(' ')[0]}.png)`;
                        markerEl.style.width = '30px';
                        markerEl.style.height = '30px';

                        const marker = new mapboxgl.Marker(markerEl).setLngLat([longitude, latitude]).addTo(map);

                        const popupContent = `
                            <div class="popup-content" style="border-radius:26px;">
                                <img src="upload/Tour Images/${img}" alt="${title}" style="width: 100%; height: 80%;">
                                <h3>${title}</h3>
                                <p>${address}</p>
                                <p>⭐ ${(Math.floor(average_rating * 100) / 100).toFixed(1)} / 5</p>
                            </div>
                        `;

                        const popup = new mapboxgl.Popup({ closeOnClick: false, offset: 25, closeButton: false })
                            .setLngLat([longitude, latitude])
                            .setHTML(popupContent);

                        markerEl.addEventListener('load', () => popup.addTo(map));
                        markerEl.addEventListener('mouseenter', () => popup.addTo(map));
                        markerEl.addEventListener('mouseleave', () => popup.remove());
                        markerEl.addEventListener('click', () => window.location.href = `tour?tours=${id}`);
                    });
                } else {
                    console.log("No tourist spots found.");
                }
            }
        </script>
    </div>
</body>

</html>