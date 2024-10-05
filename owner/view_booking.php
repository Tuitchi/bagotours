<?php
include '../include/db_conn.php';
session_start();

$pageRole = "owner";
require_once '../php/accValidation.php';

if (!isset($_GET['user_id']) || !isset($_GET['booking_id'])) {
    header("Location: ../owner/booking.php");
    exit();
}

$user_id = $_GET['user_id'];
$booking_id = $_GET['booking_id'];
$query_user = "SELECT id, username, email, phone_number FROM users WHERE id = :user_id";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt_user->execute();
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$query_booking = "SELECT b.*, t.title as tour_title FROM booking b
                  JOIN tours t ON b.tours_id = t.id
                  WHERE b.id = :booking_id";
$stmt_booking = $conn->prepare($query_booking);
$stmt_booking->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
$stmt_booking->execute();
$booking = $stmt_booking->fetch(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/owner.css">

    <title>BaGoTours. View Booking</title>
</head>

<body>
    <?php include 'includes/sidebar.php'; ?>
    <section id="content">
        <?php include 'includes/navbar.php'; ?>
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>View Booking</h1>
                    <?php include 'includes/breadcrumb.php'; ?>
                </div>
            </div>
            <div class="info">
                <?php
                if (!$booking) {
                    echo "<p>Booking not found.</p>";
                    return;
                } else { ?>
                    <h2>User Details</h2>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone_number'], ENT_QUOTES, 'UTF-8'); ?></p>

                    <h2>Booking Details</h2>
                    <p><strong>Tour:</strong> <?php echo htmlspecialchars($booking['tour_title'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Date Scheduled:</strong> <?php echo htmlspecialchars($booking['date_sched'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>People:</strong> <?php echo htmlspecialchars($booking['people'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($booking['phone_number'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Status:</strong> <?php echo $booking['status'] == '0' ? 'Pending' : ($booking['status'] == '1' ? 'Confirmed' : ($booking['status'] == '2' ? 'Completed' : 'Cancelled')); ?></p>
                <?php } ?>
            </div>
        </main>
    </section>
    <script src="../assets/js/script.js"></script>
</body>

</html>