<?php session_start();
require 'include/db_conn.php';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}
if (isset($_GET['id'])) {
    $decrypted_id_raw = base64_decode($_GET['id']);
    $decrypted_id = preg_replace(sprintf('/%s/', $salt), '', $decrypted_id_raw);

    $stmt = $conn->prepare("SELECT * FROM tours WHERE id = ?");
    $stmt->execute([$decrypted_id]); 
    $tour = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <style>
        .resdetails {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin: 10px;
    background-color: #f8f8f8;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.map {
    width: 100%;
    height: 100%;
    border-radius: 10px;
    overflow: hidden;
}

.resdetls {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.rescont {
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: #f8f8f8;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.resimg {
    width: 100%;
    max-width: 300px;
    height: auto;
    border-radius: 10px;
    margin-bottom: 15px;
}

.pricing-container {
    background-color: #e0e0e0;
    padding: 15px;
    border-radius: 10px;
    margin: 15px 0;
    width: 100%;
    max-width: 400px;
    cursor: pointer;
    text-align: center;
}

.pricing-header {
    font-size: 1.5rem;
    margin: 0;
}

.pricing-content {
    display: none; /* Hidden by default */
    margin-top: 10px;
}

.btons {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.bookbtn, .viewbtn {
    background-color: #010058af;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.bookbtn:hover, .viewbtn:hover {
    background-color: #45a049;
}

/* Responsive Design */
@media (min-width: 768px) {
    .resdetails {
        flex-direction: row;
    }

    .resdetls {
        flex: 1;
        margin-left: 20px;
    }

    .map {
        width: 50%;
        height: 450px;
    }
}
    </style>
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
            <div class="resdetails">
                <div class="map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3922.61615955203!2d122.84090677404929!3d10.530867863677159!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33aec63007585bb7%3A0x46a4d1fea7196baf!2sBago%20City%20College!5e0!3m2!1sen!2sph!4v1729254108994!5m2!1sen!2sph" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="resdetls">
                    <div class="rescont">
                        <img src="shire res.jpg" alt="" class="resimg">
                        <h2 class="title"><?php echo $tour['title']?></h2>
                        <p>Location: <?php echo $tour['address']?></p><p class="rating">⭐⭐⭐⭐</p>
                        <p class="details">
                        Description: <?php echo $tour['description']?>
                        </p>
                        <div class="pricing-container">
                            <h3 class="pricing-header">Price</h3>
                            <div class="pricing-content">
                                <h3>Entrance</h3>
                                <h5>P100/pax</h5>
                                <h5>Cottage Small</h5>
                                <h5>P200</h5>
                                <h5>Cottage Large</h5>
                                <h5>P500</h5>
                            </div>
                        </div>
                        <div class="btons">
                            <button class="bookbtn">Book Now</button>
                            <button class="viewbtn">Go Here</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="index.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
        document.querySelector('.pricing-header').addEventListener('click', function () {
    const pricingContent = document.querySelector('.pricing-content');
    pricingContent.style.display = pricingContent.style.display === 'none' || pricingContent.style.display === '' ? 'block' : 'none';
});

    </script>
</body>

</html>