<?php
function getTouristSpots($conn) {
    $query = "SELECT id, title, latitude, longitude, type, img, address FROM tours";
    $result = $conn->query($query);

    $touristSpots = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $touristSpots[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude'],
                'type' => $row['type'],
                'image' => $row['img'],
                'address' => $row['address']
            ];
        }
    }

    return json_encode($touristSpots);
}

function createNotification($userId, $message, $url, $type = 'info') {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, url, type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $userId, $message, $url, $type);
    $stmt->execute();
}
function getNotifications($userId) {
    global $conn;

    $stmt = $conn->prepare("SELECT id, message, url FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    return $notifications;
}

function getNotificationCount($userId) {
    global $conn; // Your database connection

    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $row = $result->fetch_assoc();
    return $row['count'];
}