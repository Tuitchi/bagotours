<?php session_start();
require 'include/db_conn.php';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

try {
    $stmt = $conn->prepare("SELECT * FROM events WHERE event_date_end >= DATE_SUB(CURDATE(), INTERVAL 3 DAY)");
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo $webIcon ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BagoTours</title>
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <style>
        .booking {
            display: flex;
            flex-direction: column;
            padding: 16px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 1200px;
        }

        .booking h3 {
            font-size: 18px;
            color: #333;
        }

        .booking img {
            width: 250px;
            margin: auto;
            font-size: 24px;
            color: #333;
        }

        .booking h2 {
            margin: 0 0 20px;
            font-size: 24px;
            color: #333;
        }

        .desc p {
            margin-bottom: 20px;
        }

        .carousel-item img {
            width: 77vw;
            object-fit: cover;
        }

        .spots {
            width: 100%;
            height: 300px;
            position: relative;
        }

        .spots img {
            margin: auto;
            width: 100%;
            height: 100%;
            border-radius: 10px;
            object-fit: cover;
        }

        .spots .spot-details {
            position: absolute;
            width: 98%;
            height: 94%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            color: #fff;
            border-radius: 10px;
        }

        .spots .spot-details .lower {
            position: absolute;
            bottom: 20px;
            font-weight: 500;
        }

        .spots .spot-details .upper {
            text-align: center;
        }

        .view {
            width: 20%;
            text-align: center;
            padding: 5px;
            background-color: #fff;
            color: black;
            font-size: 1rem;
            position: absolute;
            bottom: 20px;
            right: 20px;
        }

        .desc {
            text-align: justify;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            line-clamp: 3;
            -webkit-box-orient: vertical;
        }
    </style>
</head>

<body>
    <?php include 'nav/topnav.php' ?>

    <div class="main-container">

        <?php include 'nav/sidenav.php' ?>
        <div class="main">
            <?php if ($events) { ?>
                <div class="carousel-container">
                    <button class="prev" onclick="prevSlide()">&#10094;</button>
                    <button class="next" onclick="nextSlide()">&#10095;</button>
                    <div class="carousel-slide">
                        <?php require_once 'func/user_func.php';
                        foreach (array_slice($events, 0, 3) as $event) {
                            echo "<div class='carousel-item'>
                        <a href='view-event?event=" . base64_encode($event['event_code'] . $salt) . "'>
                        <img src='upload/Event/" . $event['event_image'] . "' alt='" . $event['event_name'] . "'>
                        <div class='carousel-caption'>
                            <h3>" . $event['event_name'] . "</h3>
                            <p>" . $event['event_type'] . "</p>
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
                    <h2>Discover Events</h2>
                    <?php foreach ($events as $event) { ?>
                        <a href="view-event?event=<?php echo base64_encode($event['event_code'] . $salt) ?>">
                            <div class="spots">
                                <img src="upload/Event/<?php echo $event['event_image'] ?>" alt="">
                                <div class="spot-details">
                                    <div class="upper">
                                        <h1><?php echo $event['event_name'] ?></h1>
                                        <p><strong><?php echo $event['event_type'] ?></strong></p>
                                    </div>
                                    <p class="desc"><?php echo $event['event_description'] ?></p>
                                    <div class="lower">
                                        <p>📅 <?php echo date('F d, Y', strtotime($event['event_date_start'])) ?> -
                                            <?php echo date('F d, Y', strtotime($event['event_date_end'])) ?>
                                        </p>
                                        <p>📍 <?php echo $event['event_location'] ?></p>
                                    </div>
                                </div>
                                <div class="view">click to view</div>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="booking">
                    <h2>No Event.</h2>
                    <img src="assets/booking-empty.png" alt="No bookings found" style="max-width: 100%; height: auto;">
                    <div class="desc">
                        <p>At this time, there are no events scheduled. Please visit again later for updates.</p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php require "include/login-registration.php"; ?>
    <script src="index.js"></script>
    <script>
        function getRandomColorWithOpacity() {
            const r = Math.floor(Math.random() * 128);
            const g = Math.floor(Math.random() * 128);
            const b = Math.floor(Math.random() * 128);
            return `rgba(${r}, ${g}, ${b}, 0.6)`;
        }

        const spotDetails = document.querySelectorAll('.spot-details');
        spotDetails.forEach(spot => {
            spot.style.backgroundColor = getRandomColorWithOpacity();
        });
    </script>
</body>

</html>