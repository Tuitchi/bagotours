<?php
session_start();
require '../include/db_conn.php';

function updateNotificationStatus($conn, $notificationId) {
    $sql = 'UPDATE notifications SET is_read = 1 WHERE id = :id';

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bindParam(':id', $notificationId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Failed to update notification status.'];
        }
    } else {
        return ['success' => false, 'message' => 'Error preparing statement.'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $notificationId = intval($_POST['id']);

    if ($notificationId > 0) {
        $response = updateNotificationStatus($conn, $notificationId);
        echo json_encode($response);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid notification ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No notification ID specified.']);
}
