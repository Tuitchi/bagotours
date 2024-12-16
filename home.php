<?php
session_start();
require 'include/db_conn.php';



if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin");
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $date = date('Y-m-d');
    $stmt = $conn->prepare("SELECT expiry, id, status FROM tours WHERE user_id = :user_id AND (status = 'Rejected' OR DATE(expiry) >= :current_date)");

    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':current_date', $date, PDO::PARAM_STR);
    $stmt->execute();

    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($tours) {
        foreach ($tours as $tour) {

            if ($tour['expiry'] <= $date) {
                $deleteStmt = $conn->prepare("DELETE FROM tours WHERE user_id = :user_id AND id = :tour_id");
                $deleteStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $deleteStmt->bindParam(':tour_id', $tour['id'], PDO::PARAM_INT);

                if ($deleteStmt->execute()) {
                    require_once 'func/func.php';
                    createNotification($conn, $user_id, $tour['id'], "You can register as an owner again.", "form", "Tour Deletion");
                }
            }
        }
    }
    // Check if the user already has the 'is_trusted' status set to 1
    $trustStmt = $conn->prepare("SELECT is_trusted FROM users WHERE id = :user_id");
    $trustStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $trustStmt->execute();

    $isTrusted = $trustStmt->fetchColumn(); // Fetch the current 'is_trusted' status

    // Only update if the user is not already trusted and the booking count is 2 or more
    if ($isTrusted != 1) {
        // Count the number of trusted bookings (status = 4)
        $trustStmt = $conn->prepare("SELECT COUNT(*) FROM booking WHERE user_id = :user_id AND status = 4");
        $trustStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $trustStmt->execute();

        $bookingCount = $trustStmt->fetchColumn();

        if ($bookingCount >= 2) {
            // Mark the user as trusted
            $stmt = $conn->prepare("UPDATE users SET is_trusted = 1 WHERE id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $_SESSION['trusted'] = true;
            }
        }
    }
}


