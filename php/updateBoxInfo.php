<?php
include '../include/db_conn.php';
session_start();

$user_id = $_GET['id']; // User ID
$tour_id = isset($_GET['tour']) ? $_GET['tour'] : null; // Optional tour filter
$time_filter = isset($_GET['time']) ? $_GET['time'] : null; // Optional time filter

require_once __DIR__ . '/../func/dashboardFunc.php';

$response = [
    'total_visitors' => totalVisitors($conn, $user_id, $tour_id, $time_filter),
    'average_stars' => averageStars($conn, $user_id, $tour_id, $time_filter),
    'total_tours' => totalTours($conn, $user_id, $tour_id),
];

// Return JSON response
echo json_encode($response);

