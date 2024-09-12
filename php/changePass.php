<?php
session_start();
include_once '../include/db_conn.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($newPassword !== $confirmPassword) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match!"]);
        exit();
    }
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify the old password
    if (!password_verify($oldPassword, $user['password'])) {
        echo json_encode(["status" => "error", "message" => "Old password is incorrect!"]);
        exit();
    }

    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashedNewPassword, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Password updated successfully!"]);
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update password!"]);
        exit();
    }
}
