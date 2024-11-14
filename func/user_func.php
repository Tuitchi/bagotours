<?php
function getAllToursforAdmin($conn, $query = null)
{
    $sql = "SELECT * FROM tours";

    // Prepare the SQL statement
    if ($query) {
        // Use prepared statements to prevent SQL injection
        $sql .= " WHERE title LIKE :search OR address LIKE :search"; // Adjust fields as necessary
        $stmt = $conn->prepare($sql);
        $searchTerm = "%" . $query . "%";
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
    } else {
        $stmt = $conn->prepare($sql);
    }

    // Execute the prepared statement
    $stmt->execute();

    // Fetch all results
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function getAllTours($conn)
{
    // Updated SQL query to include average rating and review count
    $sql = "
        SELECT t.*, 
               IFNULL(AVG(r.rating), 0) AS average_rating, 
               IFNULL(COUNT(r.id), 0) AS review_count 
        FROM tours t 
        LEFT JOIN review_rating r ON t.id = r.tour_id 
        WHERE t.status = 1 
        GROUP BY t.id
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getAllPopularTours($conn)
{
    // Updated SQL query to include average rating and review count
    $sql = "
WITH booking_visitors AS (
    SELECT t.id, 
           SUM(b.people) AS total_booking_visitors
    FROM tours t
    LEFT JOIN booking b ON t.id = b.tour_id AND b.status = 4
    GROUP BY t.id
),
visit_visitors AS (
    SELECT t.id, 
           COUNT(DISTINCT v.id) AS total_visit_visitors
    FROM tours t
    LEFT JOIN visit_records v ON t.id = v.tour_id
    GROUP BY t.id
)

SELECT t.id, 
       t.title,
       t.img,
       t.type,
       COALESCE(bv.total_booking_visitors, 0) + COALESCE(vv.total_visit_visitors, 0) AS total_visitors,
       COUNT(DISTINCT b.id) AS total_completed_bookings,
       IFNULL(AVG(r.rating), 0) AS average_rating,  -- Average rating
       IFNULL(COUNT(r.id), 0) AS review_count       -- Review count
FROM tours t 
LEFT JOIN booking b ON t.id = b.tour_id AND b.status = 4
LEFT JOIN booking_visitors bv ON t.id = bv.id
LEFT JOIN visit_visitors vv ON t.id = vv.id
LEFT JOIN review_rating r ON t.id = r.tour_id  -- Join with review_rating table
WHERE t.status = 1
GROUP BY t.id, t.title, bv.total_booking_visitors, vv.total_visit_visitors
ORDER BY total_visitors DESC, total_completed_bookings DESC LIMIT 15;
";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getTourById($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM tours WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getEventbyCode($conn, $event_code)
{
    $stmt = $conn->prepare("SELECT * FROM events WHERE event_code = :event_code");
    $stmt->bindParam(":event_code", $event_code, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getEventByDate($conn) {
    $stmt = $conn->prepare("SELECT * FROM events WHERE event_date_start > CURDATE()");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserById($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAverageRating($conn, $tour_id)
{
    $stmt = $conn->prepare("
        SELECT COALESCE(AVG(r.rating), 0) AS average_rating
        FROM review_rating r
        WHERE r.tour_id = :tour_id");
    $stmt->bindParam(":tour_id", $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn() ?: 0;
}
function registerExpiry($conn, $user_id)
{
    $stmt = $conn->prepare("SELECT expiry, id FROM tours WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $date = date('Y-m-d');

    foreach ($tours as $tour) {
        if ($tour['expiry'] == $date) {
            $deleteStmt = $conn->prepare("DELETE FROM tours WHERE user_id = :user_id AND id = :tour_id");
            $deleteStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $deleteStmt->bindParam(':tour_id', $tour['id'], PDO::PARAM_INT);

            if ($deleteStmt->execute()) {
                require_once 'func.php';
                createNotification($conn, $user_id, $tour['id'], "You can register as an owner again.", "form.php", "Upgrade cancelled");
            }
        }
    }
}

function registerStatus($user_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT status FROM tours WHERE user_id = ? LIMIT 1");
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}


function emailAlreadyUsed($conn, $email)
{
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

function usernameAlreadyUsed($conn, $username)
{
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

function getNotificationsCount($conn)
{
    $stmt = $conn->prepare("SELECT COUNT(*) FROM notification WHERE user_id = :user_id AND unread = 1");
    $stmt->bindParam(":user_id", $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn() ?: 0;
}


// booking
function checkBookingStatus($conn, $user_id, $tour_id)
{
    $stmt = $conn->prepare(
        "SELECT status, id FROM booking 
         WHERE user_id = :user_id 
         AND tour_id = :tour_id 
         ORDER BY date_created DESC 
         LIMIT 1"
    );

    // Bind parameters
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->bindParam(":tour_id", $tour_id, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch a single record
}




function isBookable($conn, $tour_id)
{
    $stmt = $conn->prepare("SELECT * FROM tours WHERE id = :tour_id AND bookable = 1");
    $stmt->bindParam(":tour_id", $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}


function userValidation($pageRole)
{
    if (isset($_SESSION['user_id'])) {
        if (!empty($_SESSION['role'])) {
            if ($_SESSION['role'] !== $pageRole) {
                session_unset();
                session_destroy();
                header("Location: ../index.php");
                exit;
            }
        } else {
            session_unset();
            session_destroy();
            header("Location: ../index.php");
            exit;
        }
    } else {
        header("Location: ../index.php");
        exit;
    }
}
