<?php
session_start();
include("../include/db_conn.php");

$query = "SELECT id, title, latitude, longitude, type, img, address FROM tours";
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
?>

<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="assets/css/style.css">

  <script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
  <link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />

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

    .popup-content img {
      width: 100%;
      height: auto;
      border-radius: 5px;
    }

    .popup-content h3 {
      margin: 10px 0 5px;
      font-size: 16px;
    }

    .popup-content p {
      margin: 0;
      font-size: 14px;
      color: #555;
    }
  </style>
</head>

<body>

  <?php include('inc/topnav.php'); ?>

  <div id='map'></div>

  <script>
document.addEventListener('DOMContentLoaded', () => {
  mapboxgl.accessToken = 'pk.eyJ1Ijoibmlrb2xhaTEyMjIiLCJhIjoiY20wZ3VqMzZuMDVhNDJycW9mbHE3emh2NCJ9.BFCb9yfuCSZDZW_U5Qdi3Q';

  navigator.geolocation.getCurrentPosition(successLocation, errorLocation, {
    enableHighAccuracy: true,
    timeout: 10000,
    maximumAge: 0
  });

  function successLocation(position) {
    setupMap([position.coords.longitude, position.coords.latitude]);
  }

  function errorLocation() {
    setupMap([122.8313, 10.5338]);
  }

  function setupMap(center) {
    const map = new mapboxgl.Map({
      container: 'map',
      style: 'mapbox://styles/mapbox/streets-v12',
      center: center,
      zoom: 11
    });
    map.addControl(new mapboxgl.NavigationControl());
    map.addControl(new mapboxgl.GeolocateControl({
      positionOptions: { enableHighAccuracy: true },
      trackUserLocation: true,
      showUserHeading: true
    }));

    const touristSpots = <?php echo json_encode($touristSpots); ?>;

    touristSpots.forEach(({ type, longitude, latitude, image, title, address, id }) => {
      const markerEl = document.createElement('div');
      markerEl.className = 'marker';
      markerEl.style.backgroundImage = `url(../assets/icons/${type.split(' ')[0]}.png)`;
      markerEl.style.width = '30px';
      markerEl.style.height = '30px';
      markerEl.style.backgroundSize = 'cover';

      const marker = new mapboxgl.Marker(markerEl)
        .setLngLat([longitude, latitude])
        .addTo(map);

      const popupContent = `
        <div class="popup-content" style="border-radius:26px;">
          <img src="../upload/Tour Images/${image}" alt="${title}" style="width: 100%; height: 80%;">
          <h3>${title}</h3>
          <p>${address}</p>
        </div>
      `;

      const popup = new mapboxgl.Popup({ closeOnClick: false, offset: 25 ,closeButton: false})
        .setLngLat([longitude, latitude])
        .setHTML(popupContent);

      markerEl.addEventListener('mouseenter', () => popup.addTo(map));
      markerEl.addEventListener('mouseleave', () => popup.remove());
      markerEl.addEventListener('click', () => {
        window.location.href = `tour?tours=${id}`;
      });
    });
  }
});


    function myFunction() {
      var x = document.getElementById("myTopnav");
      if (x.className === "topnav") {
        x.className += " responsive";
      } else {
        x.className = "topnav";
      }
    }

    function showTab(tabNumber) {
      const tabs = document.querySelectorAll('.tab');
      tabs.forEach(tab => {
        tab.classList.remove('active');
      });

      document.getElementById('tab' + tabNumber).classList.add('active');
    }
  </script>

</body>

</html>