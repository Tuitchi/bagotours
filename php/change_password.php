<?php
session_start();
require_once '../include/db_conn.php';

$oldPassword = $_POST['oldPassword'] ?? '';
$newPassword = $_POST['newPassword'] ?? '';
$userId = $_SESSION['user_id'] ?? 0;

if (empty($oldPassword) || empty($newPassword)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
    exit;
}
if (strlen($newPassword) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long.']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($oldPassword, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Old password is incorrect.']);
        exit;
    }

    $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);

    $updateStmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
    $updateStmt->execute([
        ':password' => $newPasswordHash,
        ':id' => $userId
    ]);

    echo json_encode(['success' => true, 'message' => 'Password successfully updated.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to update password. Please try again later.']);
    error_log($e->getMessage()); // Log error for debugging (optional)
}
