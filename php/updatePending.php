<?php
include '../include/db_conn.php';
require '../func/func.php';

$id = isset($_GET["tour_id"]) ? intval($_GET["tour_id"]) : 0;
$user_id = isset($_GET["user_id"]) ? intval($_GET["user_id"]) : 0;
$status = $_GET['status'];

if ($status == 'Rejected') {
    $currentDate = date('Y-m-d');
    $newDate = date('Y-m-d', strtotime('+7 days'));
    $stmt = $conn->prepare("UPDATE tours SET status = :status, expiry = :expiry WHERE id = :id");
    $stmt->bindParam(':expiry', $newDate);
    $message = "Your application has rejected by admin - try again next 7 days.";
    $type = "Upgrade Cancelled";
} else {
    $stmt = $conn->prepare("UPDATE tours SET status = :status WHERE id = :id");
    $message = "Your tourist attraction has approved and you're an owner now - Congratulations!";
    $type = "Upgrade Approved";
}

$stmt->bindParam(':status', $status, PDO::PARAM_STR);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);

if ($stmt->execute()) {
    createNotification($conn, $user_id, $id, $message, "form", "$type");
    header("Location: ../admin/pending?process=success");
    exit();
} else {
    header("Location: ../admin/pending?process=error&message=Failed to update tour status");
    exit();
}
