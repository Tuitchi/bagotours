<?php
include '../include/db_conn.php';
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id > 0) {
        $sql = 'DELETE FROM users WHERE id = ?';

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('i', $id);

            if ($stmt->execute() && $stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'user deleted successfully.']);
                exit();
            } else {
                echo json_encode(['success' => false, 'message' => 'Unable to delete user. User may not exist.']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error preparing statement.']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No user ID specified.']);
    exit();
}
