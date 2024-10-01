<?php
include("../func/user_func.php");
session_start();
$pageRole = "user";
require_once '../php/accValidation.php';

$id = isset($_GET["tours"]) ? intval($_GET["tours"]) : 0;
$status = isset($_GET["status"]) ? $_GET["status"] : '';

$tour = getTourById($conn, $id);
$images = getTourImages($conn, $id);
$average_rating = getAverageRating($conn, $id);

if (!$tour) {
  header('location: home');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">


  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">
  <script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
  <link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css">
  <title>BaGoTours. Tour</title>


  <style>
    .marker {
      background-size: cover;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      cursor: pointer;
    }

    /* The Modal (background) */
    .modal {
      display: none;
      /* Hidden by default */
      position: fixed;
      /* Stay in place */
      z-index: 1;
      /* Sit on top */
      left: 0;
      top: 0;
      width: 100%;
      /* Full width */
      height: 100%;
      /* Full height */
      overflow: auto;
      /* Enable scroll if needed */
      background-color: rgba(0, 0, 0, 0.5);
      /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
      background-color: #fff;
      margin: 10% auto;
      /* 10% from the top and centered */
      padding: 20px;
      border: 1px solid #888;
      width: 90%;
      /* Default for smaller screens */
      max-width: 500px;
      /* Limit the max width for larger screens */
      border-radius: 8px;
    }

    /* Modal Header */
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #e5e5e5;
    }

    /* Close Button */
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
      cursor: pointer;
    }

    /* Responsive form elements */
    .modal-body input[type="text"],
    .modal-body input[type="tel"],
    .modal-body input[type="date"],
    .modal-body input[type="time"],
    .modal-body input[type="number"] {
      width: 100%;
      padding: 10px;
      margin: 5px 0 15px 0;
      display: inline-block;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }

    /* Submit button */
    .modal-body input[type="submit"] {
      width: 100%;
      background-color: #4CAF50;
      color: white;
      padding: 12px 20px;
      margin: 8px 0;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .modal-body input[type="submit"]:hover {
      background-color: #45a049;
    }

    /* Responsive behavior */
    @media screen and (max-width: 768px) {
      .modal-content {
        width: 95%;
        /* Slightly smaller on smaller screens */
      }
    }

    @media screen and (max-width: 480px) {
      .modal-content {
        width: 100%;
        margin: 5% auto;
        /* More space for mobile devices */
      }

      .modal-header h2 {
        font-size: 18px;
      }

      .modal-body input[type="submit"] {
        padding: 10px;
      }
    }


    .title {
      margin: auto;
      align-items: center;
      display: grid;
      width: 100%;

    }

    .title h2 {
      font-size: 24px;
      font-weight: bold;
      color: black;

    }

    .book-btn,
    .book-now-btn {
      margin-top: 10px;
      padding: 10px 20px;
      background-color: #007bff;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .resortDetails {
      display: flex;
      flex-wrap: wrap;
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 10px;
      background-color: #fff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      gap: 20px;
    }

    #map {
      flex: 1;
      min-width: 250px;
      height: 500px;
      border-radius: 10px;
    }

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
      min-width: 250px;
    }

    .infocont img {
      width: 100%;
      height: auto;
      object-fit: cover;
      margin-bottom: 10px;
      border-radius: 5px;
    }

    .infocont h2 {
      margin: 10px 0;
      font-size: 1.8em;
    }

    .infocont p {
      margin: 5px 0;
    }

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

    .book-btn {
      padding: 8px 16px;
      /* Smaller padding for the button */
    }


    .title {
      overflow: hidden;
      background-color: hsl(0, 5%, 34%);
      display: flex;
      margin-top: 10px;
      margin-bottom: 10px;
      position: relative;

    }

    .title h2 {
      float: left;
      color: #f2f2f2;
      text-align: center;
      padding: 14px 16px;
      text-decoration: none;
      font-size: 17px;
    }

    .title p {
      float: left;
      color: #f2f2f2;
      text-align: center;
      margin-top: 30px;
      text-decoration: none;
      font-size: 17px;
    }

    .title h2 {

      color: rgb(235, 233, 233);
    }

    /* Create a right-aligned (split) link inside the navigation bar */

    .title .split {
      position: absolute;
      right: 0;
      top: 50%;
      transform: translateY(-50%);
      background-color: #0444aa;
      color: white;
      padding: 10px 13px;
      margin-right: 5px;
      border: none;
      cursor: pointer;
      font-size: 17px;
      border-radius: 10px;
      transition: background-color 0.3s;
    }

    .split:hover {
      background-color: #496c8f;
      color: white;
      text-decoration: none;
      transition: background-color 0.3s;

    }

    /* image slide */


    /* Slideshow container */
    /* Slideshow Container */
    .slideshow-container {
      position: relative;
      max-width: 100%;
      margin: auto;
      overflow: hidden;
    }

    /* Slides */
    .mySlides {
      display: none;
      background-color: #f9f9f9;
      padding: 10px;
      transition: opacity 1s ease;
      opacity: 0;
      transition: transform 0.5s ease;

    }

    .mySlides img {
      object-fit: cover;
      width: 100%;
      height: auto;

    }

    /* Navigation Buttons */
    .prev,
    .next {
      cursor: pointer;
      position: absolute;
      top: 50%;
      width: auto;
      padding: 16px;
      margin-top: -22px;
      color: rgb(18, 17, 17);
      font-weight: bold;
      font-size: 18px;
      border-radius: 0 3px 3px 0;
      user-select: none;
      background-color: rgba(0, 0, 0, 0.5);
    }

    .next {
      right: 0;
      border-radius: 3px 0 0 3px;
    }

    .prev:hover,
    .next:hover {
      background-color: rgba(0, 0, 0, 0.8);
    }

    /* Dots */
    .dot-container {
      text-align: center;
      padding: 10px;
      background: rgba(0, 0, 0, 0.5);
    }

    .dot {
      cursor: pointer;
      height: 15px;
      width: 15px;
      margin: 0 2px;
      background-color: #bbb;
      border-radius: 50%;
      display: inline-block;
      transition: background-color 0.6s ease;
    }

    .active,
    .dot:hover {
      background-color: #717171;
    }


    @keyframes fade {
      from {
        opacity: .4
      }

      to {
        opacity: 1
      }
    }

    /* On smaller screens, decrease text size */
    @media only screen and (max-width: 300px) {

      .prev,
      .next,
      .text {
        font-size: 11px
      }
    }

    /* RESORT DEATILS */



    .book-btn:hover {
      background-color: #0056b3;
    }

    @media (max-width: 768px) {
      .resortDetails {
        flex-direction: column;
        align-items: center;
      }

      #map,
      .infocont {
        width: 100%;
        min-width: unset;
      }
    }

    @media (max-width: 480px) {
      .infocont h2 {
        font-size: 1.5em;
      }

      .book-btn {
        padding: 8px 16px;
      }
    }
  </style>
