<?php
include '../include/db_conn.php';

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']); // Ensure the ID is an integer for security

    $query = "SELECT * FROM users WHERE id = :id";
    try {
        // Prepare the statement
        $stmt = $conn->prepare($query);
        // Bind the ID parameter
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        // Execute the query
        $stmt->execute();

        // Check if any user is found
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error preparing statement: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

// No need to manually close the connection; PDO will do this when the script ends
