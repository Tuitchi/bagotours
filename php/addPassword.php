<?php
session_start();
require_once '../include/db_conn.php';

$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$userId = $_SESSION['user_id'];

if (empty($password) || empty($confirm_password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
    exit();
}
if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
    exit();
}
if (strlen($confirm_password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
    exit();
}
if ($password == $confirm_password) {
    try {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $updateStmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
        $updateStmt->execute([
            ':password' => $passwordHash,
            ':id' => $userId
        ]);

        echo json_encode(['success' => true, 'message' => 'Password successfully updated.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to update password. Please try again later.']);
        error_log($e->getMessage()); // Log error for debugging (optional)
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
    exit();
}

