<?php
header('Content-Type: application/json');
include '../../include/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid fee ID provided.']);
        exit();
    }

    $feeId = intval($_POST['id']);

    try {
        $stmt = $conn->prepare("DELETE FROM fees WHERE id = :id OR id IN (SELECT id FROM fees WHERE id = :id)");
        $stmt->execute([':id' => $feeId]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Fee successfully deleted.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No fee found with the provided ID.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
