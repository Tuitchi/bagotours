<?php
session_start();
require 'include/db_conn.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}
$message = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_review'])) {
    // Sanitize input
    $tour_id = htmlspecialchars($_POST['tour_id']);
    $rating = (int) $_POST['rating'];
    $review = htmlspecialchars($_POST['review']);
    $img = ''; // Placeholder for image upload functionality

    // Insert review into the database
    try {
        $stmt = $conn->prepare("INSERT INTO review_rating (tour_id, user_id, rating, review, img) VALUES (:tour_id, :user_id, :rating, :review, :img)");
        $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        $stmt->bindParam(':review', $review, PDO::PARAM_STR);
        $stmt->bindParam(':img', $img, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $message = "<p>Review submitted successfully!</p>";
            try {
                $statusStmt = $conn->prepare("update booking SET status = 4 WHERE id = :id");
                $statusStmt->bindParam(':id', $_GET['booking_id'], PDO::PARAM_INT);
                if ($statusStmt->execute()) {
                    sleep(2);

                    header("Location: booking");
                    exit();
                }
            } catch (PDOException $e) {
                error_log($e->getMessage());
            }
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}

if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];
    try {
        $stmt = $conn->prepare("SELECT * FROM booking WHERE id = :id");
        $stmt->bindParam(':id', $booking_id, PDO::PARAM_INT);
        $stmt->execute();
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($booking) {
            $tour_id = $booking['tour_id'];
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}
try {
    $stmt = $conn->prepare("SELECT * FROM review_rating WHERE tour_id = :tour_id ORDER BY date_created DESC");
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
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
        .review-section {
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 20px;
        }

        .review-section h2,
        .review-section h3 {
            color: #333;
        }

        .review-section form {
            margin-bottom: 20px;
        }

        .review-section label {
            display: block;
            margin-bottom: 5px;
        }

        .review-section select,
        .review-section textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .review-section button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .review-section button:hover {
            background-color: #218838;
        }

        .review {
            margin-bottom: 15px;
        }

        .review strong {
            display: block;
            margin-bottom: 5px;
        }

        .review p {
            margin: 5px 0;
        }
    </style>
</head>
<?php include 'nav/topnav.php' ?>
<div class="main-container">
    <?php include 'nav/sidenav.php' ?>
    <div class="main">
        <div class="review-section">
            <?php if ($message)
                echo $message ?>
                <h2>Share Your Experience</h2>
                <form method="POST" action="">
                    <input type="hidden" name="tour_id" value="<?php echo htmlspecialchars($tour_id); ?>">
                <label for="rating">Rating:</label>
                <select name="rating" id="rating" required>
                    <option value="1">1 Star</option>
                    <option value="2">2 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="5">5 Stars</option>
                </select>
                <br>
                <label for="review">Review:</label>
                <textarea name="review" id="review" rows="5" required></textarea>
                <br>
                <button type="submit" name="submit_review">Submit Review</button>
            </form>

            <h3>Existing Reviews</h3>
            <div class="reviews">
                <?php if ($reviews): ?>
                    <?php foreach ($reviews as $row): ?>
                        <div class="review">
                            <strong>User ID: <?php echo htmlspecialchars($row['user_id']); ?></strong>
                            <p>Rating: <?php echo htmlspecialchars($row['rating']); ?> Stars</p>
                            <p><?php echo nl2br(htmlspecialchars($row['review'])); ?></p>
                            <p><em>Reviewed on: <?php echo htmlspecialchars($row['date_created']); ?></em></p>
                        </div>
                        <hr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No reviews yet. Be the first to review!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require "include/login-registration.php"; ?>
<script src="index.js"></script>
</body>

</html>