<?php
include '../include/db_conn.php';

$id = isset($_GET["tour_id"]) ? intval($_GET["tour_id"]) : 0;
$status = isset($_GET["status"]) ? intval($_GET["status"]) : 0;
$user_id = isset($_GET["user_id"]) ? intval($_GET["user_id"]) : 0;

$stmt = $conn->prepare("UPDATE tours SET status = ? WHERE id = ?");
$stmt->bind_param('ii', $status, $id);

if ($stmt->execute()) {
    if ($user_id > 0) {
        $stmt = $conn->prepare("UPDATE users SET role = 'owner' WHERE id = ?");
        $stmt->bind_param('i', $user_id);

        if ($stmt->execute()) {
            header("Location: ../admin/pending?process=success");
            exit();
        } else {
            header("Location: ../admin/pending?process=error&message=Failed to update user role");
            exit();
        }
    } else {
        header("Location: ../admin/pending?process=error&message=Invalid user ID");
        exit();
    }
} else {
    header("Location: ../admin/pending?process=error&message=Failed to update tour status");
    exit();
}