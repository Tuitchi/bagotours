<?php

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
        WHERE DATE(date_sched) = CURDATE()
    ");
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $notif = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($notif as $notification) {
            error_log("Creating notification for user: " . $notification['user_id'] . ", tour: " . $notification['tour_id']);
            $checkStmt = $conn->prepare("
                SELECT COUNT(*) FROM notifications 
                WHERE user_id = :user_id AND tour_id = :tour_id AND DATE(created_at) = CURDATE()
            ");
            $checkStmt->bindParam(':user_id', $notification['user_id'], PDO::PARAM_INT);
            $checkStmt->bindParam(':tour_id', $notification['tour_id'], PDO::PARAM_INT);
            $checkStmt->execute();
$userStmt = $conn->prepare("SELECT name FROM users WHERE id = :user_id");
            $userStmt->bindParam(':user_id', $notification['client'], PDO::PARAM_INT);
            $userStmt->execute();
            $user = $userStmt->fetch();
        
            if ($checkStmt->fetchColumn() == 0) {
                try {
                    createNotif($conn, $notification['user_id'], $notification['tour_id'], $user['name'] . " will arrive today at ". $notification['title'], "booking?id=".$notification['id']."", "booking");
                    createNotif($conn, $notification['client'], $notification['tour_id'],"Your reservation at ". $notification['title']." is scheduled for today. Click me, and I'll direct you there.", "map?id=". base64_encode($notification['tour_id'] . $salt) ."", "booking");
                    error_log("Notification created for user: " . $notification['user_id'] . ", tour: " . $notification['tour_id']);
                } catch (Exception $e) {
                    error_log("Failed to create notification: " . $e->getMessage());
                }
            } else {
                error_log("Notification already exists for user: " . $notification['user_id'] . ", tour: " . $notification['tour_id']);
            }
        }
    } else {
        error_log("No bookings found for today.");
    }
} catch (PDOException $e) {
    error_log('Error: ' . $e->getMessage());
}

error_log("Script finished.");

function createNotif($conn, $userId, $tour_id, $message, $url, $type)
{
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, tour_id, message, url, type, created_at) VALUES (:user_id, :tour_id, :message, :url, :type, NOW())");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);
    $stmt->bindParam(':url', $url, PDO::PARAM_STR);
    $stmt->bindParam(':type', $type, PDO::PARAM_STR);
    if ($stmt->execute()) {
        return "success";
    } else {
        return "error";
    }
}
