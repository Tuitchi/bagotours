<?php session_start();
require 'include/db_conn.php';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id =?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <style>
        /* Modal Background */
        .modal {
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        /* Modal Content */
        .modal-content {
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

        .bookbtn,
        .viewbtn {
            background-color: #010058af;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .bookbtn:hover,
        .viewbtn:hover {
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
            <div class="tour-container">
                <div class="tour-images">
                    <div class="carousel-container">
                        <button class="prev" onclick="prevSlide()">&#10094;</button>
                        <button class="next" onclick="nextSlide()">&#10095;</button>
                        <div class="carousel-slide">
                            <?php
                            require_once 'func/user_func.php';
                            $tour_images = getTourImageById($conn, $decrypted_id);
                            foreach ($tour_images as $tour_image) {
                                echo "<div class='carousel-item'>
                        <img src='upload/Tour Images/" . $tour_image['combined_image'] . "' alt='" . $tour_image['title'] . "'>
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
                            <p class="rating">⭐⭐⭐⭐</p>
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
                                    if (isAlreadyBooked($conn, $user_id, $tour['id'])) {
                                        echo "<button class='bookbtn' disabled>Already Booked</button>";
                                    } else {
                                        echo '<button class="bookbtn">Book Now</button>';
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
                        <div class="comment">
                            <img src="https://via.placeholder.com/40" alt="User Avatar" class="avatar">
                            <div class="comment-content">
                                <h4 class="comment-author">John Doe</h4>
                                <p class="comment-text">This is a sample comment 1.</p>
                                <div class="comment-actions">
                                    <span class="comment-time">2 hours ago</span>
                                    <span class="reply-btn">Reply</span>
                                </div>
                            </div>
                        </div>

                        <!-- Add more comments here as needed -->
                    </div>
                    <button class="show-more-btn" style="display: none;">Show More</button>
                </div>
            </div>
            <!-- comment section -->
        </div>
        <div id="bookingModal" class="modal">
            <div class="modal-content">
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
    </div>


    <script src="index.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>

        document.querySelector('.pricing-header').addEventListener('click', function () {
            const pricingContent = document.querySelector('.pricing-content');
            pricingContent.style.display = pricingContent.style.display === 'none' || pricingContent.style.display === '' ? 'block' : 'none';
        });
        document.addEventListener('DOMContentLoaded', function () {
            var modal = document.getElementById("bookingModal");
            var btn = document.querySelector(".bookbtn");
            var span = document.querySelector(".close");

            btn.addEventListener("click", function () {
                modal.classList.add('active');
            });

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
            window.addEventListener("keydown", function (event) {
                if (event.key === "Escape") {
                    modal.classList.remove('active');
                }
            });

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