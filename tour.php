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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = filter_var($_POST['rating'], FILTER_SANITIZE_NUMBER_INT);
    $review = filter_var($_POST['review'], FILTER_SANITIZE_STRING);

    if (!empty($rating) && !empty($review)) {
        if ($rating < 1 || $rating > 5) {
            $_SESSION['errorMessage'] = "Rating must be between 1 and 5 stars.";
        } else {
            try {
                // Check for duplicate reviews
                $stmt = $conn->prepare("SELECT COUNT(*) FROM review_rating WHERE tour_id = :tour_id AND user_id = :user_id");
                $stmt->bindParam(':tour_id', $decrypted_id, PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt->fetchColumn() > 0) {
                    $_SESSION['errorMessage'] = "You have already submitted a review for this tour.";
                } else {
                    // Insert the review
                    $img = ''; // Handle image upload if required
                    $stmt = $conn->prepare("INSERT INTO review_rating (tour_id, user_id, rating, review, img, date_created) VALUES (:tour_id, :user_id, :rating, :review, :img, NOW())");
                    $stmt->bindParam(':tour_id', $decrypted_id, PDO::PARAM_INT);
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
                    $stmt->bindParam(':review', $review, PDO::PARAM_STR);
                    $stmt->bindParam(':img', $img, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        $_SESSION['successMessage'] = "Review submitted successfully!";
                    } else {
                        $_SESSION['errorMessage'] = "Review submission failed.";
                    }
                }
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage(), 3, "error_log.txt");
                $_SESSION['errorMessage'] = "An error occurred while submitting your review.";
            }
        }
    } else {
        $_SESSION['errorMessage'] = "Please provide both rating and review.";
    }

    // Redirect to the same page
    header("Location: " . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);
    exit;
}



