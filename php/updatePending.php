<?php
include '../include/db_conn.php';

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
$status = isset($_GET["status"]) ? $_GET["status"] : '';

$stmt = $conn->prepare("UPDATE tours SET status = ? WHERE id = ?");
$stmt->bind_param('ii', $status, $id);

if ($stmt->execute()) {
    header("Location: ../admin/pending?process=success");
    exit();
} else {
    header("Location: ../admin/pending?process=error");
    exit();
}
