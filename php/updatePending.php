<?php
include '../include/db_conn.php';
require '../func/func.php';

header('Content-Type: application/json');

try {
    // Check for the presence of necessary parameters
    $tour_id = $_REQUEST['tour_id'] ?? null;
    $user_id = $_REQUEST['user_id'] ?? null;
    $status = $_REQUEST['status'] ?? null;
    
    // If request method is either GET or POST, handle accordingly
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if ($tour_id && $user_id && $status) {
            // Handle approval for GET request (Confirmed status)
            if ($status === 'Confirmed') {
                $stmt = $conn->prepare("UPDATE tours SET status = :status WHERE id = :tour_id AND user_id = :user_id");
                $stmt->bindParam(':status', $status, PDO::PARAM_STR);
                $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    // Create approval notification
                    $message = "Your tourist attraction has been approved! Congratulations on becoming an owner.";
                    $type = "Upgrade Approved";
                    createNotification($conn, $user_id, $tour_id, $message, "form", $type);

                    echo json_encode(['success' => true, 'message' => 'Approval successful.']);
                    exit();
                } else {
                    error_log("Database error: " . implode(" | ", $stmt->errorInfo()));
                    echo json_encode(['success' => false, 'message' => 'Failed to approve the tour.']);
                }
            }

            // Handle rejection for POST request (Rejected status)
            elseif ( $status === 'Rejected') {
                $reason = $_POST['reason'] ?? null;

                if ($reason) {
                    // Calculate expiry date (7 days from now)
                    $expiry_date = date('Y-m-d H:i:s', strtotime('+3 days'));

                    $stmt = $conn->prepare("UPDATE tours SET status = :status, reason = :reason, expiry = :expiry_date WHERE id = :tour_id AND user_id = :user_id");
                    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
                    $stmt->bindParam(':reason', $reason, PDO::PARAM_STR);
                    $stmt->bindParam(':expiry_date', $expiry_date, PDO::PARAM_STR);
                    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        // Create rejection notification
                        $message = "Your tourist attraction application has been rejected. Reason: $reason. Expiry date: $expiry_date.";
                        $type = "Upgrade Rejected";
                        createNotification($conn, $user_id, $tour_id, $message, "form", $type);

                        echo json_encode(['success' => true, 'message' => 'Rejection successful with expiry date set.']);
                    } else {
                        error_log("Database error: " . implode(" | ", $stmt->errorInfo()));
                        echo json_encode(['success' => false, 'message' => 'Failed to reject the tour.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Reason for rejection is required.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid parameters for approval or rejection.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    }
} catch (Exception $e) {
    error_log("Exception occurred: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
}
