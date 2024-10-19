<?php session_start();
require 'include/db_conn.php';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
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
</head>

<body>
    <?php include 'nav/topnav.php' ?>
   
    <div class="main-container">
       
        <?php include 'nav/sidenav.php' ?>
        <div class="main">

            <div class="searchbar2">
                <input type="text" name="" id="" placeholder="Search">
                <div class="searchbtn">
                    <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210180758/Untitled-design-(28).png"
                        class="icn srchicn" alt="search-button">
                </div>
            </div>

            <div class="carousel-container">
                <button class="prev" onclick="prevSlide()">&#10094;</button>
                <button class="next" onclick="nextSlide()">&#10095;</button>
                <div class="carousel-slide">
                    <?php require_once 'func/user_func.php';
                    $tours = getAllTours($conn);
                    shuffle($tours);
                    foreach (array_slice($tours, 0, 3) as $tour) {
                        echo "<div class='carousel-item'>
                        <a href='tour?id=" . base64_encode($tour['id'] . $salt) . "'>
                        <img src='upload/Tour Images/" . $tour['img'] . "' alt='" . $tour['title'] . "'>
                        <div class='carousel-caption'>
                            <h3>" . $tour['title'] . "</h3>
                            <p>Type: " . $tour['type'] . "</p>
                        </div>
                        </a>
                    </div>";
                    } ?>
                </div>

                <div class="carousel-indicators">
                    <div class="active" onclick="goToSlide(0)"></div>
                    <div onclick="goToSlide(1)"></div>
                    <div onclick="goToSlide(2)"></div>
                </div>
            </div>
            <div class="popularspot">
                <h2>Trending</h2>
                <div class="spots">
                    <?php foreach ($tours as $tour) {
                        echo "<div class='spot'>
                        <a href='tour?id=" . base64_encode($tour['id'] . $salt) . "'>
                        <img src='upload/Tour Images/" . $tour['img'] . "' alt='" . $tour['title'] . "'>  
                        <h3>" . $tour['title'] . "</h3>
                        <p>" . $tour['type'] . "</p>
                        <div class='rating'>★★★★☆ <span>(156 reviews)</span>
                        </div>
                        </a>
                    </div>";
                    } ?>
                    <?php foreach ($tours as $tour) {
                        echo "<div class='spot'>
                        <a href='tour?id=" . base64_encode($tour['id'] . $salt) . "'>
                        <img src='upload/Tour Images/" . $tour['img'] . "' alt='" . $tour['title'] . "'>  
                        <h3>" . $tour['title'] . "</h3>
                        <p>" . $tour['type'] . "</p>
                        <div class='rating'>★★★★☆ <span>(156 reviews)</span>
                        </div>
                        </a>
                    </div>";
                    } ?>
                </div>

                <div class="report-container" id="cardContainer">
                    <?php foreach ($tours as $tour) {
                        echo "<div class='cards'>
                        <a href='tour?id=" . base64_encode($tour['id'] . $salt) . "' class='card'>
                        <img src='upload/Tour Images/" . $tour['img'] . "' alt='" . $tour['title'] . "'>  
                            <h2 class='title'>" . $tour['title'] . "</h2>
                        </a>
                    </div>";
                    } ?>
                    <?php foreach ($tours as $tour) {
                        echo "<div class='cards'>
                        <a href='tour?id=" . base64_encode($tour['id'] . $salt) . "' class='card'>
                        <img src='upload/Tour Images/" . $tour['img'] . "' alt='" . $tour['title'] . "'>  
                            <h2 class='title'>" . $tour['title'] . "</h2>
                        </a>
                    </div>";
                    } ?>
                    <?php foreach ($tours as $tour) {
                        echo "<div class='cards'>
                        <a href='tour?id=" . base64_encode($tour['id'] . $salt) . "' class='card'>
                        <img src='upload/Tour Images/" . $tour['img'] . "' alt='" . $tour['title'] . "'>  
                            <h2 class='title'>" . $tour['title'] . "</h2>
                        </a>
                    </div>";
                    } ?>
                    <?php foreach ($tours as $tour) {
                        echo "<div class='cards'>
                        <a href='tour?id=" . base64_encode($tour['id'] . $salt) . "' class='card'>
                        <img src='upload/Tour Images/" . $tour['img'] . "' alt='" . $tour['title'] . "'>  
                            <h2 class='title'>" . $tour['title'] . "</h2>
                        </a>
                    </div>";
                    } ?>
                    <?php foreach ($tours as $tour) {
                        echo "<div class='cards'>
                        <a href='tour?id=" . base64_encode($tour['id'] . $salt) . "' class='card'>
                        <img src='upload/Tour Images/" . $tour['img'] . "' alt='" . $tour['title'] . "'>  
                            <h2 class='title'>" . $tour['title'] . "</h2>
                        </a>
                    </div>";
                    } ?>
                    <?php foreach ($tours as $tour) {
                        echo "<div class='cards'>
                        <a href='tour?id=" . base64_encode($tour['id'] . $salt) . "' class='card'>
                        <img src='upload/Tour Images/" . $tour['img'] . "' alt='" . $tour['title'] . "'>  
                            <h2 class='title'>" . $tour['title'] . "</h2>
                        </a>
                    </div>";
                    } ?>
                    <?php foreach ($tours as $tour) {
                        echo "<div class='cards'>
                        <a href='tour?id=" . base64_encode($tour['id'] . $salt) . "' class='card'>
                        <img src='upload/Tour Images/" . $tour['img'] . "' alt='" . $tour['title'] . "'>  
                            <h2 class='title'>" . $tour['title'] . "</h2>
                        </a>
                    </div>";
                    } ?>
                </div>
                <div class="pagination" id="pagination"></div>
            </div>
        </div>
        <?php require "include/login-registration.php"; ?>
        <script src="index.js"></script>
</body>

</html>