<?php
require '../include/db_conn.php';


$data = $_POST;  // Get the data from the POST request

$commentId = $data['comment_id'];
$review = $data['review'];  // The new comment text

$currentTimestamp = date('Y-m-d H:i:s');
try {
    $stmt = $conn->prepare("UPDATE review_rating SET review = :review, date_updated = :date WHERE id = :comment_id");
    $stmt->bindParam(':review', $review, PDO::PARAM_STR);
    $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
    $stmt->bindParam(':date', $currentTimestamp, PDO::PARAM_STR);


    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'updatedReview' => htmlspecialchars($review)]);
    }

    // Return a success response with the updated review text
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>