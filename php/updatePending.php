<?php
include '../include/db_conn.php';

$id = isset($_GET["tour_id"]) ? intval($_GET["tour_id"]) : 0;
$status = isset($_GET["status"]) ? $_GET["status"] : '';

$stmt = $conn->prepare("UPDATE tours SET status = ? WHERE id = ?");
$stmt->bind_param('ii', $status, $id);

if ($stmt->execute()) {
    $user_id = isset($_GET["user_id"]) ? intval($_GET["user_id"]) : 0;
    $stmt = $conn->prepare("UPDATE users SET role = 'owner' WHERE id = ?");
    $stmt->bind_param('i',$user_id);
    header("Location: ../admin/pending?process=success");
    exit();
} else {
    header("Location: ../admin/pending?process=error");
    exit();
}
