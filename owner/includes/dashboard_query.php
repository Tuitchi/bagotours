<?php
$tour_id = isset($_SESSION["tour_id"]) ? $_SESSION["tour_id"] : 0;

$query_count = "SELECT COUNT(*) AS total_books FROM booking WHERE tours_id = ?";
$stmt_count = $conn->prepare($query_count);
$stmt_count->bind_param("i", $tour_id);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_books = $result_count->fetch_assoc()['total_books'];

$query_inq = "SELECT COUNT(*) as total_inquiry FROM inquiry WHERE tour_id = ?";
$stmt_inq = $conn->prepare($query_inq);
$stmt_inq->bind_param("i", $tour_id);
$stmt_inq->execute();
$result_inq = $stmt_inq->get_result();
$total_inq = $result_inq->fetch_assoc()['total_inquiry'];

$stmt_count->close();
$stmt_inq->close();
