

<?php
include '../include/db_conn.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $query = "SELECT users.*, tours.* FROM tours 
              JOIN users ON users.id = tours.user_id 
              WHERE tours.id = :id AND tours.status = 0";

    try {
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $pending = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'pending' => $pending]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Tour not found']);
        }
    } catch (PDOException $e) {
        error_log("Error executing query: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error processing request']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
