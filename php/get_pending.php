<?php
include '../include/db_conn.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "SELECT users.*, tours.* FROM tours RIGHT JOIN users ON users.id = tours.user_id WHERE tours.id = ? AND tours.status = 0;";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $pending = $result->fetch_assoc();
            echo json_encode(['success' => true, 'pending' => $pending]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing statement']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();
?>
