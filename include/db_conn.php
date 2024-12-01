<?php
ini_set('log_errors', 1); // Enable error logging
ini_set('error_log', '../error_log.txt'); // Set the error log file path

$DATABASE_HOSTNAME = "localhost";
$DATABASE_USERNAME = "root";
$DATABASE_PASSWORD = "";
$DATABASE_NAME = "tourism";

$salt = "ATON_ATON_LANG_NI!";

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
    $stmt = $conn->prepare("
        SELECT b.id, b.user_id as client, t.id as tour_id, t.user_id as user_id, t.title as title
        FROM booking b 
        JOIN tours t ON b.tour_id = t.id 
        JOIN users u ON t.user_id = u.id 
        WHERE DATE(b.start_date) = CURDATE()
    ");
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $notif = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Prepare reusable statements
        $checkStmt = $conn->prepare("
            SELECT COUNT(*) FROM notifications 
            WHERE user_id = :user_id AND tour_id = :tour_id AND DATE(created_at) = CURDATE()
        ");
        $userStmt = $conn->prepare("SELECT name FROM users WHERE id = :user_id");

        foreach ($notif as $notification) {
            // Check if notification already exists
            $checkStmt->bindParam(':user_id', $notification['user_id'], PDO::PARAM_INT);
            $checkStmt->bindParam(':tour_id', $notification['tour_id'], PDO::PARAM_INT);
            $checkStmt->execute();

            if ($checkStmt->fetchColumn() == 0) {
                // Fetch client name once
                $userStmt->bindParam(':user_id', $notification['client'], PDO::PARAM_INT);
                $userStmt->execute();
                $user = $userStmt->fetch();

                // Create notifications
                try {
                    createNotif(
                        $conn,
                        $notification['user_id'],
                        $notification['tour_id'],
                        "{$user['name']} will arrive today at {$notification['title']}",
                        "booking?id={$notification['id']}",
                        "booking"
                    );

                    createNotif(
                        $conn,
                        $notification['client'],
                        $notification['tour_id'],
                        "Your reservation at {$notification['title']} is scheduled for today. Click me, and I'll direct you there.",
                        "map?id=" . base64_encode($notification['tour_id'] . $salt),
                        "booking"
                    );

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
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, tour_id, message, url, type, created_at) VALUES (:user_id, :tour_id, :message, :url, :type, NOW())");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);
    $stmt->bindParam(':url', $url, PDO::PARAM_STR);
    $stmt->bindParam(':type', $type, PDO::PARAM_STR);
    return $stmt->execute() ? "success" : "error";
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
