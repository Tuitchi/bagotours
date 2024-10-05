<?php
include("../include/db_conn.php");

function getAllToursforAdmin($conn)
{
    $sql = "SELECT * FROM tours WHERE status = 1 OR status = 3";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getAllTours($conn)
{
    $sql = "SELECT * FROM tours WHERE status = 1";
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
function getTourImageById($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM tours_image WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getBookingById($conn, $id)
{
    $stmt = $conn->prepare("SELECT 
        b.id AS booking_id,
        t.title AS tour_title,
        b.date_sched AS date_scheduled,
        b.people AS number_of_people,
        CASE 
            WHEN b.status = 1 THEN 'Approved'
            WHEN b.status = 2 THEN 'Disapproved'
            ELSE 'Pending'
        END AS status
    FROM 
        booking b
    JOIN 
        tours t ON b.tour_id = t.id
    WHERE 
        b.user_id = :user_id");
    $stmt->bindParam(":user_id", $id, PDO::PARAM_INT);
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

function getTourImages($conn, $tourId)
{
    $stmt = $conn->prepare("SELECT * FROM tours_image WHERE tour_id = :tour_id");
    $stmt->bindParam(":tour_id", $tourId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllPopular($conn)
{
    $stmt = $conn->prepare("SELECT t.id, t.title, t.address, t.type, t.img, COUNT(rr.id) AS rating_count 
        FROM tours t 
        INNER JOIN review_rating rr ON t.id = rr.tour_id 
        GROUP BY t.id 
        ORDER BY rating_count DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Register Owner

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
                createNotification($conn, $user_id, $tour['id'], "You can Register as an owner again.", "form.php", "Upgrade cancelled");
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
function isAlreadyBook($conn, $user_id, $tour_id)
{
    $stmt = $conn->prepare("SELECT * FROM booking WHERE user_id = :user_id AND tour_id = :tour_id");
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->bindParam(":tour_id", $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

function isBookable($conn, $tour_id)
{
    $stmt = $conn->prepare("SELECT * FROM tours WHERE id = :tour_id AND bookable = 1");
    $stmt->bindParam(":tour_id", $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}
