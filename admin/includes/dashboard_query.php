<?php
$query_count = "SELECT COUNT(*) AS total_users FROM users";
$stmt_count = $conn->prepare($query_count);
$stmt_count->execute();
$total_users = $stmt_count->fetchColumn();

$query_pending = "SELECT COUNT(*) AS total_pending FROM tours WHERE status = 0";
$stmt_pending = $conn->prepare($query_pending);
$stmt_pending->execute();
$total_pending = $stmt_pending->fetchColumn();

$query_tours = "SELECT COUNT(*) AS total_tours FROM tours WHERE status = 1";
$stmt_tours = $conn->prepare($query_tours);
$stmt_tours->execute();
$total_tours = $stmt_tours->fetchColumn();
?>
