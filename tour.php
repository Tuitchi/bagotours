<?php session_start();
require 'include/db_conn.php';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id =?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    header("Location: home?login=true");
    exit;
}
if (isset($_GET['id'])) {
    $decrypted_id_raw = base64_decode($_GET['id']);
    $decrypted_id = preg_replace(sprintf('/%s/', $salt), '', $decrypted_id_raw);

    $stmt = $conn->prepare("SELECT * FROM tours WHERE id = ?");
    $stmt->execute([$decrypted_id]);
    $tour = $stmt->fetch(PDO::FETCH_ASSOC);
}
require_once 'func/func.php';
$averageRating = getAverageRatingNew($conn, $decrypted_id);
$ratingStars = displayRatingStars($averageRating);
function timeAgo($timestamp)
{
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time_ago;

    $seconds = $time_difference;
    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    $weeks = round($seconds / 604800);
    $months = round($seconds / 2629440); // ~30.44 days
    $years = round($seconds / 31553280); // ~365.24 days

    // Determine the appropriate time frame and format
    if ($seconds < 60) {
        return ($seconds == 1) ? "one second ago" : "$seconds seconds ago";
    } elseif ($minutes < 60) {
        return ($minutes == 1) ? "one minute ago" : "$minutes minutes ago";
    } elseif ($hours < 24) {
        return ($hours == 1) ? "one hour ago" : "$hours hours ago";
    } elseif ($days < 7) {
        return ($days == 1) ? "one day ago" : "$days days ago";
    } elseif ($weeks < 4) {
        return ($weeks == 1) ? "one week ago" : "$weeks weeks ago";
    } elseif ($months < 12) {
        return ($months == 1) ? "one month ago" : "$months months ago";
    } else {
        return ($years == 1) ? "one year ago" : "$years years ago";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BagoTours</title>
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <style>
        /* Modal Background */
        .modal.booking {
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        /* Modal Content */
        .modal-content.booking {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 400px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
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
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        /* Form Styles */
        .modal-content form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .modal-content form label {
            font-weight: bold;
        }

        .modal-content form input,
        .modal-content form button {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .book-btn {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }

        .book-btn:hover {
            background-color: #218838;
        }

        .tour-container {
            background-color: #f8f8f8;
            padding: 10px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .resdetails {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin: 10px;
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
            display: none;
            /* Hidden by default */
            margin-top: 10px;
        }

        .btons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .viewbtn {
            display: inline-block;
            text-align: center;
            background-color: #ffffff;
            /* White background */
            color: #218838;
            /* Green text color */
            padding: 12px 24px;
            border: 2px solid #218838;
            /* Green border */
            border-radius: 8px;
            font-size: 1em;
            font-weight: bold;
            text-decoration: none;
            /* Removes underline */
            cursor: pointer;
            transition: background 0.3s, color 0.3s, transform 0.2s;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .viewbtn:hover {
            background: linear-gradient(135deg, #218838, #1e7e34);
            /* Green gradient on hover */
            color: #ffffff;
            /* White text color on hover */
            transform: translateY(-3px);
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.2);
        }

        .viewbtn:active {
            transform: translateY(-1px);
            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.15);
        }

        .bookbtn {
            background: linear-gradient(135deg, #005eff, #0200d4);
            color: #ffffff;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        .bookbtn:hover {
            background: linear-gradient(135deg, #007bff, #0100b3);
            transform: translateY(-3px);
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.3);
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

        /* comment section design.css */
        /* Container styling */
        .comment-section {
            width: 90%;
            margin: 20px auto;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Comment box styling */
        .comment-box {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .comment-box .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .comment-box .comment-input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
            resize: none;
        }

        .comment-box .comment-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .comment-box .comment-submit-btn {
            margin-left: 10px;
            padding: 8px 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .comment-box .comment-submit-btn:hover {
            background-color: #0056b3;
        }

        /* Comments list styling */
        .comments-list {
            margin-top: 10px;
        }

        .comment {
            display: flex;
            align-items: flex-start;
            padding: 10px 0;
            border-top: 1px solid #e0e0e0;
        }

        .comment .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .comment-content {
            flex: 1;
        }

        .comment-author {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
        }

        .comment-star .bx {
            font-size: 15px;
            color: #f5ce22;
        }

        .comment-text {
            margin: 5px 0;
            font-size: 13px;
            color: #333;
        }

        .comment-actions {
            font-size: 12px;
            color: #666;
            display: flex;
            gap: 15px;
        }

        .comment-actions .reply-btn {
            cursor: pointer;
            color: #007bff;
        }

        .comment-actions .reply-btn:hover {
            text-decoration: underline;
        }

        /* Show More button styling */
        .show-more-btn {
            display: block;
            /* Makes the button take up the entire line */
            margin: 15px auto;
            /* Centers the button */
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .show-more-btn:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            /* Slightly lift the button on hover */
        }

        .show-more-btn:active {
            background-color: #004494;
            transform: translateY(0);
            /* Return to the original position on click */
        }


        /* Responsive adjustments */
        @media (max-width: 480px) {
            .comment-box .comment-input {
                font-size: 14px;
            }

            .comment-author,
            .comment-text,
            .comment-actions {
                font-size: 12px;
            }
        }

        hr {
            border: none;
            border-top: 1px solid #ccc;
            margin: auto;
            margin-bottom: 15px;
            width: 80%;

        }

        .carousel-item img {
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body>
    <?php include 'nav/topnav.php' ?>
    <div class="main-container">
        <?php include 'nav/sidenav.php' ?>
        <div class="main">
            <div class="tour-container">
                <div class="tour-images">
                    <div class="carousel-container">
                        <button class="prev" onclick="prevSlide()">&#10094;</button>
                        <button class="next" onclick="nextSlide()">&#10095;</button>
                        <div class="carousel-slide">
                            <?php
                            require_once 'func/user_func.php';
                            $tour_images = explode(',', $tour['img']);
                            foreach ($tour_images as $tour_image) {
                                echo "<div class='carousel-item'>
                        <img src='upload/Tour Images/" . $tour_image . "' alt='" . $tour['title'] . "'>
                      </div>";
                            }
                            ?>
                        </div>

                        <div class="carousel-indicators">
                            <?php
                            foreach ($tour_images as $index => $tour_image) {
                                $activeClass = ($index === 0) ? 'active' : '';
                                echo "<div class='$activeClass' onclick='goToSlide($index)'></div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="resdetails">
                    <div id="map" class="map"></div>
                    <div class="resdetls">
                        <div class="rescont">
                            <h1 class="title"><?php echo $tour['title'] ?></h1>
                            <p>Location: <?php echo $tour['address'] ?></p>
                            <p class="rating"><?php echo $ratingStars; ?></p>
                            <p class="details">
                                Description: <?php echo $tour['description'] ?>
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
                                <?php if (isBookable($conn, $tour['id'])) {
                                    $status = checkBookingStatus($conn, $user_id, $tour['id']);

                                    if ($status) {
                                        if ($status['status'] == 0 || $status['status'] == 1) {
                                            echo "<button class='bookbtn' disabled>Already Booked</button>";
                                        } elseif ($status['status'] == 3) {
                                            echo '<button class="bookbtn" id="rate">Rate and Review</button>';
                                        } else {
                                            echo '<button class="bookbtn" id="book">Book Now</button>';
                                        }
                                    } else {
                                        echo '<button class="bookbtn" id="book">Book Now</button>';
                                    }
                                } ?>
                                <a href="map?id=<?php echo $_GET['id']; ?>" class="viewbtn">Go Here</a>
                            </div>

                        </div>
                    </div>
                </div>
                <hr>
                <div class="comment-section">
                    <h3>Rating and Reviews</h3>
                    <div class="comment-box">
                        <img src="https://via.placeholder.com/40" alt="User Avatar" class="avatar">
                        <textarea placeholder="Write a comment..." class="comment-input"></textarea>
                        <button class="comment-submit-btn">Post</button>
                    </div>

                    <div class="comments-list">
                        <?php
                        try {
                            $stmt = $conn->prepare("SELECT rr.*, u.name as name, u.profile_picture as img FROM review_rating rr JOIN users u ON rr.user_id = u.id WHERE tour_id = :tour_id ORDER BY date_created DESC");
                            $stmt->bindParam(':tour_id', $decrypted_id, PDO::PARAM_INT);
                            $stmt->execute();
                            $comments = $stmt->fetchAll();
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage();
                        }
                        foreach ($comments as $comment) {
                            ?>
                            <div class="comment">
                                <img src="upload/Profile Pictures/<?php echo $comment['img'] ?>" alt="User Avatar"
                                    class="avatar">
                                <div class="comment-content">
                                    <h4 class="comment-author"><?php echo $comment['name'] ?></h4>
                                    <div class="comment-star">
                                        <?php
                                        $rating = $comment['rating']; // Example rating
                                        $starOutput = '';

                                        // Loop to create filled stars
                                        for ($i = 1; $i <= $rating; $i++) {
                                            $starOutput .= "<span class='comment-star'><i class='bx bxs-star'></i></span>";
                                        }

                                        // Loop to create empty stars
                                        for ($i = $rating + 1; $i <= 5; $i++) {
                                            $starOutput .= "<span class='comment-star'><i class='bx bx-star'></i></span>";
                                        }

                                        // Display the stars
                                        echo $starOutput;
                                        ?>
                                    </div>
                                    <p class="comment-text"><?php echo $comment['review'] ?></p>
                                    <div class="comment-actions">
                                        <span class="comment-time"><?php echo timeAgo($comment['date_created']) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- Add more comments here as needed -->
                    </div>
                    <button class="show-more-btn" style="display: none;">Show More</button>
                </div>
            </div>
            <!-- comment section -->
        </div>
    </div>
    <div id="bookingModal" class="modal booking">
        <div class="modal-content booking">
            <span class="close">&times;</span>
            <h2>Book Your Tour</h2>
            <form action="php/booking.php" method="POST">
                <input type="hidden" name="tour_id" value="<?php echo $tour['id']; ?>">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <input type="hidden" name="phone_number" value="<?php echo $user['phone_number']; ?>">
                <label for="tour_date">Select Date:</label>
                <input type="date" id="tour_date" name="date_sched" required>
                <button type="submit" class="book-btn">Confirm Booking</button>
            </form>
        </div>
    </div>
    <?php require "include/login-registration.php"; ?>
    <?php
    if (isset($status['id'])) {
        $bookingId = $status['id'];
    } else {
        $bookingId = ''; // Or handle the error accordingly
    }
    ?>
    <script src="index.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>

        document.querySelector('.pricing-header').addEventListener('click', function () {
            const pricingContent = document.querySelector('.pricing-content');
            pricingContent.style.display = pricingContent.style.display === 'none' || pricingContent.style.display === '' ? 'block' : 'none';
        });
        document.addEventListener('DOMContentLoaded', function () {
            var modal = document.getElementById("bookingModal");
            var bookbtn = document.getElementById("book");
            var ratebtn = document.getElementById("rate");
            var span = document.querySelector(".close");

            if (bookbtn) {
                bookbtn.addEventListener("click", function () {
                    modal.classList.add('active');
                });
            }

            if (ratebtn) {
                ratebtn.addEventListener("click", function () {
                    window.location.href = 'rate_review?booking_id=<?php echo $bookingId ?>';
                });
            }

            // Close the modal when the "x" button is clicked
            span.addEventListener("click", function () {
                modal.classList.remove('active');
            });

            // Close the modal when clicking outside of the modal content
            window.addEventListener("click", function (event) {
                if (event.target === modal) {
                    modal.classList.remove('active');
                }
            });

            // Optional: Add keypress event to close modal with ESC key


            const comments = document.querySelectorAll('.comments-list .comment');
            const showMoreButton = document.querySelector('.show-more-btn');
            let commentsPerPage = 5;
            let currentCommentCount = commentsPerPage;

            function updateCommentDisplay() {
                comments.forEach((comment, index) => {
                    if (index < currentCommentCount) {
                        comment.style.display = 'flex';
                    } else {
                        comment.style.display = 'none';
                    }
                });

                if (currentCommentCount >= comments.length) {
                    showMoreButton.style.display = 'none';
                } else {
                    showMoreButton.style.display = 'block';
                }
            }

            showMoreButton.addEventListener('click', function () {
                currentCommentCount += commentsPerPage;
                updateCommentDisplay();
            });

            mapboxgl.accessToken = 'pk.eyJ1Ijoibmlrb2xhaTEyMjIiLCJhIjoiY20xemJ6NG9hMDRxdzJqc2NqZ3k5bWNlNiJ9.tAsio6eF8LqzAkTEcPLuSw';

            const map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/light-v11',
                center: [<?php echo htmlspecialchars($tour['longitude']); ?>, <?php echo htmlspecialchars($tour['latitude']); ?>],
                zoom: 15,
                interactive: false
            });

            const markerElement = document.createElement('div');
            markerElement.className = 'marker';
            markerElement.style.backgroundImage = 'url(assets/icons/<?php echo htmlspecialchars(strtok($tour['type'], " ")); ?>.png)';
            markerElement.style.backgroundSize = 'contain';
            markerElement.style.width = '30px';
            markerElement.style.height = '30px';

            const marker = new mapboxgl.Marker(markerElement)
                .setLngLat([<?php echo htmlspecialchars($tour['longitude']); ?>, <?php echo htmlspecialchars($tour['latitude']); ?>])
                .addTo(map);

            map.dragPan.disable();
            map.scrollZoom.disable();
            map.touchZoomRotate.disable();
            map.rotate.disable();
        });

    </script>
</body>

</html>