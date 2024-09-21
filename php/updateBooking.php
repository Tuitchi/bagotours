<?php
include '../include/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    $query = "UPDATE booking SET status = :status WHERE id = :id";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $booking_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement.']);
    }
}
?>
