<?php
include '../include/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $booking_id = $_GET['id'];

    try {
        $query = "DELETE FROM booking WHERE id = :id";
        $stmt = $conn->prepare($query);
        
        $stmt->bindParam(':id', $booking_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            header("Location: ../user/booking?delete=success");
            exit();
        } else {
            header("Location: ../user/booking?delete=error");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        header("Location: ../user/booking?delete=error");
        exit();
    }
} else {
    header("Location: ../user/booking?delete=methodError");
    exit();
}
