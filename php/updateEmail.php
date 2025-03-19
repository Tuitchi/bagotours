<?php
include '../include/db_conn.php';
session_start();

$user_id = $_SESSION['user_id'];
$new_email = $_POST['new_email'];

// Check if the email already exists
$query = "SELECT id FROM users WHERE email = :new_email";
$stmt = $conn->prepare($query);
$stmt->bindParam(':new_email', $new_email);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => false, 'message' => 'Email is already in use']);
} else {
    // Update the email
    $update_query = "UPDATE users SET email = :new_email WHERE id = :user_id";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bindParam(':new_email', $new_email);
    $update_stmt->bindParam(':user_id', $user_id);

    if ($update_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Email changed successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update email']);
    }
}
?>
