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

    // Check if new password and confirm password match
    if ($newPassword !== $confirmPassword) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match!"]);
        exit();
    }

    // Fetch the current password of the user from the database
    try {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify the old password
        if (!password_verify($oldPassword, $user['password'])) {
            echo json_encode(["status" => "error", "message" => "Old password is incorrect!"]);
            exit();
        }

        // Hash the new password
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in the database
        $updateStmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
        $updateStmt->bindParam(':password', $hashedNewPassword, PDO::PARAM_STR);
        $updateStmt->bindParam(':id', $user_id, PDO::PARAM_INT);

        // Execute the update and check if successful
        if ($updateStmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Password updated successfully!"]);
            exit();
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update password!"]);
            exit();
        }
    } catch (PDOException $e) {
        // Handle errors and log the exception
        error_log("Error: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => "An error occurred while updating the password!"]);
        exit();
    }
}
