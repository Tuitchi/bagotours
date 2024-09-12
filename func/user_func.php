<?php
include("../include/db_conn.php");

function getAllTours($conn)
{
    $sql = "SELECT * FROM tours WHERE status = 1";
    $result = mysqli_query($conn, $sql);
    $tours = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $tours[] = $row;
        }
    }
    return $tours;
}

function getTourById($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM tours WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
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
        tours t ON b.tours_id = t.id
    WHERE 
        b.user_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    return $bookings;
}
function getUserById($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}
function getAverageRating($conn, $tour_id)
{
    $stmt = $conn->prepare("
        SELECT COALESCE(AVG(r.rating), 0) AS average_rating
        FROM review_rating r
        WHERE r.tour_id = ?");
    $stmt->bind_param("i", $tour_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['average_rating'];
    } else {
        return 0;
    }
}

function getTourImages($conn, $tourId)
{
    $stmt = $conn->prepare("SELECT * FROM tours_image WHERE tours_id = ?");
    $stmt->bind_param("i", $tourId);
    $stmt->execute();
    $result = $stmt->get_result();
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
    return $images;
}

function getAllPopular($conn)
{
    $stmt = $conn->prepare("SELECT t.id, t.title, t.address, t.type, t.img, COUNT(rr.id) AS rating_count FROM tours t INNER JOIN review_rating rr ON t.id = rr.tour_id GROUP BY t.id ORDER BY rating_count DESC;");
    $stmt->execute();
    $result = $stmt->get_result();
    $popularTours = [];
    while ($row = $result->fetch_assoc()) {
        $popularTours[] = $row;
    }
    return $popularTours;
}

function alreadyRegistered($user_id) {
    global $conn;

    $sql = "SELECT COUNT(*) FROM tours WHERE user_id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        return $count > 0;
    } else {
        return false;
    }
}