</head>

<body>

  <?php include('inc/topnav.php'); ?>

  <main>
    <div class="title">
      <h2><?php echo htmlspecialchars($tour['title']); ?></h2>
    </div>

    <div class="resortDetails">
      <div id='map'></div>

      <div class="infocont">
        <img src="../upload/Tour Images/<?php echo htmlspecialchars($tour['img']); ?>" alt="<?php echo htmlspecialchars($tour['title']); ?>">

        <h2><?php echo htmlspecialchars($tour['title']); ?></h2>
        <p>⭐<?php echo number_format($average_rating, 1) ?></p>
        <p><?php echo htmlspecialchars($tour['description']); ?></p>
        <div class="tour-images">
          <?php foreach ($images as $image): ?>
            <img src="../upload/Tour Images/<?php echo htmlspecialchars($image['img']); ?>" alt="Tour Image">
          <?php endforeach; ?>
        </div>
        <div class="btn">
          <button class="go-here-btn">Go Here</button>
          <button class="book-now-btn">Book now</button>
        </div>
      </div>
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
          <input type="tel" id="phone" name="phone" required pattern="^(09|\+639)\d{9}$" placeholder="e.g. 09123456789 or +639123456789"><br>
          <label for="date">Date:</label><br>
          <input type="date" id="date" name="date" required><br>
          <label for="people">Number of people:</label><br>
          <input type="number" id="people" name="people" min="1" max="50" required><br>
          <input type="submit" value="Book now">
        </form>
      </div>
    </div>
  </div>

  <?php include('inc/footer.php') ?>

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

      const modal = document.getElementById("myModal");
      const modalBtn = document.querySelector(".book-now-btn");
      const closeBtn = document.querySelector(".close");

      modalBtn.onclick = function() {
        modal.style.display = "block";
      };

      closeBtn.onclick = function() {
        modal.style.display = "none";
      };

      window.onclick = function(event) {
        if (event.target == modal) {
          modal.style.display = "none";
        }
      };
    });
    const dateInput = document.getElementById('date');
    const today = new Date();
    const minDate = new Date();
    minDate.setDate(today.getDate() + 3);

    const formatDate = (date) => {
      return date.toISOString().split('T')[0];
    };
    dateInput.setAttribute('min', formatDate(minDate));
    const phoneInput = document.getElementById('phone');

    phoneInput.addEventListener('input', function() {
      const phonePattern = /^(09|\+639)\d{9}$/;
      if (!phonePattern.test(phoneInput.value)) {
        phoneInput.setCustomValidity('Please enter a valid Philippine phone number.');
      } else {
        phoneInput.setCustomValidity('');
      }
    });

    <?php if (isset($_GET["success"])) : ?>
      Swal.fire({
        title: '<?php echo $_GET["success"]; ?>',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
      });
    <?php endif; ?>
  </script>
</body>

</html>