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
  <style>
    .cardmain a {
      display: block;
      color: black;
      text-align: center;
      padding: 14px 16px;
      text-decoration: none;
      border: none;
      outline: none;
      font-size: 17px;
      transition: 0.3s;
    }

    .rating {
      position: absolute;
      right: 50px;
    }

    .card p {
      text-align: justify;
      display: -webkit-box;
      -webkit-line-clamp: 4;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  </style>
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
      <h1>popular</h1>
      <div>
        <button>weekly</button>
        <button>monthly</button>
      </div>
      <div class="popular">
        <div class="destination">
          <img src="" alt="">
          <div class="details">
            <h2>name:</h2>
            <p>description:</p>
          </div>
        </div>
        <div class="destination">
          <img src="" alt="">
          <div class="details">
            <h2>name:</h2>
            <p>description:</p>
          </div>
        </div>
        <div class="destination">
          <img src="" alt="">
          <div class="details">
            <h2>name:</h2>
            <p>description:</p>
          </div>
        </div>
        <div class="destination">
          <img src="" alt="">
          <div class="details">
            <h2>name:</h2>
            <p>description:</p>
          </div>
        </div>
      </div>
    </div>

  </main>

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