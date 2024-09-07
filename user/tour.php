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
    /* Resort Details Container */
/* Resort Details Container */
/* Resort Details Container */
.resortDetails {
  display: flex;
  flex-wrap: wrap; /* Allows wrapping on smaller screens */
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 10px;
  background-color: #fff;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  gap: 20px; /* Space between the map and information container */
}

/* Map Styling */
#map {
  flex: 1;
  min-width: 250px; /* Minimum width for responsiveness */
  height: 500px;
  border-radius: 10px;
}

/* Resort Information Container */
.infocont {
  flex: 1;
  position: relative;
  background-color: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 20px;
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  min-width: 250px; /* Minimum width for responsiveness */
}

/* Resort Image Styling */
.infocont img {
  width: 100%;
  height: auto;
  object-fit: cover;
  margin-bottom: 10px;
  border-radius: 5px;
}

/* Resort Information Styling */
.infocont h2 {
  margin: 10px 0;
  font-size: 1.8em;
}

.infocont p {
  margin: 5px 0;
}

/* Tour Images and Button Styling */
.tour-images {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  justify-content: center;
  margin-top: 15px;
}

.tour-images img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 5px;
  transition: transform 0.3s;
}

.tour-images img:hover {
  transform: scale(1.1);
}

.book-btn {
  margin-top: 10px;
  padding: 10px 20px;
  background-color: #007bff;
  color: #fff;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.book-btn:hover {
  background-color: #0056b3;
}

/* Media Queries for Responsiveness */
@media (max-width: 768px) {
  .resortDetails {
    flex-direction: column; /* Stack elements vertically on smaller screens */
    align-items: center; /* Center the content */
  }

  #map, .infocont {
    width: 100%; /* Full width for map and infocont on small screens */
    min-width: unset; /* Remove the minimum width */
  }
}

@media (max-width: 480px) {
  .infocont h2 {
    font-size: 1.5em; /* Slightly smaller font size for titles on very small screens */
  }

  .book-btn {
    padding: 8px 16px; /* Smaller padding for the button */
  }
}

    
  </style>
</head>

<body>

  <?php include('inc/topnav.php'); ?>
  
  <main>
    <div class="title">
      <h2>BaGoTours</h2>
      <p> => </p>
      <h2><?php echo htmlspecialchars($tour['title']);?></h2>
      <button class="split" id="myBtn">Book Now!</button>
    </div>
    <div class="slideshow-container">
  <!-- Slides -->
  <div class="mySlides fade">
    <div class="numbertext">1 / 3</div>
    <img src="../assets/gallery-1.jpg" alt="Gallery Image 1" >
    <div class="text"><?php echo htmlspecialchars($tour['title']);?></div>
  </div>

  <div class="mySlides fade">
    <div class="numbertext">2 / 3</div>
    <img src="../assets/about.png" alt="Gallery Image 2" >
    <div class="text"><?php echo htmlspecialchars($tour['title']);?></div>
  </div>

  <div class="mySlides fade">
    <div class="numbertext">3 / 3</div>
    <img src="../assets/gallery-3.jpg" alt="Gallery Image 3" >
    <div class="text"><?php echo htmlspecialchars($tour['title']);?></div>
  </div>

  <!-- Navigation arrows -->
  <button class="prev" onclick="plusSlides(-1)" aria-label="Previous slide">❮</button>
  <button class="next" onclick="plusSlides(1)" aria-label="Next slide">❯</button>
</div>

<!-- Dots for navigation -->
<div style="text-align:center">
  <button class="dot" onclick="currentSlide(1)" aria-label="Slide 1"></button> 
  <button class="dot" onclick="currentSlide(2)" aria-label="Slide 2"></button> 
  <button class="dot" onclick="currentSlide(3)" aria-label="Slide 3"></button> 
</div>

  <br>
  
  <div class="resortDetails">
  <!-- Map container -->
    <div id='map' ></div>

    <!-- Resort image -->
    <div class="infocont">
      <img src="../upload/Tour Images/<?php echo htmlspecialchars($tour['img']); ?>" alt="<?php echo htmlspecialchars($tour['title']); ?>">

<!-- Resort title and rating -->
      <h2><?php echo htmlspecialchars($tour['title']); ?></h2>
      <p>⭐<?php echo number_format($average_rating, 1) ?></p>
      <p><?php echo htmlspecialchars($tour['description']); ?></p>
      <div class="tour-images">
      <?php foreach ($images as $image): ?>
        <img src="../upload/Tour Images/<?php echo htmlspecialchars($image['img']); ?>" alt="Tour Image">
      <?php endforeach; ?>
      <button id="myBtn">Book now</button>
    </div>
    </div>
   
    <!-- Tour images and button -->
    
</div>

    
      
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
  <?php include ('inc/footer.php') ?>

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

// Next/previous controls
function plusSlides(n) {
  showSlides(slideIndex += n);
}

// Thumbnail image controls
function currentSlide(n) {
  showSlides(slideIndex = n);
}

// Function to display the slides
function showSlides(n) {
  let i;
  const slides = document.getElementsByClassName("mySlides");
  const dots = document.getElementsByClassName("dot");

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

// Pause slideshow on hover
const container = document.querySelector('.slideshow-container');
container.addEventListener('mouseover', () => clearInterval(autoSlide));
container.addEventListener('mouseleave', () => autoSlide = setInterval(() => plusSlides(1), 5000));

// Automatic slide change
let autoSlide = setInterval(() => plusSlides(1), 5000);

</script>

</body>

</html>