// Handle AJAX request for nearby tours
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    if ($input['action'] === 'get_nearby_tours') {
        $userLat = $input['userLat'];
        $userLng = $input['userLng'];
        $radius = 50;

        // Validate input
        if (!is_numeric($userLat) || !is_numeric($userLng)) {
            echo json_encode(['error' => 'Invalid latitude or longitude']);
            exit;
        }
        $query = "
        SELECT 
            t.id AS id, 
            title, 
            type, 
            t.img AS img, 
            IFNULL(AVG(r.rating), 0) AS average_rating, 
            IFNULL(COUNT(r.id), 0) AS review_count,
            (6371 * acos(
                cos(radians(:userLat)) * cos(radians(latitude)) * 
                cos(radians(longitude) - radians(:userLng)) + 
                sin(radians(:userLat)) * sin(radians(latitude))
            )) AS distance
        FROM tours t
        LEFT JOIN review_rating r ON t.id = r.tour_id 
        WHERE t.status IN ('Active', 'Temporarily Closed')
        GROUP BY t.id, t.title, t.type, t.img, t.latitude, t.longitude
        HAVING distance < :radius  -- Corrected HAVING clause
        ORDER BY distance ASC;  -- Corrected ORDER BY clause
    ";



        try {
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':userLat' => $userLat,
                ':userLng' => $userLng,
                ':radius' => $radius,
            ]);

            $nearbyTours = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($nearbyTours as &$tour) {
                $tour['encoded_id'] = base64_encode($tour['id'] . $salt);
            }

            // Ensure we return an empty array if no tours are found
            header('Content-Type: application/json');
            echo json_encode($nearbyTours);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo $webIcon ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <title>BagoTours</title>
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
    <?php include 'nav/topnav.php' ?>

    <div class="main-container">
        <?php include 'nav/sidenav.php' ?>
        <div class="main">
            <div class="carousel-container">
                <button class="prev" onclick="prevSlide()">&#10094;</button>
                <button class="next" onclick="nextSlide()">&#10095;</button>
                <div class="carousel-slide">
                    <?php
                    require_once 'func/user_func.php';
                    $tours = getAllTours($conn);
                    shuffle($tours);

                    foreach (array_slice($tours, 0, 3) as $tour) {
                        // Get the main image only (first image in the comma-separated list)
                        $tour_images = explode(',', $tour['img']);
                        $main_image = $tour_images[0];

                        echo "<div class='carousel-item'>
                <a href='tour?id=" . base64_encode($tour['id'] . $salt) . "'>
                    <img src='upload/Tour Images/" . htmlspecialchars($main_image, ENT_QUOTES, 'UTF-8') . "' alt='" . htmlspecialchars($tour['title'], ENT_QUOTES, 'UTF-8') . "'>
                    <div class='carousel-caption'>
                        <h3>" . htmlspecialchars($tour['title'], ENT_QUOTES, 'UTF-8') . "</h3>
                        <p>" . htmlspecialchars($tour['type'], ENT_QUOTES, 'UTF-8') . "</p>
                    </div>
                </a>
              </div>";
                    }
                    ?>
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
                    <?php
                    $popularTours = getAllPopularTours($conn);
                    foreach ($popularTours as $tour) {
                        $averageRating = round($tour['average_rating']);
                        $fullStars = str_repeat("★", $averageRating);
                        $emptyStars = str_repeat("☆", 5 - $averageRating);
                        $totalStars = $fullStars . $emptyStars;
                        $tour_images = explode(',', $tour['img']);
                        $main_image = $tour_images[0];
                        echo "<div class='spot'>
                                <a href='tour?id=" . base64_encode($tour['id'] . $salt) . "'>
                                    <img src='upload/Tour Images/" . $main_image . "' alt='" . htmlspecialchars($tour['title']) . "'>  
                                    <h3>" . htmlspecialchars($tour['title']) . "</h3>
                                    <p>" . htmlspecialchars($tour['type']) . "</p>
                                    <div class='rating'>" . $totalStars . " <span>(" . htmlspecialchars($tour['review_count']) . " reviews)</span>
                                    </div>
                                </a>
                            </div>";
                    } ?>
                </div>
            </div>
            <?php
            $events = getEventByDate($conn);
            $eventCount = count($events);
            ?>

            <div class="popularspot">
                <?php if ($eventCount > 1): ?>
                    <h2>Upcoming Events</h2>
                <?php elseif ($eventCount == 1): ?>
                    <h2>Upcoming Event</h2>
                <?php else: ?>
                    <h2>Upcoming Event</h2>
                    <div class="spots events">
                        <div class="spot">
                            <img src="assets/booking-empty.png" alt="Empty booking">
                            <h3>No Event</h3>
                            <p>Await forthcoming events</p>

                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($eventCount > 0): ?>
                    <div class="spots events">
                        <?php foreach ($events as $event): ?>
                            <div class="spot event">
                                <a href="tour?id=<?php echo base64_encode($event['event_code'] . $salt); ?>">
                                    <img src="upload/Event/<?php echo htmlspecialchars($event['event_image']); ?>"
                                        alt="<?php echo htmlspecialchars($event['event_name']); ?>">
                                    <h3><?php echo htmlspecialchars($event['event_name']); ?></h3>
                                    <p><?php echo htmlspecialchars($event['event_type']); ?></p>
                                    <div class="event-meta">
                                        <p>📅
                                            <?php echo date('F d, Y', strtotime($event['event_date_start'])) . ' - ' . date('F d, Y', strtotime($event['event_date_end'])); ?>
                                        </p>
                                        <p>📍 <?php echo htmlspecialchars($event['event_location']); ?></p>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="popularspot">
                <h2>Nearby Tours</h2>
                <div id="loadingCard" style="display: none;">
                    <p>Loading nearby tours...</p>
                </div>
                <div class="spots nearby-spots">
                    <!-- Nearby tours will be loaded here -->
                </div>
            </div>

            <div class="report-container" id="cardContainer">
                <?php foreach ($tours as $tour) {
                    $tour_images = explode(',', $tour['img']);
                    $main_image = $tour_images[0];
                    echo "<div class='cards'>
                        <a href='tour?id=" . base64_encode($tour['id'] . $salt) . "' class='card'>
                        <img src='upload/Tour Images/" . $main_image . "' alt='" . $tour['title'] . "'>
                          
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            if (navigator.geolocation) {
                console.log("Geolocation is supported.");
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        console.log("Geolocation acquisition successful.");
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        console.log("User location:", userLat, userLng);
                        fetchNearbyTours(userLat, userLng);
                    },
                    function (error) {
                        console.error("Error getting location: " + error.message);
                    }
                );
            } else {
                console.log("Geolocation is not supported by this browser.");
            }
            function fetchNearbyTours(userLat, userLng) {
                console.log("Fetching nearby tours for coordinates:", userLat, userLng);
                $('#loadingCard').show();  // Show loading indicator

                fetch('home.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'get_nearby_tours',
                        userLat: userLat,
                        userLng: userLng
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log("Nearby tours data:", data);
                        $('#loadingCard').hide();  // Hide loading indicator

                        if (!Array.isArray(data) || data.length === 0) {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                icon: 'error',
                                title: 'No Nearby Tours',
                                timerProgressBar: true,

                            });
                            Toast.fire();
                            $('#loadingCard').show();
                        } else {
                            displayNearbyTours(data);
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        $('#loadingCard').hide();  // Hide loading indicator even on error
                    });
            }

            function displayNearbyTours(tours) {
                const spotsContainer = $('.nearby-spots'); // Assuming there is a container with this class
                spotsContainer.empty(); // Clear existing content

                if (!tours || tours.length === 0) {
                    spotsContainer.append('<p>No nearby tours found.</p>');
                    return;
                }

                tours.forEach(tour => {
                    const mainImage = tour.img.split(',')[0]; // Extract the main image
                    const tourElement = `
            <div class="spot">
                <a href="tour?id=${tour.encoded_id}">
                    <img src="upload/Tour Images/${mainImage}" alt="${tour.title}">
                    <h3>${tour.title}</h3>
                    <p>${tour.type}</p>
                    <p>Distance: ${tour.distance.toFixed(2)} km</p>
                    <div class="rating">
                        ${'★'.repeat(Math.round(tour.average_rating)) + '☆'.repeat(5 - Math.round(tour.average_rating))}
                        <span>(${tour.review_count} reviews)</span>
                    </div>
                </a>
            </div>`;
                    spotsContainer.append(tourElement);
                });
            }


            <?php
            if (isset($_SESSION['trusted']) && $_SESSION['trusted'] == true) {
                echo "
            Swal.fire({
                icon: 'success',
                title: ' Congratulations🎉.',
                text: 'You earned a trustworthy badge.',
                showConfirmButton: true
                }
            });
        ";
                unset($_SESSION['trusted']);
            } elseif (isset($_SESSION['downgrade']) && $_SESSION['downgrade'] == true) {
                echo "
            Swal.fire({
                icon: 'info',
                title: 'Your account has been downgraded to a standard user by the admin.',
                text: 'If you believe this is a mistake, please contact the admin for clarification.',
                showConfirmButton: true,
            });
        ";
                unset($_SESSION['downgrade']);
            } elseif (isset($_SESSION['loginSuccess']) && $_SESSION['loginSuccess'] == true) {
                echo "
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            icon: 'success',
            title: 'As your guide today, I’m happy to inform you that you\'re all set and logged in!',
            timerProgressBar: true,
           
        });
        Toast.fire();
        ";
                unset($_SESSION['loginSuccess']);
            }
            ?>
        });
    </script>

</body>

</html>