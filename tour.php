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
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo $webIcon ?>">
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="assets/css/tour.css">
    <style>
        .comment-section {
    position: relative;
    width: 100%;
    margin: auto;
    padding: 10px;
    background-color: #f9f9f9;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Comment box styling */
.comment-box {
    display: flex;
    flex-direction: row;
    gap: 10px; /* Ensure spacing between elements */
    align-items: flex-start; /* Align to the top */
}

.input-box {
    display: flex;
    flex-direction: row;
    flex: 1; /* Allow the input to grow and fit */
    width: 100%; /* Ensure full-width use */
    box-sizing: border-box; /* Prevent padding from breaking layout */
}

.input-box #rating {
    width: 30px;
    height: 30px;
    background-color: #ccc;
    border-radius: 50%;
    cursor: pointer;
}

.comment-box .avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
}

.input-box .comment-input {
    display: block; /* Fix any inline behavior */
    width: 100%; /* Ensure it spans the container */
    padding: 8px;
    font-size: 14px; /* Make it readable on smaller screens */
    box-sizing: border-box; /* Account for padding in the width */
}

.input-box .comment-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

.comment-box .comment-submit-btn {
    margin-left: 10px;
    padding: 8px 12px;
    background-color: #007bff;
    width: auto;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.comment-box .comment-submit-btn:hover {
    background-color: #0056b3;
}
/* Container styling for dropdown */
.rating-container {
    position: relative;
    max-width: 300px;
    margin: auto;
}

/* Dropdown base styles */
.star {
   background-image: url(star.png);
    appearance: none; /* Remove default dropdown */
    background: linear-gradient(to right, #f7f7f7, #ffffff); /* Subtle gradient */
    border: 1px solid #ddd; /* Light border */
    border-radius: 8px; /* Smooth corners */
    padding: 12px 15px; /* Comfortable padding */
    font-size: 16px; /* Modern font size */
    color: #444; /* Darker text color */
    cursor: pointer;
    width: 100%; /* Responsive width */
    transition: all 0.3s ease; /* Smooth transition effects */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
}

/* Add custom arrow for the dropdown */
.star::after {
    content: '▼'; /* Unicode arrow */
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 14px;
    color: #888;
    pointer-events: none; /* Make sure arrow doesn’t interfere */
}

/* Hover and Focus States */
.star:hover {
    border-color: #007bff; /* Blue border on hover */
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.3); /* Blue glow */
}

.star:focus {
    outline: none;
    border-color: #0056b3; /* Stronger blue on focus */
    box-shadow: 0 0 10px rgba(0, 86, 179, 0.4); /* Glow effect */
}

/* Styling for the Options in the Dropdown */
.star option {
    background: #fff; /* White background */
    color: #333; /* Dark text for visibility */
    font-size: 16px;
    padding: 10px; /* Padding for spacing */
}

/* Optional Label Styling */
.rating-label {
    font-size: 14px;
    color: #555;
    margin-bottom: 8px;
    display: block;
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
                            <p class="desc"><i class="bx bxs-map"></i><?php echo $tour['address'] ?></p>
                            <div class="pricing-container">
                                <h3 class="pricing-header">Price <i class='bx bx-caret-down'></i></h3>
                                <div class="pricing-content">
                                    <div class="pricing">
                                        <h4>Entrance</h4>
                                        -
                                        <h5>P100/pax</h5>
                                    </div>
                                    <div class="pricing">
                                        <h4>Entrance</h4>
                                        -
                                        <h5>P100/pax</h5>
                                    </div>
                                    <div class="pricing">
                                        <h4>Entrance</h4>
                                        -
                                        <h5>P100/pax</h5>
                                    </div>
                                </div>
                            </div>
                            <p class="rating"><?php echo $ratingStars; ?></p>
                            <h4>About</h4>
                            <p class="details"><?php echo $tour['description']?></p>
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
                        <img src="upload/Profile Pictures/<?php echo $_SESSION['profile-pic'] ?>" alt="User Avatar"
                            class="avatar">
                        <div class="input-box">
                            
                        <div class="rating-container">
                            <label for="rating" class="rating-label">Rate Us:</label>
                            <select class="star" id="rating" name="rating">
                                <option value="1">⭐ 1 Star</option>
                                <option value="2">⭐⭐ 2 Stars</option>
                                <option value="3">⭐⭐⭐ 3 Stars</option>
                                <option value="4">⭐⭐⭐⭐ 4 Stars</option>
                                <option value="5">⭐⭐⭐⭐⭐ 5 Stars</option>
                            </select>
                        </div>

                            <textarea placeholder="Share your experience..." class="comment-input"></textarea>
                            <button class="comment-submit-btn">Post</button>
                        </div>
                       
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
            let isExpanded = false; // Track whether all comments are shown

            function updateCommentDisplay() {
                comments.forEach((comment, index) => {
                    if (index < commentsPerPage || isExpanded) {
                        comment.style.display = 'flex'; // Show visible comments
                    } else {
                        comment.style.display = 'none'; // Hide others
                    }
                });

                // Update button text
                showMoreButton.textContent = isExpanded ? 'Show Less' : 'Show More';

                // Show or hide button based on comment count
                if (!isExpanded && comments.length <= commentsPerPage) {
                    showMoreButton.style.display = 'none'; // Hide button if not needed
                } else {
                    showMoreButton.style.display = 'block'; // Show button otherwise
                }
            }

            if (comments.length > 0) {
                updateCommentDisplay(); // Initialize display

                showMoreButton.addEventListener('click', function () {
                    if (isExpanded) {
                        commentsPerPage = 5; // Reset to initial comments count
                    } else {
                        commentsPerPage = comments.length; // Show all comments
                    }
                    isExpanded = !isExpanded; // Toggle expanded state
                    updateCommentDisplay(); // Update display
                });
            } else {
                showMoreButton.style.display = 'none'; // Hide button if no comments
            }

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