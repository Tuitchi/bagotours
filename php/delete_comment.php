<?php
require '../include/db_conn.php';
$data = $_POST;
$commentId = $data['comment_id'];


try {
    $stmt = $conn->prepare("DELETE FROM review_rating WHERE id = :comment_id");
    $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
    $stmt->execute();

    // Return success response
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // Return error response
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>