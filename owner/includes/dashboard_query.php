<?php
$query_book = "SELECT COUNT(*) AS total_books FROM booking b JOIN tours t ON b.tours_id = t.id JOIN users u ON u.id = t.user_id";
$stmt_book = $conn->prepare($query_book);
$stmt_book->execute();
$total_books = $stmt_book->fetchColumn();

$query_star = "SELECT SUM(rating) AS total_stars FROM review_rating WHERE tour_id = :tour_id";
$stmt_star = $conn->prepare($query_star);
$stmt_star->bindParam(':tour_id', $tour, PDO::PARAM_INT);
$stmt_star->execute();
$total_stars = $stmt_star->fetchColumn();


$query_tours = "SELECT COUNT(*) AS total_tours FROM tours WHERE status = 1";
$stmt_tours = $conn->prepare($query_tours);
$stmt_tours->execute();
$total_tours = $stmt_tours->fetchColumn();

$query_visit = "SELECT COUNT(*) AS total_visit FROM visit_records vr JOIN tours t ON vr.tour_id = t.id JOIN users u ON t.user_id = u.id WHERE u.role = 'admin';";
$stmt_visit = $conn->prepare($query_visit);
$stmt_visit->execute();
$total_visit = $stmt_visit->fetchColumn();

$query_nonbago = "SELECT COUNT(*) AS total_visit FROM visit_records vr JOIN tours t ON vr.tour_id = t.id JOIN users u ON t.user_id = u.id WHERE u.role = 'admin' AND vr.city_residence = 'Non-Bago City';";
$stmt_nonbago = $conn->prepare($query_nonbago);
$stmt_nonbago->execute();
$nonbago_visit = $stmt_nonbago->fetchColumn();
