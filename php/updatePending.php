<?php
include '../include/db_conn.php';
require '../func/func.php';

try {
    // Handle POST and GET inputs
    $tour_id = $_POST['tour_id'] ?? $_GET['tour_id'] ?? 0;
    $user_id = $_POST['user_id'] ?? $_GET['user_id'] ?? 0;
    $status = $_POST['status'] ?? $_GET['status'] ?? '';
    $reason = $_POST['reason'] ?? null;

    // Validate inputs
    if (empty($tour_id) || empty($user_id) || empty($status)) {
        header("Location: ../admin/pending?process=error&message=Invalid input data");
        exit();
    }

    // Prepare status-specific updates
    if ($status === 'Rejected') {
        $currentDate = date('Y-m-d');
        $newDate = date('Y-m-d', strtotime('+7 days'));
        $stmt = $conn->prepare("UPDATE tours SET status = :status, expiry = :expiry, rejection_reason = :reason WHERE id = :id");
        $stmt->bindParam(':expiry', $newDate, PDO::PARAM_STR);
        $stmt->bindParam(':reason', $reason, PDO::PARAM_STR);
        $message = "Your application has been rejected by the admin. You can try again after 7 days.";
        $type = "Upgrade Cancelled";
    } else if ($status === 'Approved') {
        $stmt = $conn->prepare("UPDATE tours SET status = :status WHERE id = :id");
        $message = "Your tourist attraction has been approved! Congratulations on becoming an owner.";
        $type = "Upgrade Approved";
    } else {
        header("Location: ../admin/pending?process=error&message=Invalid status value");
        exit();
    }

    // Bind common parameters
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':id', $tour_id, PDO::PARAM_INT);

    // Execute the query
    if ($stmt->execute()) {
        // Create notification
        createNotification($conn, $user_id, $tour_id, $message, "form", $type);

        // Redirect on success
        header("Location: ../admin/pending?process=success");
        exit();
    } else {
        // Log the error and redirect on failure
        error_log("Database error: " . implode(" | ", $stmt->errorInfo()));
        header("Location: ../admin/pending?process=error&message=Failed to update tour status");
        exit();
    }
} catch (Exception $e) {
    // Log unexpected errors and redirect
    error_log("Exception occurred: " . $e->getMessage());
    header("Location: ../admin/pending?process=error&message=An unexpected error occurred");
    exit();
}
