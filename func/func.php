<?php
function getTouristSpots($conn, $user_id)
{
    $query = "SELECT * FROM tours WHERE status = 1 AND user_id = $user_id";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
}

// NOTIFICATIONS
function createNotification($conn, $userId, $tour_id, $message, $url, $type)
{
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, tour_id, message, url, type) VALUES (:user_id, :tour_id, :message, :url, :type)");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);
    $stmt->bindParam(':url', $url, PDO::PARAM_STR);
    $stmt->bindParam(':type', $type, PDO::PARAM_STR);
    $stmt->execute();
}

function getNotifications($conn, $userId)
{
    $stmt = $conn->prepare("SELECT id, message, url FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getNotificationCount($conn, $user_id)
{
    $stmt = $conn->prepare("SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = :user_id AND is_read = 0");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['unread_count'];
}

// QR Code
function validateQR($conn, $tour_id)
{
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM qrcode WHERE tour_id = :tour_id");
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
}

function getAllQR($conn, $user_id)
{
    $query = "SELECT qr.id,qr.title,qr.qr_code_path FROM qrcode qr JOIN tours t ON qr.tour_id = t.id JOIN users u ON u.id = t.user_id WHERE u.id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}
function getQR($conn, $tour_id)
{
    $stmt = $conn->prepare("SELECT * FROM qrcode WHERE tour_id = :tour_id");
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// VISIT

function recordVisit($conn, $tourId, $userId)
{
    $home = $conn->prepare("SELECT CASE WHEN home_address LIKE '%Bago%' THEN 'Bago City' ELSE 'Non-Bago City' END AS residence_status FROM users WHERE id = :user_id");
    $home->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $home->execute();
    $homeAddress = $home->fetch()['residence_status'];

    $sql = 'INSERT INTO visit_records (tour_id, user_id, visit_time, city_residence) VALUES (:tour_id, :user_id, NOW(), :homeAddress)';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':tour_id', $tourId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':homeAddress', $homeAddress, PDO::PARAM_STR);

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Visit recorded successfully.'];
    } else {
        return ['success' => false, 'message' => 'Error recording visit.'];
    }
}


function hasVisitedToday($conn, $tourId, $userId)
{
    $sql = 'SELECT COUNT(*) FROM visit_records WHERE tour_id = :tour_id AND user_id = :user_id AND DATE(visit_time) = CURDATE()';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':tour_id', $tourId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    $count = $stmt->fetchColumn();
    return $count > 0;
}


// REVIEW AND RATINGS --------------------------------

function getAllRR($conn, $tour_id)
{
    $stmt = $conn->prepare("
        SELECT AVG(rr.rating) AS average_rating, rr.review, u.name 
        FROM review_rating rr 
        JOIN users u ON rr.user_id = u.id 
        WHERE rr.tour_id = :tour_id
        GROUP BY rr.review, u.name
    ");
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