require_once 'func/func.php';
$averageRating = getAverageRatingNew($conn, $decrypted_id);
$ratingStars = displayRatingStars($averageRating);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BagoTours</title>
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo $webIcon ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="https://api.mapbox.com/mapbox-gl-js/v3.8.0/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.8.0/mapbox-gl.js"></script>
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
            flex-direction: column;
            gap: 10px;
            /* Ensure spacing between elements */
            align-items: flex-start;
            /* Align to the top */
        }

        .rating-container {
            display: flex
        }

        .input-box {
            display: flex;
            flex-direction: row;
            flex: 1;
            /* Allow the input to grow and fit */
            width: 100%;
            /* Ensure full-width use */
            box-sizing: border-box;
            /* Prevent padding from breaking layout */
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
            display: block;
            /* Fix any inline behavior */
            width: 100%;
            /* Ensure it spans the container */
            padding: 8px;
            font-size: 14px;
            /* Make it readable on smaller screens */
            box-sizing: border-box;
            /* Account for padding in the width */
        }

        .input-box .comment-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .comment-actions .edit:hover {
            color: #007bff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .comment-actions .delete:hover {
            color: red;
            cursor: pointer;
            transition: background-color 0.3s ease;
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
            appearance: none;
            /* Remove default dropdown */
            background: linear-gradient(to right, #f7f7f7, #ffffff);
            /* Subtle gradient */
            border: 1px solid #ddd;
            /* Light border */
            border-radius: 8px;
            /* Smooth corners */
            padding: 12px 15px;
            /* Comfortable padding */
            font-size: 16px;
            /* Modern font size */
            color: #444;
            /* Darker text color */
            cursor: pointer;
            width: 100%;
            /* Responsive width */
            transition: all 0.3s ease;
            /* Smooth transition effects */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Subtle shadow for depth */
        }

        /* Add custom arrow for the dropdown */
        .star::after {
            content: '▼';
            /* Unicode arrow */
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            color: #888;
            pointer-events: none;
            /* Make sure arrow doesn’t interfere */
        }

        /* Hover and Focus States */
        .star:hover {
            border-color: #007bff;
            /* Blue border on hover */
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
            /* Blue glow */
        }

        .star:focus {
            outline: none;
            border-color: #0056b3;
            /* Stronger blue on focus */
            box-shadow: 0 0 10px rgba(0, 86, 179, 0.4);
            /* Glow effect */
        }

        /* Styling for the Options in the Dropdown */
        .star option {
            background: #fff;
            /* White background */
            color: #333;
            /* Dark text for visibility */
            font-size: 16px;
            padding: 10px;
            /* Padding for spacing */
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
                            <p class="details"><?php echo $tour['description'] ?></p>
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
                    <form action="" method="post">
                        <div class="comment-box">
                            <div class="rating">
                                <div class="rating-container">
                                    <img src="upload/Profile Pictures/<?php echo $_SESSION['profile-pic'] ?>"
                                        alt="User Avatar" class="avatar">
                                    <label for="rating" class="rating-label">Rate Us:</label>
                                    <select class="star" id="rating" name="rating">
                                        <option value="5">⭐ 5 Stars</option>
                                        <option value="4">⭐ 4 Stars</option>
                                        <option value="3">⭐ 3 Stars</option>
                                        <option value="2">⭐ 2 Stars</option>
                                        <option value="1">⭐ 1 Star</option>
                                    </select>
                                </div>
                            </div>

                            <div class="input-box">

                                <textarea placeholder="Share your experience..." class="comment-input"
                                    name="review"></textarea>
                                <button class="comment-submit-btn" type="submit">Post</button>
                            </div>
                        </div>
                    </form>
                    <div class="comments-list">
                        <?php
                        try {
                            $stmt = $conn->prepare("SELECT rr.*, CONCAT(firstname, ' ', lastname) as name, u.profile_picture AS img 
                                FROM review_rating rr 
                                JOIN users u ON rr.user_id = u.id 
                                WHERE tour_id = :tour_id 
                                ORDER BY date_created DESC");
                            $stmt->bindParam(':tour_id', $decrypted_id, PDO::PARAM_INT);
                            $stmt->execute();
                            $comments = $stmt->fetchAll();

                            $userComment = [];
                            $otherComments = [];

                            foreach ($comments as $comment) {
                                if ($comment['user_id'] == $user_id) {
                                    $userComment[] = $comment;
                                } else {
                                    $otherComments[] = $comment;
                                }
                            }
                            $comments = array_merge($userComment, $otherComments);
                        } catch (PDOException $e) {
                            error_log("Error fetching comments: " . $e->getMessage());
                        }
                        ?>

                        <?php foreach ($comments as $comment):
                            $isUserComment = ($comment['user_id'] == $user_id); ?>
                            <div class="comment" id="comment-<?php echo $comment['id']; ?>">
                                <img src="upload/Profile Pictures/<?php echo $comment['img']; ?>" alt="User Avatar"
                                    class="avatar">
                                <div class="comment-content">
                                    <h4 class="comment-author"><?php echo htmlspecialchars($comment['name']); ?></h4>
                                    <div class="comment-star">
                                        <?php
                                        $rating = $comment['rating'];
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $rating
                                                ? "<span class='comment-star'><i class='bx bxs-star'></i></span>"
                                                : "<span class='comment-star'><i class='bx bx-star'></i></span>";
                                        }
                                        ?>
                                    </div>
                                    <div class="comment-text">
                                        <p class="comment" id="text-<?php echo $comment['id']; ?>">
                                            <?php echo htmlspecialchars($comment['review']); ?>
                                        </p>

                                        <?php if ($isUserComment): ?>
                                            <form id="edit-form-<?php echo $comment['id']; ?>" class="edit-form"
                                                style="display: none;">
                                                <textarea id="edit-text-<?php echo $comment['id']; ?>" class="comment-input"
                                                    style="width:100%"><?php echo htmlspecialchars($comment['review']); ?></textarea>
                                                <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>" />
                                                <button type="submit" class="comment-submit-btn"
                                                    data-comment-id="<?php echo $comment['id']; ?>">
                                                    <i class="bx bxs-send"></i> Save
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                    <div class="comment-actions">
                                        <span class="comment-time">
                                            <?php
                                            // Check if 'date_updated' is null, and determine the appropriate timestamp
                                            $timestamp = $comment['date_updated'] ?: $comment['date_created'];
                                            $status = $comment['date_updated'] ? "edited" : "";
                                            echo timeAgo($timestamp) . " " . $status;
                                            ?>
                                        </span>
                                        <?php if ($isUserComment): ?>
                                            <a class="edit" id="edit-btn-<?php echo $comment['id']; ?>" href="#">Edit</a>
                                            <a class="delete" href="#" id="delete-btn-<?php echo $comment['id']; ?>">Delete</a>
                                            <div class="cancel-btn hide" id="cancel-btn-<?php echo $comment['id']; ?>">
                                                <p>Press ESC to</p>
                                                <a class="cancel" href="#">Cancel</a>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>
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
        $(document).ready(function () {
            // Initialize SweetAlert Toast
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.on('mouseenter', Swal.stopTimer);
                    toast.on('mouseleave', Swal.resumeTimer);
                }
            });

            // Edit button functionality
            $('.edit').on('click', function (e) {
                e.preventDefault();
                const commentId = $(this).attr('id').split('-')[2]; // Extract comment ID
                toggleEdit(commentId);
            });

            // Delete comment functionality
            $('.delete').on('click', function (e) {
                e.preventDefault();
                const commentId = $(this).attr('id').split('-')[2]; // Extract comment ID
                deleteComment(commentId);
            });

            // Cancel edit functionality
            $('.cancel').on('click', function (e) {
                e.preventDefault();
                const commentId = $(this).closest('.cancel-btn').attr('id').split('-')[2]; // Extract comment ID
                cancelEdit(commentId);
            });

            // Toggle edit form visibility
            function toggleEdit(commentId) {
                const $textElement = $(`#text-${commentId}`);
                const $editForm = $(`#edit-form-${commentId}`);
                const $editBtn = $(`#edit-btn-${commentId}`);
                const $deleteBtn = $(`#delete-btn-${commentId}`);
                const $cancelBtn = $(`#cancel-btn-${commentId}`);

                // Cancel the edit
                function cancelEdit() {
                    $editForm.hide();
                    $textElement.show();
                    $editBtn.show();
                    $deleteBtn.show();
                    $cancelBtn.addClass('hide');
                }

                if ($editForm.is(':hidden')) {
                    $editForm.show();
                    $textElement.hide();
                    $editBtn.hide();
                    $deleteBtn.hide();
                    $cancelBtn.removeClass('hide');

                    $(document).on('keydown.escape', function (event) {
                        if (event.key === 'Escape') {
                            cancelEdit();
                            $(document).off('keydown.escape');
                        }
                    });
                } else {
                    cancelEdit();
                }
            }

            // Handle form submission via AJAX
            $('.edit-form').on('submit', function (event) {
                event.preventDefault();

                const $form = $(this);
                const commentId = $form.find('input[name="comment_id"]').val();
                const reviewText = $form.find('textarea').val().trim();

                $.ajax({
                    url: 'php/edit_comment.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { comment_id: commentId, review: reviewText },
                    success: function (data) {
                        if (data.success) {
                            Toast.fire({
                                icon: 'success',
                                title: 'Your review has been successfully updated.'
                            });
                            $(`#text-${commentId}`).text(data.updatedReview);
                            $(`#comment-${commentId}`).find('.comment-text').show();
                            toggleEdit(commentId);
                        } else {
                            alert('Error updating the comment.');
                        }
                    },
                    error: function () {
                        Toast.fire({
                            icon: 'error',
                            title: 'An error occurred while saving the comment'
                        });
                    }
                });
            });

            // Delete comment action
            window.deleteComment = function (commentId) {
                if (!confirm('Are you sure you want to delete this comment?')) return;

                $.ajax({
                    url: 'php/delete_comment.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { comment_id: commentId },
                    success: function (data) {
                        if (data.success) {
                            $(`#comment-${commentId}`).remove();
                            Toast.fire({
                                icon: 'success',
                                title: 'Your review has been successfully deleted.'
                            });
                        } else {
                            alert('Error deleting the comment.');
                        }
                    },
                    error: function () {
                        alert('Error deleting the comment.');
                    }
                });
            };

            // SweetAlert Toast Notifications for session messages
            <?php if (isset($_SESSION['successMessage'])): ?>
                Toast.fire({
                    icon: 'success',
                    title: '<?php echo $_SESSION['successMessage']; ?>'
                });
                <?php unset($_SESSION['successMessage']); ?>
            <?php elseif (isset($_SESSION['errorMessage'])): ?>
                Toast.fire({
                    icon: 'error',
                    title: '<?php echo $_SESSION['errorMessage']; ?>'
                });
                <?php unset($_SESSION['errorMessage']); ?>
            <?php endif; ?>

            // Toggle pricing section visibility
            $('.pricing-header').on('click', function () {
                $('.pricing-content').toggle();
            });

            // Modal handling for booking and rating
            const modal = $("#bookingModal");
            const $bookbtn = $("#book");
            const $ratebtn = $("#rate");
            const span = $(".close");

            $bookbtn.on("click", function () {
                modal.addClass('active');
            });

            $ratebtn.on("click", function () {
                window.location.href = 'rate_review?booking_id=<?php echo $bookingId ?>';
            });

            span.on("click", function () {
                modal.removeClass('active');
            });

            $(window).on("click", function (event) {
                if ($(event.target).is(modal)) {
                    modal.removeClass('active');
                }
            });

            // Handle comments show more/less functionality
            const $comments = $('.comments-list .comment');
            const $showMoreButton = $('.show-more-btn');
            let commentsPerPage = 5;
            let isExpanded = false;

            function updateCommentDisplay() {
                $comments.each(function (index, comment) {
                    $(comment).toggle(index < commentsPerPage || isExpanded);
                });

                $showMoreButton.text(isExpanded ? 'Show Less' : 'Show More');
                $showMoreButton.toggle($comments.length > commentsPerPage);
            }

            if ($comments.length > 0) {
                updateCommentDisplay();
                $showMoreButton.on('click', function () {
                    isExpanded = !isExpanded;
                    commentsPerPage = isExpanded ? $comments.length : 5;
                    updateCommentDisplay();
                });
            } else {
                $showMoreButton.hide();
            }

            mapboxgl.accessToken = 'pk.eyJ1Ijoibmlrb2xhaTEyMjIiLCJhIjoiY20xemJ6NG9hMDRxdzJqc2NqZ3k5bWNlNiJ9.tAsio6eF8LqzAkTEcPLuSw';

            const map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/navigation-night-v1',
                center: [<?php echo htmlspecialchars($tour['longitude'], ENT_QUOTES, 'UTF-8'); ?>, <?php echo htmlspecialchars($tour['latitude'], ENT_QUOTES, 'UTF-8'); ?>],
                zoom: 15,
                interactive: false
            });

            const markerEl = document.createElement('div');
            markerEl.className = 'marker';
            markerEl.style.backgroundImage = `url(assets/icons/<?php echo htmlspecialchars(strtok($tour['type'], " "), ENT_QUOTES); ?>.png)`;
            markerEl.style.width = '50px';
            markerEl.style.height = '50px';
            markerEl.style.backgroundSize = 'contain';


            const marker = new mapboxgl.Marker(markerEl)
                .setLngLat([<?php echo htmlspecialchars($tour['longitude'], ENT_QUOTES, 'UTF-8'); ?>, <?php echo htmlspecialchars($tour['latitude'], ENT_QUOTES, 'UTF-8'); ?>])
                .addTo(map);

            map.dragPan.disable();
            map.scrollZoom.disable();
            map.touchZoomRotate.disable();
            map.rotate.disable();

        });
    </script>

</body>

</html>