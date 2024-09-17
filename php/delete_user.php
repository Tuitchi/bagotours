<?php
include '../include/db_conn.php';
session_start();

// Ensure the user is logged in and has the necessary permission (e.g., admin role).
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// Check if ID is provided in the GET request
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Convert to integer for security

    // Validate that the ID is greater than zero
    if ($id > 0) {
        $sql = 'DELETE FROM users WHERE id = ?';

        // Prepare the SQL statement
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('i', $id); // Bind the ID parameter

            // Execute the statement and check if rows were affected
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Unable to delete user. User may not exist.']);
                }
            } else {
                // If execute fails, log the error and return a generic message
                error_log('Error executing delete: ' . $stmt->error);
                echo json_encode(['success' => false, 'message' => 'Error executing deletion.']);
            }

            $stmt->close();
        } else {
            error_log('Error preparing statement: ' . $conn->error);
            echo json_encode(['success' => false, 'message' => 'Error preparing statement.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No user ID specified.']);
}

// Close the database connection
$conn->close();
?>
