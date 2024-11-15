<?php
session_start();
require 'include/db_conn.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

$stmt = $conn->prepare("SELECT b.*, t.title, t.img FROM booking b JOIN users u ON b.user_id = u.id JOIN tours t ON b.tour_id = t.id WHERE u.id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$statusOrder = [3, 0, 1, 4, 2];

usort($bookings, function ($a, $b) use ($statusOrder) {
    $aOrder = array_search($a['status'], $statusOrder);
    $bOrder = array_search($b['status'], $statusOrder);
    return $aOrder - $bOrder;
});

// Initialize counters for each booking status
$statusCounts = [
    'waiting' => 0,
    'ongoing' => 0,
    'cancelled' => 0,
    'review' => 0,
    'completed' => 0
];

// Count the number of bookings per status
foreach ($bookings as $booking) {
    switch ($booking['status']) {
        case 0:
            $statusCounts['waiting']++;
            break;
        case 1:
            $statusCounts['ongoing']++;
            break;
        case 2:
            $statusCounts['cancelled']++;
            break;
        case 3:
            if (!$booking['is_review'])
                $statusCounts['review']++; // Count for review
            break;
        case 4:
            $statusCounts['completed']++;
            break;
    }
}

$totalBookings = count($bookings);

// Cancel booking logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_booking_id'])) {
    $booking_id = $_POST['cancel_booking_id'];

    // Check if the booking is cancellable
    $stmt = $conn->prepare("SELECT status FROM booking WHERE id = :booking_id");
    $stmt->bindParam(":booking_id", $booking_id, PDO::PARAM_INT);
    $stmt->execute();
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($booking && $booking['status'] == 0) { // 0 means Booking Approval
        // Proceed to cancel
        $updateStmt = $conn->prepare("UPDATE booking SET status = 2 WHERE id = :booking_id"); // Assuming 2 means cancelled
        $updateStmt->bindParam(":booking_id", $booking_id, PDO::PARAM_INT);
        $updateStmt->execute();

        echo "<script>alert('Booking has been successfully canceled.'); window.location.href='booking.php';</script>"; // Redirect to the current page
    } else {
        echo "<script>alert('Booking cannot be canceled unless it is in Booking Approval status.'); window.location.href='booking.php';</script>";
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
    <title>BagoTours</title>
    <link rel="stylesheet" href="user.css">
    <style>
        /* Main container styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

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

        .desc a {
            background-color: #75ba75;
            padding: 10px 20px;
            color: white;
        }

        .desc a:hover {
            background-color: #419a43;
        }


        .booking-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .booking-card {
            display: flex;
            flex-direction: column;
            background-color: #fff;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        /* Hover effect for booking cards */
        .booking-card:hover {
            transform: translateY(-5px);
        }

        /* Image styling */
        .booking-card img {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        /* Title and description */
        .booking-card h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #444;
        }

        .btn {
            background-color: #ff6b6b;
            color: white;
            border: none;
            padding: 10px 16px;
            font-size: 14px;
            border-radius: 30px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            cursor: pointer;
            margin-top: auto;
        }

        .btn-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .btn-container button {
            background-color: #dce5fd;
            border: none;
            padding: 10px 16px;
            font-size: 14px;
            border-radius: 8px;
            margin-left: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-container button.active {
            background-color: #007bff;
            color: white;
        }

        .status-label {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.9rem;
            color: #fff;
            margin-top: 10px;
        }

        .waiting {
            background-color: blue;
        }

        .cancelled {
            background-color: red;
        }

        .ongoing {
            background-color: skyblue;
        }

        .complete {
            background-color: green;
        }
    </style>
</head>

<body>
    <?php include 'nav/topnav.php'; ?>
    <div class="main-container">
        <?php include 'nav/sidenav.php'; ?>
        <div class="main">
            <div class="booking">
                <?php if (empty($bookings)) { ?>
                    <h3>No Booking Found.</h3>
                    <img src="assets/booking-empty.png" alt="No bookings found" style="max-width: 100%; height: auto;">
                    <div class="desc">
                        <p>Book your adventure now and create memories that last a lifetime!</p>
                        <a href="list" class="btn">Plan your trip</a>
                    </div>
                <?php } else { ?>
                    <h2>Your Bookings</h2>
                    <div class="btn-container">
                        <button data-status="0" onclick="filterBookings(0)">Booking Approval (<?php echo $statusCounts['waiting']; ?>)</button>
                        <button data-status="1" onclick="filterBookings(1)">Ongoing (<?php echo $statusCounts['ongoing']; ?>)</button>
                        <button data-status="3" onclick="filterBookings(3)">To Review (<?php echo $statusCounts['review']; ?>)</button>
                        <button data-status="4" onclick="filterBookings(4)">Completed (<?php echo $statusCounts['completed']; ?>)</button>
                        <button data-status="all" onclick="filterBookings('all')">View All (<?php echo $totalBookings; ?>)</button>
                    </div>
                    <div class="booking-container">
                        <?php foreach ($bookings as $booking) { ?>
                            <div class="booking-card" data-status="<?php echo $booking['status']; ?>" data-review="<?php echo $booking['is_review']; ?>">
                                <img src="upload/Tour Images/<?php echo $booking['img']; ?>" alt="<?php echo $booking['title']; ?>">
                                <div class="desc">
                                    <h3><?php echo $booking['title']; ?></h3>
                                    <?php
                                    if ($booking['is_review']) {
                                        echo '<div class="status-label complete">Reviewed</div>';
                                    } else {
                                        switch ($booking['status']) {
                                            case 0:
                                                echo '<div class="status-label waiting">Waiting for approval</div>';
                                                echo '<form method="POST" style="margin-top: 10px;">';
                                                echo '<input type="hidden" name="cancel_booking_id" value="' . $booking['id'] . '">';
                                                echo '<button type="submit" class="btn" onclick="return confirm(\'Are you sure you want to cancel this booking?\')">Cancel Booking</button>';
                                                echo '</form>';
                                                break;
                                            case 1:
                                                echo '<div class="status-label ongoing">Ongoing</div>';
                                                break;
                                            case 2:
                                                echo '<div class="status-label cancelled">Cancelled</div>';
                                                break;
                                            case 3:
                                                echo '<a href="rate_review.php?booking_id=' . $booking['id'] . '" class="btn">Rate & Review</a>';
                                                break;
                                            case 4:
                                                echo '<div class="status-label complete">Completed</div>';
                                                break;
                                            default:
                                                echo '<div class="status-label">Error Booking</div>';
                                        }
                                    }
                                    ?>
                                </div>
                                <span class="sched">Scheduled Date: <strong>
                                        <?php
                                        $date = new DateTime($booking['date_sched']);
                                        echo $date->format('M. d, Y');
                                        ?>
                                    </strong></span>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <script src="index.js"></script>
    <script>
        function filterBookings(status) {
            const cards = document.querySelectorAll('.booking-card');
            const buttons = document.querySelectorAll('.btn-container button');

            // Reset active class for all buttons
            buttons.forEach(button => button.classList.remove('active'));

            // Add active class to the clicked button
            const activeButton = Array.from(buttons).find(button => {
                return button.getAttribute('data-status') == status || status === 'all' && button.getAttribute('data-status') === 'all';
            });
            if (activeButton) {
                activeButton.classList.add('active');
            }

            // Show or hide cards based on the selected status
            cards.forEach(card => {
                const cardStatus = parseInt(card.getAttribute('data-status'));
                const isReviewed = parseInt(card.getAttribute('data-review'));

                if (status === 'all' ||
                    (status == 0 && cardStatus === 0) ||
                    (status == 1 && cardStatus === 1) ||
                    (status == 3 && cardStatus === 3 && isReviewed === 0) || // To Review
                    (status == 4 && cardStatus === 4)) {
                    card.style.display = 'block'; // Show card
                } else {
                    card.style.display = 'none'; // Hide card
                }
            });
        }

        // Set the initial filter to show all bookings
        document.addEventListener("DOMContentLoaded", () => {
            filterBookings('all'); // Show all bookings on page load
        });
    </script>
</body>

</html>