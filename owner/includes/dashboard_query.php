<?php 
$query_count = "SELECT COUNT(*) AS total_books FROM booking WHERE tours_id = $tours_id";
$result_count = mysqli_query($conn, $query_count);
$total_users = mysqli_fetch_assoc($result_count)['total_books'];

$query_pending = "SELECT COUNT(*) as total_pending FROM tours WHERE status = 0";
$result_pending = mysqli_query($conn, $query_pending);
$total_pending = mysqli_fetch_assoc($result_pending)['total_pending'];

$query_tours = "SELECT COUNT(*) as total_tours FROM tours WHERE status = 1";
$result_tours = mysqli_query($conn, $query_tours);
$total_tours = mysqli_fetch_assoc($result_tours)['total_tours'];
?>
