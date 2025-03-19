<?php
ini_set('log_errors', 1); // Enable error logging
ini_set('error_log', '../error_log.txt'); // Set the error log file path

if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === 'bagotours.com') {
    // Local environment
    $DATABASE_HOSTNAME = "localhost";
    $DATABASE_USERNAME = "root";
    $DATABASE_PASSWORD = "";
    $DATABASE_NAME = "tourism";
} else {
    // Live environment
    $DATABASE_HOSTNAME = "localhost";
    $DATABASE_USERNAME = "u520834156_digiTourism24";
    $DATABASE_PASSWORD = "2024@BagoDigitalTourism";
    $DATABASE_NAME = "u520834156_digitaltourism";
}


$salt = "ATON_ATON_LANG_NI!";
$clientID = '993186673271-dhs3giufk5m2bie1u0tdp9b2eor5el1o.apps.googleusercontent.com';
$clientSecret = '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';
try {
    $conn = new PDO("mysql:host=$DATABASE_HOSTNAME;dbname=$DATABASE_NAME", $DATABASE_USERNAME, $DATABASE_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch the web icon
    $sql = "SELECT file FROM system_info WHERE type = 'Tab Icon' LIMIT 1;";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $webIcon = $row['file'];
    } else {
        throw new Exception("No file found for 'Tab Icon'.");
    }
} catch (PDOException $e) {
    error_log("PDO error: " . $e->getMessage());
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
}
try {
    $conn->beginTransaction();
    $stmt = $conn->prepare("UPDATE booking SET status = 2 WHERE DATE(start_date) < CURDATE() AND status IN (0, 1, 3)");
    $stmt->execute();

    $stmt = $conn->prepare("
        DELETE n
        FROM notifications n
        JOIN booking b ON n.user_id = b.user_id AND n.tour_id = b.tour_id
        WHERE b.start_date < NOW()
    ");
    $stmt->execute();

    $conn->commit();
} catch (Exception $e) {
    $conn->rollBack();
    error_log("Error deleting bookings: " . $e->getMessage());
}


try {
    // Query all necessary information in one step
    $stmt = $conn->prepare("SELECT 
        b.id AS booking_id, 
        b.user_id AS client, 
        t.id AS tour_id, 
        t.user_id AS user_id, 
        t.title AS title, 
        CONCAT(u.firstname, ' ', u.lastname) AS client_name 
    FROM booking b 
    JOIN tours t ON b.tour_id = t.id
    JOIN users u ON b.user_id = u.id
    WHERE DATE(b.start_date) = CURDATE();"); // Use CURDATE() instead of NOW()

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $notif = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $checkStmt = $conn->prepare("
            SELECT COUNT(*) 
            FROM notifications 
            WHERE user_id = :user_id 
            AND tour_id = :tour_id 
            AND type = 'booking'
            AND DATE(created_at) = CURDATE()");

        foreach ($notif as $notification) {

            // Check if notification already exists for today
            $checkStmt->execute([
                ':user_id' => $notification['user_id'],
                ':tour_id' => $notification['tour_id']
            ]);

            if ($checkStmt->fetchColumn() == 0) {
                // Prepare messages
                $tourOwnerMessage = "{$notification['client_name']} will arrive today at {$notification['title']}";
                $clientMessage = "Your reservation at {$notification['title']} is scheduled for today. Click me, and I'll direct you there.";
                $clientUrl = "map?id=" . base64_encode($notification['tour_id'] . $salt);

                // Create notifications
                try {
                    createNotif($conn, $notification['user_id'], $notification['tour_id'], $tourOwnerMessage, "booking?id={$notification['booking_id']}", "booking");
                    createNotif($conn, $notification['client'], $notification['tour_id'], $clientMessage, $clientUrl, "booking");

                    log_error_with_location("Notification created for user: {$notification['user_id']}, tour: {$notification['tour_id']}");
                } catch (Exception $e) {
                    log_error_with_location("Failed to create notification: " . $e->getMessage());
                }
            }
        }
    }
} catch (PDOException $e) {
    log_error_with_location('Error: ' . $e->getMessage());
}

function createNotif($conn, $userId, $tour_id, $message, $url, $type)
{
    try {
        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, tour_id, message, url, type, created_at) 
            VALUES (:user_id, :tour_id, :message, :url, :type, NOW())
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':tour_id' => $tour_id,
            ':message' => $message,
            ':url' => $url,
            ':type' => $type
        ]);
        return "success";
    } catch (PDOException $e) {
        log_error_with_location("Error creating notification: " . $e->getMessage());
        return "error";
    }
}


function log_error_with_location($message)
{
    // Get the current file and line where this function is called
    $file = __FILE__;    // Get the current file path
    $line = __LINE__;    // Get the current line number

    // Format the error message with file and line number
    $formatted_message = "[" . date("Y-m-d H:i:s") . "] Error in file $file on line $line: $message" . PHP_EOL;

    // Log the message to the PHP error log
    error_log($formatted_message, 3, '../error_log.txt');
}
function getUserByEmail($conn, $email)
{
    $stmt = $conn->prepare('SELECT id, username, role, profile_picture, device_id FROM users WHERE email = :email');
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function createUser($conn, $email, $firstname, $lastname, $profile_picture)
{
    $stmt = $conn->prepare('INSERT INTO users (email, firstname, lastname, profile_picture, role) VALUES (:email,:firstname,:lastname, :profile_picture, "user")');
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':firstname', $firstname);
    $stmt->bindParam(':lastname', $lastname);
    $stmt->bindParam(':profile_picture', $profile_picture);
    $stmt->execute();
    return $conn->lastInsertId();
}