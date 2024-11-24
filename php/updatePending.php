<?php
include '../include/db_conn.php';
require '../func/func.php';

$id = isset($_GET["tour_id"]) ? intval($_GET["tour_id"]) : 0;
$user_id = isset($_GET["user_id"]) ? intval($_GET["user_id"]) : 0;
$status = isset($_GET["status"]) ? intval($_GET["status"]) : 0;

if ($status == 2) {
    $currentDate = date('Y-m-d');
    $newDate = date('Y-m-d', strtotime('+7 days'));
    $stmt = $conn->prepare("UPDATE tours SET status = :status, expiry = :expiry WHERE id = :id");
    $stmt->bindParam(':expiry', $newDate);
    $message = "Tour cancelled by admin";
    $type = "Upgrade Cancelled";
} else {
    $stmt = $conn->prepare("UPDATE tours SET status = :status WHERE id = :id");
    $message = "Tour approved by admin";
    $type = "Upgrade Approved";
}

$stmt->bindParam(':status', $status, PDO::PARAM_INT);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);

if ($stmt->execute()) {
    createNotification($conn, $user_id, $id, $message, "form", "$type");
    header("Location: ../admin/pending?process=success");
    exit();
} else {
    header("Location: ../admin/pending?process=error&message=Failed to update tour status");
    exit();
}
