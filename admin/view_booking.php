<?php 
include '../include/db_conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?action=Invalid");
    exit();
}

if (!isset($_GET['user_id']) || !isset($_GET['booking_id'])) {
    header("Location: ../admin/booking.php");
    exit();
}

$user_id = $_GET['user_id'];
$booking_id = $_GET['booking_id'];

// Fetch user details
$query_user = "SELECT id, username, email, phone_number FROM users WHERE id = ?";
$stmt_user = mysqli_prepare($conn, $query_user);
mysqli_stmt_bind_param($stmt_user, "i", $user_id);
mysqli_stmt_execute($stmt_user);
$user_result = mysqli_stmt_get_result($stmt_user);

if ($user_result) {
    $user = mysqli_fetch_assoc($user_result);
} else {
    die("User not found.");
}

$query_booking = "SELECT b.*, t.title as tour_title FROM booking b
                  JOIN tours t ON b.tours_id = t.id
                  WHERE b.id = ?";
$stmt_booking = mysqli_prepare($conn, $query_booking);
mysqli_stmt_bind_param($stmt_booking, "i", $booking_id);
mysqli_stmt_execute($stmt_booking);
$booking_result = mysqli_stmt_get_result($stmt_booking);

if ($booking_result) {
    $booking = mysqli_fetch_assoc($booking_result);
} else {
    die("Booking not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo $webIcon ?>">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">

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
                    <?php include 'includes/breadcrumb.php';?>
                </div>
            </div>

            <div class="info">
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
            </div>
        </main>
    </section>
    <script src="../assets/js/script.js"></script>
</body>
</html>
