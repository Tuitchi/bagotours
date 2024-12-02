<?php
include '../../include/db_conn.php'; // Include your PDO connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tour_id = $_POST['tour_id'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $name = "Entrance Fee";

    // Validate inputs
    if (empty($tour_id) || empty($description) || empty($amount)) {
        http_response_code(400);
        echo 'Invalid input';
        exit();
    }

    try {
        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("INSERT INTO fees (tour_id, name, description, amount) VALUES (:tour_id, :name, :description, :amount)");
        $stmt->bindParam(':tour_id', $tour_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':amount', $amount);

        if ($stmt->execute()) {
            echo 'Success';
        } else {
            http_response_code(500);
            echo 'Failed to add Entrance Fee.';
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo 'Database error: ' . $e->getMessage();
    }
}
