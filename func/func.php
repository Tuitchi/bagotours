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
    if ($stmt->execute()) {
        return "success";
    } else {
        return "error";
    }
}
function createNotificationForAllUsers($conn, $tour_id, $message, $url, $type)
{
    // Retrieve all user IDs
    $stmt = $conn->query("SELECT id FROM users");
    $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Loop through each user ID and send the notification
    foreach ($userIds as $userId) {
        createNotification($conn, $userId, $tour_id, $message, $url, $type);
    }
}


function getNotifications($conn, $userId)
{
    $stmt = $conn->prepare("SELECT id, message, url, is_read, created_at  FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC");
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

function markNotificationAsRead($conn, $notificationId)
{
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = :notification_id");
    $stmt->bindParam(':notification_id', $notificationId, PDO::PARAM_INT);
    $stmt->execute();
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

// VISIT -------------------------------------------------------

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
function addReview($conn, $tour_id, $user_id, $rating, $review)
{
    $stmt = $conn->prepare("INSERT INTO review_rating (tour_id, user_id, rating, review) VALUES (:tour_id, :user_id, :rating, :review)");
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
    $stmt->bindParam(':review', $review, PDO::PARAM_STR);
    if ($stmt->execute()) {
        $stmtUpdate = $conn->prepare("UPDATE booking SET is_review = 1 WHERE tour_id = :tour_id AND user_id = :user_id");
        $stmtUpdate->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        if ($stmtUpdate->execute()) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function ReviewAction($conn)
{
    $stmt = $conn->prepare("SELECT id FROM booking WHERE status = 4 AND is_review = 0");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($results) > 0) {
        return $results;
    } else {
        return false;
    }
}
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
function getAverageRatingNew($conn, $tourId)
{

    // Step 2: Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT AVG(rating) AS average_rating FROM review_rating WHERE tour_id = :tour_id");
    $stmt->execute(['tour_id' => $tourId]);

    // Step 3: Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the average_rating is not null
    if ($result['average_rating'] !== null) {
        return round($result['average_rating'], 2); // Round to 2 decimal places
    }

    return 0; // Return 0 if there are no ratings
}
function displayRatingStars($averageRating)
{
    $stars = "";
    for ($i = 0; $i < 5; $i++) {
        if ($i < floor($averageRating)) {
            $stars .= "<i class='bx bxs-star' ></i>"; // Full star
        } elseif ($i < ceil($averageRating)) {
            $stars .= "<i class='bx bxs-star-half'></i>"; // You can replace this with a half star if you want to include half ratings
            // For half star, you can use a different symbol like "⭐½"
        } else {
            $stars .= "<i class='bx bx-star' ></i>"; // Empty star
        }
    }
    return $stars;
}

// BOOKING_______________________________________________________
function specificBooking($conn, $user_id)
{
    $stmt = $conn->prepare("SELECT t.title, t.id
                            FROM booking b
                            JOIN tours t ON b.tour_id = t.id
                            WHERE b.user_id = :user_id AND b.status = 4
                            LIMIT 1");

    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch();
}

function getBooking($conn, $user_id)
{
    $stmt = $conn->prepare("SELECT b.*, t.title as tour_title, u.username FROM booking b
          JOIN tours t ON b.tour_id = t.id
          JOIN users u ON b.user_id = u.id WHERE t.user_id = :user_id
          ORDER BY b.date_sched DESC");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getBookingbyID($conn, $booking_id)
{
    $stmt = $conn->prepare("SELECT b.*, t.title as tour_title, u.username FROM booking b
          JOIN tours t ON b.tour_id = t.id
          JOIN users u ON b.user_id = u.id WHERE b.id = :id
          ORDER BY b.date_sched DESC");
    $stmt->bindParam(':id', $booking_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function alreadyBook($conn, $user_id, $tour_id)
{
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM booking WHERE user_id = :user_id AND tour_id = :tour_id AND status = 1");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch()['count'] > 0;
}
function checkBooking($conn, int $tour_id)
{
    $stmt = $conn->prepare("
        SELECT u.name AS name, t.title AS title, t.user_id AS owner_id, b.* 
        FROM booking b 
        JOIN users u ON u.id = b.user_id 
        JOIN tours t ON t.id = b.tour_id 
        WHERE tour_id = :tour_id AND b.status = 1 OR b.status = 3
    ");
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->execute();

    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($bookings as $booking) {
        $dateSched = new DateTime($booking['date_sched']);
        $sched = new DateTime($booking['date_sched']);
        $dateSched->modify('-1 day'); // Check for notifications the day before
        $today = new DateTime(); // Current date

        // If tomorrow's date matches, notify that the booking is tomorrow
        if ($dateSched <= $today && $sched > $today) {
            createNotification(
                $conn,
                $booking['owner_id'],
                $booking['tour_id'],
                $booking['name'] . " will arrive tomorrow at " . $booking['title'],
                "../php/phpmailer.php?id=" . $booking['id'],
                "booking"
            );
        }
        // If the scheduled date is today, notify the owner of today's arrival
        elseif ($sched <= $today) {
            createNotification(
                $conn,
                $booking['owner_id'],
                $booking['tour_id'],
                $booking['name'] . " will arrive today at " . $booking['title'],
                "booking?complete=true&id=" . $booking['id'],
                "booking"
            );
        }
    }
}

// Event
function addEventValidator($conn, $event_name)
{
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM events WHERE event_name = :event_name");
    $stmt->bindParam(':event_name', $event_name, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch()['count'] > 0;
}

// TOUR FUNCTIONS

function timeAgo($timestamp)
{
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time_ago;

    $seconds = $time_difference;
    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    $weeks = round($seconds / 604800);
    $months = round($seconds / 2629440); // ~30.44 days
    $years = round($seconds / 31553280); // ~365.24 days

    // Determine the appropriate time frame and format
    if ($seconds < 60) {
        return ($seconds == 1) ? "one second ago" : "$seconds seconds ago";
    } elseif ($minutes < 60) {
        return ($minutes == 1) ? "one minute ago" : "$minutes minutes ago";
    } elseif ($hours < 24) {
        return ($hours == 1) ? "one hour ago" : "$hours hours ago";
    } elseif ($days < 7) {
        return ($days == 1) ? "one day ago" : "$days days ago";
    } elseif ($weeks < 4) {
        return ($weeks == 1) ? "one week ago" : "$weeks weeks ago";
    } elseif ($months < 12) {
        return ($months == 1) ? "one month ago" : "$months months ago";
    } else {
        return ($years == 1) ? "one year ago" : "$years years ago";
    }
}
function isDuplicateReview($conn, $tour_id, $user_id)
{
    $stmt = $conn->prepare("SELECT COUNT(*) FROM review_rating WHERE tour_id = :tour_id AND user_id = :user_id");
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}

function getPricingForTour($conn, $tour_id)
{
    if (empty($tour_id)) {
        error_log("Error: tour_id is empty.");
        return null;
    }
    $stmt = $conn->prepare("
        SELECT 
            id, 
            name AS item_name, 
            description, 
            amount
        FROM accommodations 
        WHERE tour_id = :tour_id
        UNION
        SELECT 
            id, 
            name AS item_name, 
            description, 
            amount
        FROM fees 
        WHERE tour_id = :tour_id;
    ");
    $stmt->execute([':tour_id' => $tour_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
