<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'include/db_conn.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

// Handle AJAX request for nearby tours
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    if ($input['action'] === 'get_nearby_tours') {
        $userLat = $input['userLat'];
        $userLng = $input['userLng'];
        $radius = 15; // Define your radius in kilometers

        // Validate input
        if (!is_numeric($userLat) || !is_numeric($userLng)) {
            echo json_encode(['error' => 'Invalid latitude or longitude']);
            exit;
        }
        $query = "
        SELECT 
            t.id as id, 
            title, 
            type, 
            t.img as img, 
            IFNULL(AVG(r.rating), 0) AS average_rating, 
            IFNULL(COUNT(r.id), 0) AS review_count,
            (6371 * acos(
                cos(radians(:userLat)) * cos(radians(latitude)) * 
                cos(radians(longitude) - radians(:userLng)) + 
                sin(radians(:userLat)) * sin(radians(latitude))
            )) AS distance
        FROM tours t
        LEFT JOIN review_rating r ON t.id = r.tour_id 
        GROUP BY t.id, t.title, t.type, t.img, t.latitude, t.longitude
        HAVING distance < :radius
        ORDER BY distance ASC
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
                        $averageRating = round($tour['average_rating']);
                        $fullStars = str_repeat("★", $averageRating);
                        $emptyStars = str_repeat("☆", 5 - $averageRating);
                        $totalStars = $fullStars . $emptyStars;
                        echo "<div class='spot'>
                                <a href='tour?id=" . base64_encode($tour['id'] . $salt) . "'>
                                    <img src='upload/Tour Images/" . $tour['img'] . "' alt='" . htmlspecialchars($tour['title']) . "'>  
                                    <h3>" . htmlspecialchars($tour['title']) . "</h3>
                                    <p>" . htmlspecialchars($tour['type']) . "</p>
                                    <div class='rating'>" . $totalStars . " <span>(" . htmlspecialchars($tour['review_count']) . " reviews)</span>
                                    </div>
                                </a>
                            </div>";
                    } ?>
                </div>
            </div>
            <div class="popularspot">
                <h2>Nearby Tours</h2>
                <div class="spots nearby-spots">
                </div>
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
            </div>
            <div class="pagination" id="pagination"></div>
        </div>
    </div>
    <?php require "include/login-registration.php"; ?>
    <script src="index.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if (navigator.geolocation) {
                console.log("Geolocation is supported.");
                navigator.geolocation.getCurrentPosition(
                    position => {
                        console.log("Geolocation acquisition successful.");
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        console.log("User location:", userLat, userLng);
                        fetchNearbyTours(userLat, userLng);
                    },
                    error => {
                        console.error("Error getting location: " + error.message);
                    }
                );
            } else {
                console.log("Geolocation is not supported by this browser.");
            }
        });

        function fetchNearbyTours(userLat, userLng) {
            console.log("Fetching nearby tours for coordinates:", userLat, userLng);

            fetch('home.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ action: 'get_nearby_tours', userLat: userLat, userLng: userLng })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Nearby tours data:", data);
                    if (data.error) {
                        console.error("Error from server:", data.error);
                    } else {
                        displayNearbyTours(data);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function displayNearbyTours(tours) {
            const spotsContainer = document.querySelector('.nearby-spots');
            spotsContainer.innerHTML = '';

            if (!tours || tours.length === 0) {
                spotsContainer.innerHTML = '<p>No nearby tours found.</p>';
                return;
            }

            tours.forEach(tour => {
                const tourElement = document.createElement('div');
                tourElement.classList.add('spot');
                tourElement.innerHTML = `
            <a href="tour?id=${tour.encoded_id}">
                <img src="upload/Tour Images/${tour.img}" alt="${tour.title}">
                <h3>${tour.title}</h3>
                <p>${tour.type}</p>
                <p>Distance: ${tour.distance.toFixed(2)} km</p>
                <div class="rating">
                    ${'★'.repeat(Math.round(tour.average_rating)) + '☆'.repeat(5 - Math.round(tour.average_rating))}
                    <span>(${tour.review_count} reviews)</span>
                </div>
            </a>
            `;
                spotsContainer.appendChild(tourElement);
            });
        }
    </script>

</body>

</html>