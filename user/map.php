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
$touristSpotsJson = json_encode($touristSpots);
 
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
    console.log(<?php echo $touristSpotsJson; ?>);
    document.addEventListener('DOMContentLoaded', () => {
      mapboxgl.accessToken = 'pk.eyJ1Ijoibmlrb2xhaTEyMjIiLCJhIjoiY2x6d3pva281MGx6ODJrczJhaTJ4M2RmYyJ9.0sJ2ZGR2xpEza2j370y3rQ';

      const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v12',
        center: [122.8313, 10.5338],
        zoom: 11
      });

      const touristSpots = <?php echo $touristSpotsJson; ?>;

      touristSpots.forEach(spot => {
        let iconUrl = '../assets/icons/falls.png';
        if (spot.type === 'resort') iconUrl = '../assets/icons/resort.png';
        else if (spot.type === 'beach') iconUrl = '../assets/icons/beach.png';
        else if (spot.type === 'historical') iconUrl = '../assets/icons/historical.png';

        const el = document.createElement('div');
        el.className = 'marker';
        el.style.backgroundImage = `url(${iconUrl})`;

        const marker = new mapboxgl.Marker(el)
          .setLngLat([spot.longitude, spot.latitude])
          .addTo(map);

        const popupContent = `
          <div class="popup-content">
            <img src="../upload/Tour Images/${spot.image}" alt="${spot.title}">
            <h3>${spot.title}</h3>
            <p>${spot.address}</p>
          </div>
        `;

        const popup = new mapboxgl.Popup({
          closeOnClick: false,
          offset: 25
        }).setHTML(popupContent);

        marker.getElement().addEventListener('mouseenter', () => {
          popup.addTo(map);
          popup.setLngLat([spot.longitude, spot.latitude]);
        });

        marker.getElement().addEventListener('mouseleave', () => {
          popup.remove();
        });

        marker.getElement().addEventListener('click', () => {
          window.location.href = `tour?tours=${spot.id}`;
        });
      });
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