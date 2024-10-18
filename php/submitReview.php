<?php
session_start();
include '../include/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $tour_id = $_POST['tour_id'];
    $rating = $_POST['rating'];
    $review = trim($_POST['review']);
    $photo = null;
    require_once '../func/func.php';
    addReview($conn, $tour_id, $user_id, $rating, $review); 
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
