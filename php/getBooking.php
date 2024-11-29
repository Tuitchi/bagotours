<?php
include '../include/db_conn.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $query = "SELECT u.id AS user_id, u.name, u.email, u.home_address, u.profile_picture, 
    b.id AS booking_id, b.start_date, b.end_date, b.people, b.phone_number, b.status, b.date_created as date_created , t.id as tour_id,
    t.title AS tour_title
FROM booking b
JOIN users u ON u.id = b.user_id 
JOIN tours t ON t.id = b.tour_id
WHERE b.id = :id";
    try {
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $pending = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($pending['status'] == 0 || $pending['status'] == 1) {
                echo json_encode(['success' => true, 'book' => $pending]);
                exit();
            }
            echo json_encode(['success' => false, 'message' => 'Wait for book to finish.']);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'booking not found']);
            exit();
        }
    } catch (PDOException $e) {
        error_log("Error executing query: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error processing request']);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}
