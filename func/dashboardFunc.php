<?php
function totalUser ($conn) {
    $query_book = "SELECT COUNT(*) AS total_users FROM users";
    $stmt_book = $conn->prepare($query_book);
    $stmt_book->execute();
    return $stmt_book->fetchColumn() ?? 0;
}
function totalPending ($conn) {
    $query = "SELECT COUNT(*) AS total_pending FROM tours WHERE status = '0'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $totalPending = $stmt->fetchColumn();
    return $totalPending == 0 ? "" : $totalPending;
}
function totalBooking ($conn) {
    $query = "SELECT COUNT(*) AS total_booking FROM booking b JOIN tours t ON b.tour_id = t.id ";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $totalBooking = $stmt->fetchColumn();
    return $totalBooking == 0 ? "" : $totalBooking;
}

function totalStars($conn){
    $query_star = "SELECT SUM(rating) AS total_stars FROM review_rating rr JOIN tours t ON rr.tour_id = t.id JOIN users u ON t.user_id = u.id WHERE u.role = 'admin'";
    $stmt_star = $conn->prepare($query_star);
    $stmt_star->execute();
    return $stmt_star->fetchColumn() ?? 0;
}
function totalTours($conn) {
    $query_tours = "SELECT COUNT(*) AS total_tours FROM tours WHERE status = 1";
    $stmt_tours = $conn->prepare($query_tours);
    $stmt_tours->execute();
    return $stmt_tours->fetchColumn() ?? 0;
}

function totalVisitors($conn){
    $query_visit = "SELECT COUNT(*) AS total_visit FROM visit_records vr JOIN tours t ON vr.tour_id = t.id JOIN users u ON t.user_id = u.id WHERE u.role = 'admin';";
    $stmt_visit = $conn->prepare($query_visit);
    $stmt_visit->execute();
    return $stmt_visit->fetchColumn() ?? 0;
}

function nonBago($conn){
    $query_nonbago = "SELECT COUNT(*) AS total_visit FROM visit_records vr JOIN tours t ON vr.tour_id = t.id JOIN users u ON t.user_id = u.id WHERE u.role = 'admin' AND vr.city_residence = 'Non-Bago City';";
    $stmt_nonbago = $conn->prepare($query_nonbago);
    $stmt_nonbago->execute();
    return $stmt_nonbago->fetchColumn() ?? 0;
}
function Bago($conn){
    $query_bago = "SELECT COUNT(*) AS total_visit FROM visit_records vr JOIN tours t ON vr.tour_id = t.id JOIN users u ON t.user_id = u.id WHERE u.role = 'admin' AND vr.city_residence = 'Bago City';";
    $stmt_bago = $conn->prepare($query_bago);
    $stmt_bago->execute();
    return $stmt_bago->fetchColumn() ?? 0;
}
