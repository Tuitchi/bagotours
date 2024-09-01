<?php
include '../include/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $tour_id = $_POST['tour_id'];
    $phone = $_POST['phone'];
    $datetime = $_POST['date'] . ' ' . $_POST['time'];
    $people = $_POST['people'];
    $status = '0';

    $stmt = $conn->prepare("INSERT INTO booking (user_id, tours_id, phone_number, date_sched, people, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissis", $user_id, $tour_id, $phone, $datetime, $people, $status);

    if ($stmt->execute()) {
        header("Location: ../user/tour?tours=$tour_id&status=success");
        exit();
    } else {
        header("Location: ../user/tour?tours=$tour_id&status=error");
        exit();
    }
}
