<?php
include("../func/user_func.php");
session_start();

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


  <link rel="stylesheet" href="assets/css/tours.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    .tour-images img:hover {
      transform: scale(1.1);
    }


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
        <?php 
        $sql = 'SELECT is_verified FROM users WHERE id = ' . $_SESSION['user_id'];
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $verified = $row['is_verified'];
        ?>
        <?php if ($verified == false) { ?>
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
        <?php } else { ?>
          <p>Please verify your account to book in this resort.</p>
          <a href="verify.php">Verify your account</a>
        <?php } ?>
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