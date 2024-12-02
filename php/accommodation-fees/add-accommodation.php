<?php
include '../../include/db_conn.php'; // Include your PDO connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tour_id = $_POST['tour_id'];
    $name = $_POST['name'];
    $capacity = $_POST['capacity'];
    $amount = $_POST['amount'];
    $total_units = $_POST['total_units'];
    $description = $_POST['description'];

    // Validate inputs
    if (empty($tour_id) || empty($name) || empty($capacity) || empty($total_units) || empty($description) || empty($amount)){
        http_response_code(400);
        echo 'Invalid input';
        exit();
    }

    try {
        $stmt = $conn->prepare("INSERT INTO accommodations (tour_id, name, capacity, amount, total_units, description)
                               VALUES (:tour_id, :name, :capacity, :amount, :total_units, :description)");
        $stmt->bindParam(':tour_id', $tour_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':capacity', $capacity);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':total_units', $total_units);
        $stmt->bindParam(':description', $description);

        
        if ($stmt->execute()) {
            echo 'Success';
        } else {
            http_response_code(500);
            echo 'Failed to add Accommodation.';
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo 'Database error: ' . $e->getMessage();
    }
}
