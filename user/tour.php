<?php
include("../func/user_func.php");
session_start();

$id = isset($_GET["tours"]) ? intval($_GET["tours"]) : 0;
$status = isset($_GET["status"]) ? $_GET["status"] : '';

$tour = getTourById($conn, $id);
$images = getTourImages($conn, $id);
$average_rating = getAverageRating($conn, $id);

if (!$tour) {
  echo "Tour not found.";
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/tours.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
  <link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />

  <style>
    	.marker {
			background-size: cover;
			width: 30px;
			height: 30px;
			border-radius: 50%;
			cursor: pointer;
		}
    .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
      background-color: #fefefe;
      margin: 15% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
    }
    main img {
      width: 50%;
      max-width: 1000px;
      scale: .5;
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
    }
  </style>
</head>

<body>

  <?php include('inc/topnav.php'); ?>
  <div class="title">
    <h2>BaGoTours</h2>
    <p> => </p>
    <h2><?php echo htmlspecialchars($tour['title']);?></h2>
    <button class="split" id="myBtn">Book Now!</button>
  </div>
  <head>
<div class="slideshow-container">

<div class="mySlides fade">
  <div class="numbertext">1 / 3</div>
  <img src="../assets/gallery-1.jpg" style="width:100%">
  <div class="text">Buenos Aires Mountain Resort</div>
</div>

<div class="mySlides fade">
  <div class="numbertext">2 / 3</div>
  <img src="../assets/gallery-2.jpg" style="width:100%">
  <div class="text">Buenos Aires Mountain Resort</div>
</div>

<div class="mySlides fade">
  <div class="numbertext">3 / 3</div>
  <img src="../assets/gallery-3.jpg" style="width:100%">
  <div class="text">Buenos Aires Mountain Resort</div>
</div>

<a class="prev" onclick="plusSlides(-1)">❮</a>
<a class="next" onclick="plusSlides(1)">❯</a>

</div>
<br>

<div style="text-align:center">
  <span class="dot" onclick="currentSlide(1)"></span> 
  <span class="dot" onclick="currentSlide(2)"></span> 
  <span class="dot" onclick="currentSlide(3)"></span> 
</div>
  </head>
  
  <main>
      <div id='map' style='width: 400px; height: 300px;'></div>
      <img src="../upload/Tour Images/<?php echo htmlspecialchars($tour['img']); ?>" alt="<?php echo htmlspecialchars($tour['title']); ?>">
      <h2><?php echo htmlspecialchars($tour['title']); ?></h2>
      <p>⭐<?php echo number_format($average_rating, 1)?></p>
      <p><?php echo htmlspecialchars($tour['description']); ?></p>
      <div class="tour-images">
        <?php foreach ($images as $image): ?>
          <img src="../upload/Tour Images/<?php echo htmlspecialchars($image['img']); ?>" alt="Tour Image" style="width: 100px; height: 100px;">
        <?php endforeach; ?>
      </div>
      <button id="myBtn">Book now</button>
  </main>

  <div id="myModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <span class="close">&times;</span>
        <h2>Book in <?php echo htmlspecialchars($tour['title']) ?> </h2>
      </div>
      <div class="modal-body">
        <form action="../php/booking.php" method="post">
          <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>" />
          <input type="hidden" name="tour_id" value="<?php echo $tour['id']; ?>" />
          <label for="phone">Phone:</label><br>
          <input type="tel" id="phone" name="phone" required><br>
          <label for="date">Date:</label><br>
          <input type="date" id="date" name="date" required><br>
          <label for="time">Time:</label><br>
          <input type="time" id="time" name="time" required><br>
          <label for="people">Number of people:</label><br>
          <input type="number" id="people" name="people" min="1" required><br>
          <input type="submit" value="Book now">
        </form>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      mapboxgl.accessToken = 'pk.eyJ1Ijoibmlrb2xhaTEyMjIiLCJhIjoiY2x6d3pva281MGx6ODJrczJhaTJ4M2RmYyJ9.0sJ2ZGR2xpEza2j370y3rQ';

      const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v12',
        center: [<?php echo htmlspecialchars($tour['longitude']); ?>, <?php echo htmlspecialchars($tour['latitude']); ?>],
        zoom: 12,
        interactive: false
      });

      const markerElement = document.createElement('div');
      markerElement.className = 'marker';
      markerElement.style.backgroundImage = 'url(../assets/icons/<?php echo htmlspecialchars(strtok($tour['type'], " ")); ?>.png)';

      const marker = new mapboxgl.Marker(markerElement)
        .setLngLat([<?php echo htmlspecialchars($tour['longitude']); ?>, <?php echo htmlspecialchars($tour['latitude']); ?>])
        .addTo(map);

      map.dragPan.disable();
      map.scrollZoom.disable();
      map.touchZoomRotate.disable();
      map.rotate.disable();
    });


    const Toast = Swal.mixin({
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
      }
    });
    <?php if ($status === 'success'): ?>
      Toast.fire({
        icon: "success",
        title: "Booking successfully made!"
      });
    <?php elseif ($status === 'error'): ?>
      Toast.fire({
        icon: "error",
        title: "Error occured while booking."
      });
    <?php endif; ?>

    var modal = document.getElementById("myModal");
    var btn = document.getElementById("myBtn");
    var span = document.getElementsByClassName("close")[0];

    btn.onclick = function() {
      modal.style.display = "block";
    }
    span.onclick = function() {
      modal.style.display = "none";
    }
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }


  </script>
  <script>
let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}    
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";  
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";  
  dots[slideIndex-1].className += " active";
}
</script>

</body>

</html>