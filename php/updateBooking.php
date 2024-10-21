<?php
include '../include/db_conn.php';
require_once '../func/func.php';

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
$user_id = isset($_GET["user"]) ? intval($_GET["user"]) : 0;
$tour_id = isset($_GET["tour"]) ? intval($_GET["tour"]) : 0;
$status = isset($_GET["status"]) ? intval($_GET["status"]) : 0;

try {
    $stmt = $conn->prepare("SELECT * FROM tours WHERE id = :tour_id");
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    $tour = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tour) {
        $stmt = $conn->prepare("UPDATE booking SET status = $status WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($status == 2) {
            createNotification($conn, $user_id, $tour_id, $tour['title'] . " has accepted your reservation.", "booking", "booking");
        } else {
            createNotification($conn, $user_id, $tour_id, $tour['title'] . " has declined your reservation.", "booking", "booking");
        }

        // Execute the update query
        if ($stmt->execute()) {
            header("Location: ../admin/booking?process=success");
            exit();
        } else {
            header("Location: ../admin/booking?process=error&message=Failed to update booking status");
            exit();
        }
    } else {
        header("Location: ../admin/booking?process=error&message=Tour not found");
        exit();
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header("Location: ../admin/booking?process=error&message=Database error");
    exit();
}
