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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo $webIcon ?>">
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

        @media screen and (max-width: 500px) {
            .event-meta {
                display: flex;
                flex-direction: column;
                margin-top: 10px;
                font-size: 0.8em;
                color: #666;
            }

            .event-header h1 {
                font-size: 2em;
            }
        }

        .optional {
            margin-top: 10px;
            font-size: 0.9em;
            color: #666;
        }
        .btons {
    display: flex;
    flex-wrap: wrap; /* Allow wrapping for smaller screens */
    gap: 10px; /* Space between buttons */
    justify-content: center; /* Center align buttons */
    margin: 20px 0;
}

.back-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px; /* Space between icon and text */
    padding: 10px 20px;
    font-size: 1rem;
    font-weight: bold;
    text-decoration: none;
    border-radius: 20px;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s, box-shadow 0.3s;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow */
}

.back-button.map {
    background-color: #2a9df4; /* Primary color for 'Navigate' */
    color: #fff; /* White text */
}

.back-button.map:hover {
    background-color: #1b7ec1;
    transform: translateY(-2px); /* Lift effect */
}

.back-button {
    color:#fff;
    border: 1px solid #333;
}

.back-button:hover {
    background-color: #333; /* Secondary color for 'Back to Events' */
    color: #fff;
   
    transform: translateY(-2px);
}

.back-button i {
    font-size: 1.2rem; /* Icon size */
}

/* Media Query for Mobile Devices */
@media (max-width: 768px) {
    .btons {
        flex-direction: column; /* Stack buttons vertically */
        align-items: stretch; /* Make buttons take full width */
    }

    .back-button {
        font-size: 0.9rem; /* Adjust font size for smaller screens */
        padding: 12px; /* Increase padding for better touch target */
        box-shadow: none; /* Simplify shadow on mobile */
    }
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

                <!-- Optional Fields -->
                <?php if (!empty($event['registration_deadline']) && $event['registration_deadline'] != '0000-00-00 00:00:00'): ?>
                    <div class="optional">
                        <strong>Registration Deadline:</strong>
                        <?php echo date('F d, Y', strtotime($event['registration_deadline'])); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($event['organizer_name'])): ?>
                    <div class="optional">
                        <strong>Organizer:</strong> <?php echo htmlspecialchars($event['organizer_name']); ?> -
                    <?php endif; ?>

                    <?php if (!empty($event['organizer_contact'])): ?>

                        <?php echo htmlspecialchars($event['organizer_contact']); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($event['sponsor'])): ?>
                    <div class="optional">
                        <strong>Sponsor:</strong> <?php echo htmlspecialchars($event['sponsor']); ?>
                    </div>
                <?php endif; ?>
                <div class="event-meta">
                    <p>📅
                        <?php echo date('F d, Y', strtotime($event['event_date_start'])) . ' - ' . date('F d, Y', strtotime($event['event_date_end'])); ?>
                    </p>
                    <p>📍 <?php echo htmlspecialchars($event['event_location']); ?></p>
                </div>
                <div class="btons">
                    <a href="map?event=<?php echo $_GET['event'] ?>" class="back-button map">
                        <i class="fa fa-map-marker-alt"></i> Navigate
                    </a>
                    <a href="event" class="back-button">
                        <i class="fa fa-arrow-left"></i> Back to Events
                    </a>
                </div>
            </div>  
        </div>
    </div>

    <?php require "include/login-registration.php"; ?>
    <script src="index.js"></script>
</body>

</html>