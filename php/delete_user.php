<?php
include '../include/db_conn.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id > 0) {
        $sql = 'DELETE FROM users WHERE id = :id';

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Unable to delete user. User may not exist.']);
                }
            } else {
                error_log('Error executing delete: ' . implode(' ', $stmt->errorInfo()));
                echo json_encode(['success' => false, 'message' => 'Error executing deletion.']);
            }
        } catch (PDOException $e) {
            error_log('Error preparing statement: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error preparing statement.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No user ID specified.']);
}
