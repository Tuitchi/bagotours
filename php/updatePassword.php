<?php
include '../include/db_conn.php';
session_start();

$user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];

// Check if the current password matches the stored password
$query = "SELECT password FROM users WHERE id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($current_password, $user['password'])) {
    // Update the password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $update_query = "UPDATE users SET password = :new_password WHERE id = :user_id";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bindParam(':new_password', $hashed_password);
    $update_stmt->bindParam(':user_id', $user_id);

    if ($update_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
}
?>
