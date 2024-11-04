<?php
session_start();
require 'include/db_conn.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

if (isset($_GET['event'])) {
    $event_code_raw = base64_decode($_GET['event']);
    $event_code = preg_replace(sprintf('/%s/', $salt), '', $event_code_raw);

    try {
        $stmt = $conn->prepare("SELECT * FROM events WHERE event_code = :event_code");
        $stmt->bindParam(':event_code', $event_code);
        $stmt->execute();
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            header("Location: event?status=404");
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        header("Location: event?status=500");
        exit();
    }
} else {
    header("Location: event?status=404");

    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['event_name']); ?> - BagoTours</title>
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <style>
        .event-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .event-header h1 {
            font-size: 2.5em;
            color: #333;
        }

        .event-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }

        .event-details {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .event-details h2 {
            color: #007BFF;
        }

        .event-details p {
            line-height: 1.6;
        }

        .event-meta {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 0.9em;
            color: #666;
        }

        .optional {
            margin-top: 10px;
            font-size: 0.9em;
            color: #666;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        .back-button.map {
            background-color: #75ba75;
            color: white;
        }

        .back-button.map:hover {
            background-color: #52aa6f;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <?php include 'nav/topnav.php' ?>

    <div class="main-container">
        <?php include 'nav/sidenav.php' ?>
        <div class="main">
            <div class="event-header">
                <h1><?php echo htmlspecialchars($event['event_name']); ?></h1>
                <img class="event-image" src="upload/Event/<?php echo htmlspecialchars($event['event_image']); ?>"
                    alt="<?php echo htmlspecialchars($event['event_name']); ?>">
            </div>

            <div class="event-details">
                <h2><?php echo htmlspecialchars($event['event_type']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($event['event_description'])); ?></p>

                <div class="event-meta">
                    <p>📅
                        <?php echo date('F d, Y', strtotime($event['event_date_start'])) . ' - ' . date('F d, Y', strtotime($event['event_date_end'])); ?>
                    </p>
                    <p>📍 <?php echo htmlspecialchars($event['event_location']); ?></p>
                </div>

                <!-- Optional Fields -->
                <?php if (!empty($event['registration_deadline']) && $event['registration_deadline'] != '0000-00-00 00:00:00'): ?>
                    <div class="optional">
                        <strong>Registration Deadline:</strong>
                        <?php echo date('F d, Y', strtotime($event['registration_deadline'])); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($event['organizer_name'])): ?>
                    <div class="optional">
                        <strong>Organizer:</strong> <?php echo htmlspecialchars($event['organizer_name']); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($event['organizer_contact'])): ?>
                    <div class="optional">
                        <strong>Contact:</strong> <?php echo htmlspecialchars($event['organizer_contact']); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($event['sponsor'])): ?>
                    <div class="optional">
                        <strong>Sponsor:</strong> <?php echo htmlspecialchars($event['sponsor']); ?>
                    </div>
                <?php endif; ?>
                <a href="map?event=<?php echo $_GET['event'] ?>" class="back-button map">Go to Event</a>
            </div>

            <a href="event" class="back-button">Back to Events</a>
        </div>
    </div>

    <?php require "include/login-registration.php"; ?>
    <script src="index.js"></script>
</body>

</html>