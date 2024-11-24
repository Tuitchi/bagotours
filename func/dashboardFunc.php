<?php
function totalUser($conn)
{
    $query_book = "SELECT COUNT(*) AS total_users FROM users";
    $stmt_book = $conn->prepare($query_book);
    $stmt_book->execute();
    return $stmt_book->fetchColumn() ?? 0;
}
function totalPending($conn)
{
    $query = "SELECT COUNT(*) AS total_pending FROM tours WHERE status = '0'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $totalPending = $stmt->fetchColumn();
    return $totalPending == 0 ? "" : $totalPending;
}
function totalBooking($conn, $user_id)
{
    $query = "SELECT COUNT(*) AS total_booking FROM booking b JOIN tours t ON b.tour_id = t.id JOIN users u ON t.user_id = u.id WHERE u.id = :id AND b.status != 4 AND b.status != 2 AND b.status != 3";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $totalBooking = $stmt->fetchColumn();
    return $totalBooking == 0 ? "" : $totalBooking;
}

function averageStars($conn, $user_id)
{
    $query = "
        SELECT 
            SUM(rr.rating) AS total_stars, 
            COUNT(rr.rating) AS total_reviews 
        FROM review_rating rr
        JOIN tours t ON rr.tour_id = t.id
        JOIN users u ON t.user_id = u.id
        WHERE u.id = :id
    ";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalStars = $result['total_stars'] ?? 0;
    $totalReviews = $result['total_reviews'] ?? 0;

    // Calculate the average, ensuring no division by zero
    return $totalReviews > 0 ? round($totalStars / $totalReviews, 1) : 0;
}

function totalTours($conn)
{
    $query_tours = "SELECT COUNT(*) AS total_tours FROM tours WHERE status = 1";
    $stmt_tours = $conn->prepare($query_tours);
    $stmt_tours->execute();
    return $stmt_tours->fetchColumn() ?? 0;
}

function totalVisitors($conn, $user_id)
{
    $query = "SELECT COUNT(*) AS total_visit FROM visit_records vr JOIN tours t ON vr.tour_id = t.id JOIN users u ON t.user_id = u.id WHERE u.id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn() ?? 0;
}

function nonBago($conn, $user_id)
{
    $query = "SELECT COUNT(*) AS total_visit FROM visit_records vr JOIN tours t ON vr.tour_id = t.id JOIN users u ON t.user_id = u.id WHERE u.id = :id AND vr.city_residence = 'Non-Bago City';";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn() ?? 0;
}
function Bago($conn, $user_id)
{
    $query = "SELECT COUNT(*) AS total_visit FROM visit_records vr JOIN tours t ON vr.tour_id = t.id JOIN users u ON t.user_id = u.id WHERE u.id = :id AND vr.city_residence = 'Bago City';";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn() ?? 0;
}
