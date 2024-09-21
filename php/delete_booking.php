<?php
include '../include/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = $_POST['booking_id'];

    try {
        $query = "DELETE FROM booking WHERE id = :id";
        $stmt = $conn->prepare($query);
        
        $stmt->bindParam(':id', $booking_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete booking.']);
        }
    } catch (PDOException $e) {
        // Handle any errors
        error_log("Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to delete booking.']);
    }
}
