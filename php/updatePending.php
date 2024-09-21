<?php
include '../include/db_conn.php';

$id = isset($_GET["tour_id"]) ? intval($_GET["tour_id"]) : 0;
$status = isset($_GET["status"]) ? intval($_GET["status"]) : 0;

if ($status == 2) {
    $currentDate = date('Y-m-d');
    $newDate = date('Y-m-d', strtotime('+7 days'));
    $stmt = $conn->prepare("UPDATE tours SET status = ?, expiry = '$newDate' WHERE id = ?");
} else {
    $stmt = $conn->prepare("UPDATE tours SET status = ? WHERE id = ?");
}

$stmt->bind_param('ii', $status, $id);

if ($stmt->execute()) {
    header("Location: ../admin/pending?process=success");
    exit();
} else {
    header("Location: ../admin/pending?process=error&message=Failed to update tour status");
    exit();
}
