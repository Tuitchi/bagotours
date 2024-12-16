<?php
require '../include/db_conn.php';
session_start();

// Ensure the user is logged in and the session is valid
if (!isset($_SESSION['user_id'])) {
    header("Location: ../form?error=No User ID");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Promote the user to "owner"
    $updateQuery = "UPDATE users SET role = 'owner' WHERE id = :user_id";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Check if the user has any active tours
        $checkToursQuery = "UPDATE tours SET status = 'Active' WHERE user_id = :user_id";
        $stmt = $conn->prepare($checkToursQuery);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            header("Location: ../owner/home?dsp=intro");
        } else {
            header("Location: ../form?error=Can't Update Tours");
        }
        exit();
    } else {
        header("Location: ../form?error=Update Failed");
        exit();
    }
} catch (PDOException $e) {
    // Log the error for debugging and redirect with a generic error message
    error_log("Database Error: " . $e->getMessage());
    header("Location: ../form?error=Database Error");
    exit();
}
