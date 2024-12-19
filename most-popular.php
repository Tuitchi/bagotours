<?php
session_start();
require 'include/db_conn.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

$time_filter = '1 DAY'; // Default filter
if (isset($_POST['filter'])) {
    $time_filter = $_POST['filter'];
}

$sql = "
WITH booking_visitors AS (
    SELECT t.id, 
           SUM(b.people) AS total_booking_visitors
    FROM tours t
    LEFT JOIN booking b ON t.id = b.tour_id AND b.status = 4
    WHERE b.start_date >= DATE_SUB(NOW(), INTERVAL $time_filter)
    GROUP BY t.id
),
visit_visitors AS (
    SELECT t.id, 
           COUNT(DISTINCT v.id) AS total_visit_visitors
    FROM tours t
    LEFT JOIN visit_records v ON t.id = v.tour_id
    WHERE v.visit_time >= DATE_SUB(NOW(), INTERVAL $time_filter)
    GROUP BY t.id
),
review_data AS (
    SELECT t.id, 
           IFNULL(AVG(r.rating), 0) AS average_rating,  -- Average rating
           IFNULL(COUNT(r.id), 0) AS review_count       -- Review count
    FROM tours t
    LEFT JOIN review_rating r ON t.id = r.tour_id
    WHERE r.date_created >= DATE_SUB(NOW(), INTERVAL $time_filter)  -- Filter reviews within the time frame
    GROUP BY t.id
)

SELECT t.id, 
       t.title,
       t.img,
       t.type,
       COALESCE(bv.total_booking_visitors, 0) + COALESCE(vv.total_visit_visitors, 0) AS total_visitors,
       COUNT(DISTINCT b.id) AS total_completed_bookings,
       rd.average_rating,
       rd.review_count
FROM tours t
LEFT JOIN booking b ON t.id = b.tour_id AND b.status = 4
LEFT JOIN booking_visitors bv ON t.id = bv.id
LEFT JOIN visit_visitors vv ON t.id = vv.id
LEFT JOIN review_data rd ON t.id = rd.id
WHERE t.status IN ('Active','Temporarily Closed')
GROUP BY t.id, rd.average_rating, rd.review_count, bv.total_booking_visitors, vv.total_visit_visitors;
";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $max_visitors = max(array_column($tours, 'total_visitors'));
    $max_bookings = max(array_column($tours, 'total_completed_bookings'));
    $max_rating = 5;
    $max_reviews = max(array_column($tours, 'review_count'));

    $max_visitors = ($max_visitors > 0) ? $max_visitors : 1;
    $max_bookings = ($max_bookings > 0) ? $max_bookings : 1;
    $max_reviews = ($max_reviews > 0) ? $max_reviews : 1;

    $weighted_tours = [];
    $total_visitors_weight = 0.2;
    $completed_bookings_weight = 0.2;
    $rating_weight = 0.5;
    $review_weight = 0.1;

    foreach ($tours as $tour) {
        $normalized_visitors = $tour['total_visitors'] / $max_visitors;
        $normalized_bookings = $tour['total_completed_bookings'] / $max_bookings;
        $normalized_rating = $tour['average_rating'] / $max_rating;
        $normalized_reviews = $tour['review_count'] / $max_reviews;

        $weighted_score = ($normalized_visitors * $total_visitors_weight) +
            ($normalized_bookings * $completed_bookings_weight) +
            ($normalized_rating * $rating_weight) +
            ($normalized_reviews * $review_weight);

        $tour['weighted_score'] = $weighted_score;
        $weighted_tours[] = $tour;
    }
    usort($weighted_tours, function ($a, $b) {
        if ($b['average_rating'] == 5 && $a['average_rating'] < 5) {
            return 1;
        } elseif ($a['average_rating'] == 5 && $b['average_rating'] < 5) {
            return -1;
        }
        return $b['weighted_score'] <=> $a['weighted_score'];
    });

    $top_tours = array_slice($weighted_tours, 0, 15);
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
    <link rel="stylesheet" href="user.css">
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo $webIcon ?>">
    <link rel="stylesheet" href="assets/css/login.css">
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
                foreach ($top_tours as $tour) {
                    $averageRating = isset($tour['average_rating']) && $tour['average_rating'] !== null ? round($tour['average_rating']) : 0;
                    $fullStars = str_repeat("★", $averageRating);
                    $emptyStars = str_repeat("☆", 5 - $averageRating);
                    $totalStars = $fullStars . $emptyStars;
                    $tour_images = explode(',', $tour['img']);
                    $main_image = $tour_images[0];

                    $backgroundImage = "upload/Tour Images/" . $main_image;
                    echo "<a href='tour?id=" . base64_encode($tour['id'] . $salt) . "'>
                <div class='tourList' data-bg='$backgroundImage'>
                    <img src='upload/Tour Images/" . $main_image . "' alt=''>
                    <div class='tourDetails'>
                        <h1>#" . $counter++ . "</h1>
                        <h3>" . htmlspecialchars($tour['title']) . "</h3>
                        <div class='smallDetails'>
                            <span>" . htmlspecialchars($tour['type']) . "</span>
                            <span class='rating'>" . $totalStars . " (" . ($tour['review_count']) . " reviews)</span>
                            <span class='rating'>" . htmlspecialchars($tour['total_visitors']) . " Visitors</span>
                        </div>
                    </div>
                </div>
              </a>";
                }
                ?>
            </div>
        </div>
    </div>
    <?php require "include/login-registration.php"; ?>

    <script src="index.js"></script>
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script>
        function applyBackgroundImages() {
            $('style[data-index]').remove();

            $('.tourList').each(function(index) {
                const bgImage = $(this).data('bg');
                if (bgImage) {
                    const uniqueClass = `tourList-bg-${index}`;
                    $(this).addClass(uniqueClass);

                    const style = document.createElement('style');
                    style.setAttribute('data-index', index);
                    style.textContent = `
                    .${uniqueClass}::before {
                        background-image: url('${bgImage}');
                    }
                `;
                    document.head.appendChild(style);
                }
            });
        }

        $(document).ready(function() {
            applyBackgroundImages();

            $('.option').on('click', function() {
                $('.option').removeClass('active');
                $(this).addClass('active');

                var timeFilter = $(this).data('filter');

                $.ajax({
                    url: '',
                    type: 'POST',
                    data: {
                        filter: timeFilter
                    },
                    success: function(response) {
                        $('#tour-list-container').html($(response).find('#tour-list-container').html());
                        applyBackgroundImages();
                    },
                    error: function() {
                        alert('Error loading tours');
                    }
                });
            });
        });
    </script>


</body>

</html>
