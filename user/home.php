<?php
session_start();
include("../func/user_func.php");

$tours = getAllTours($conn);
?>
<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

  <?php include('inc/topnav.php'); ?>

  <main>
    <div class="content">
      <header class="recomendation">
        <?php
        $tab = 1;
        foreach ($tours as $tour) { ?>
          <div id="tab<?php echo $tab; ?>" class="tab <?php echo $tab === 1 ? 'active' : ''; ?>">
            <div class="tabcontainer">
              <div class="details">
                <h2><?php echo $tour['title'] ?></h2>
                <p><?php echo $tour['description'] ?></p>
              </div>
              <div>
                <img src="../upload/Tour Images/<?php echo $tour['img'] ?>" alt="">
              </div>
            </div>
          </div>
        <?php
          $tab++;
        }
        ?>
        <div class="circle-container">
          <?php for ($i = 1; $i < $tab; $i++) { ?>
            <button class="circle-button" onclick="showTab(<?php echo $i; ?>)"></button>
          <?php } ?>
        </div>

      </header>
      <div class="head">Tours</div>
      <div class="cardmain">
        <?php
        foreach ($tours as $tour) {
          $average_rating = getAverageRating($conn, $tour['id']); ?>
          <a class="card" href="tour?tours=<?php echo $tour['id'] ?>">
            <img class="resorts" src="../upload/Tour Images/<?php echo $tour['img'] ?>" alt="">
            <h2><?php echo $tour['title'] ?></h2>
            <div class="logo" style="display: flex; align-items: center;">
              <img src="../assets/icons/<?php echo htmlspecialchars(strtok($tour['type'], " "), ENT_QUOTES, 'UTF-8'); ?>.png" alt="icon" style="width: 25px; height: 25px; margin-right: 10px;">
              <h6 style="margin: 0;">
                <?php echo htmlspecialchars($tour['type'], ENT_QUOTES, 'UTF-8'); ?>
                <h7 class="rating">⭐<?php echo number_format($average_rating, 1) ?></h7>
              </h6>
            </div>

            <p><?php echo $tour['description'] ?></p>
          </a>
        <?php }
        ?>
      </div>

    </div>
    <div class="topdestination">
      <h1>Popular</h1>
      <div class="btn">
        <button>Weekly</button>
        <button>Monthly</button>
      </div>
      <div class="popular">
        <div class="destination">
          <div class="Popcontent">
            <img src="../assets/gallery-1.jpg" alt="">
            <div class="details">
              <h2>name:</h2>
              <p>Type:</p>
            </div>
          </div>
          </div>
          <div class="destination">
          <div class="Popcontent">
            <img src="../assets/gallery-1.jpg" alt="">
            <div class="details">
              <h2>tan juan</h2>
              <p>Type:</p>
              <p>historical site</p>
            </div>
          </div>
          </div>
          <div class="destination">
          <div class="Popcontent">
            <img src="../assets/gallery-1.jpg" alt="">
            <div class="details">
              <h2>name:</h2>
              <p>Type:</p>
            </div>
          </div>
          </div>
        </div>
        
  </main>

<footer class="footer-distributed">

<div class="footer-left">

  <img src="../assets/kapbat.png" alt="">

  <p class="footer-links">
    <a href="#" class="link-1">Home</a>
    
    <a href="#">About</a>
    
    <a href="#">Faq</a>
    
    <a href="#">Contact</a>
  </p>

  <p class="footer-company-name">Company Name © 2015</p>
</div>

<div class="footer-center">


  <div>
    <i class="fa fa-phone"></i>
    <p>+1.555.555.5555</p>
  </div>

  <div>
    <i class="fa fa-envelope"></i>
    <p><a href="mailto:support@company.com">kapitanbato24@gmail.com</a></p>
  </div>

</div>

<div class="footer-right">

  <p class="footer-company-about">
    <span>About BagoTours.</span>
    Lorem ipsum dolor sit amet, consectateur adispicing elit. Fusce euismod convallis velit, eu auctor lacus vehicula sit amet.
  </p>

  <div class="footer-icons">

    <a href="#"><i class="fa fa-facebook"></i></a>
    <a href="#"><i class="fa fa-twitter"></i></a>
    <a href="#"><i class="fa fa-linkedin"></i></a>
    <a href="#"><i class="fa fa-github"></i></a>

  </div>

</div>

</footer>

<script>
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
