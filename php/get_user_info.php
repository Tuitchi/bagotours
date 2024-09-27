<?php
include '../include/db_conn.php';

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    $query = "SELECT * FROM users WHERE id = :id";
    try {
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

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
