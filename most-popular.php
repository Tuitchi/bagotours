<?php
session_start();
require 'include/db_conn.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

$time_filter = '30 DAY';
if (isset($_POST['filter'])) {
    $time_filter = $_POST['filter'];
}

$sql = "WITH booking_visitors AS (
    SELECT t.id, 
           SUM(b.people) AS total_booking_visitors
    FROM tours t
    LEFT JOIN booking b ON t.id = b.tour_id AND b.status = 4
    WHERE b.date_sched >= DATE_SUB(NOW(), INTERVAL $time_filter)
    GROUP BY t.id
),
visit_visitors AS (
    SELECT t.id, 
           COUNT(DISTINCT v.id) AS total_visit_visitors
    FROM tours t
    LEFT JOIN visit_records v ON t.id = v.tour_id
    WHERE v.visit_time >= DATE_SUB(NOW(), INTERVAL $time_filter)
    GROUP BY t.id
)

SELECT t.id, 
       t.title,
       t.img,
       t.type,
       COALESCE(bv.total_booking_visitors, 0) + COALESCE(vv.total_visit_visitors, 0) AS total_visitors,
       COUNT(DISTINCT b.id) AS total_completed_bookings
FROM tours t 
LEFT JOIN booking b ON t.id = b.tour_id AND b.status = 4
LEFT JOIN booking_visitors bv ON t.id = bv.id
LEFT JOIN visit_visitors vv ON t.id = vv.id
WHERE t.status = 1
GROUP BY t.id, t.title, bv.total_booking_visitors, vv.total_visit_visitors
ORDER BY total_visitors DESC, total_completed_bookings DESC;";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BagoTours</title>
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="user.css">
</head>

<body>
    <?php include 'nav/topnav.php' ?>
    <div class="main-container">
        <?php include 'nav/sidenav.php' ?>
        <div class="main">
            <h1>Most Popular</h1>
            <button class="option active" data-filter="1 DAY">Last 24h</button>
            <button class="option" data-filter="7 DAY">Last 7 days</button>
            <button class="option" data-filter="30 DAY">Last 30 days</button>

            <div id="tour-list-container">
                <?php
                $counter = 1;
                foreach ($tours as $tour) {
                    echo "<a href='tour?id=" . base64_encode($tour['id'] . $salt) . "'>
                            <div class='tourList'>
                                <img src='upload/Tour Images/" . $tour['img'] . "' alt=''>
                                <div class='tourDetails'>
                                    <h1>#" . $counter++ . "</h1>
                                    <h3>" . $tour['title'] . "</h3>
                                    <div class='smallDetails'>
                                        <span>" . $tour['type'] . "</span>
                                        <span class='rating'>★★★★☆ (" . $tour['total_visitors'] . ")</span>
                                    </div>
                                </div>
                            </div>
                          </a>";} ?>
            </div>
        </div>
    </div>
    <?php require "include/login-registration.php"; ?>

    <script src="index.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.option').on('click', function () {
                $('.option').removeClass('active');
                $(this).addClass('active');

                var timeFilter = $(this).data('filter');

                $.ajax({
                    url: '',
                    type: 'POST',
                    data: { filter: timeFilter },
                    success: function (response) {
                        $('#tour-list-container').html($(response).find('#tour-list-container').html());
                    },
                    error: function () {
                        alert('Error loading tours');
                    }
                });
            });
        });
    </script>
</body>

</html>