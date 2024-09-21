<?php
function getTouristSpots($conn) {
    $query = "SELECT id, title, latitude, longitude, type, img, address FROM tours";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return json_encode($result);
}

function createNotification($conn, $userId, $message, $url, $type) {
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, url, type) VALUES (:user_id, :message, :url, :type)");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);
    $stmt->bindParam(':url', $url, PDO::PARAM_STR);
    $stmt->bindParam(':type', $type, PDO::PARAM_STR);
    $stmt->execute();
}

function getNotifications($conn, $userId) {
    $stmt = $conn->prepare("SELECT id, message, url FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getNotificationCount($conn, $user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = :user_id AND is_read = 0");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['unread_count'];
}
