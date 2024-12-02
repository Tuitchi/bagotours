<?php
// Include the database connection file
require_once '../../include/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tour_id = isset($_POST['tour_id']) ? trim($_POST['tour_id']) : null;
    $id = isset($_POST['id']) ? trim($_POST['id']) : null;
    $type = isset($_POST['type']) ? trim($_POST['type']) : null;
    $item_name = isset($_POST['item_name']) ? trim($_POST['item_name']) : '';
    $capacity = isset($_POST['capacity']) ? trim($_POST['capacity']) : null;
    $total_units = isset($_POST['total_units']) ? trim($_POST['total_units']) : null;
    $amount = isset($_POST['amount']) ? trim($_POST['amount']) : null;
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    // Validate required fields
    if (empty($item_name) || empty($amount) || empty($description)) {
            // header("Location: ../../admin/accommodation-fees-management?success=0");
        die('Invalid request. Ensure all required fields are filled.');
    }
    try {
        if ($type === 'accommodation') {
            $sql = "UPDATE accommodations
                    SET 
                        name = :name,
                        capacity = :capacity,
                        total_units = :total_units,
                        amount = :amount,
                        description = :description
                    WHERE id = :id";

            $stmt = $conn->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $item_name, PDO::PARAM_STR);
            $stmt->bindParam(':capacity', $capacity, PDO::PARAM_STR);
            $stmt->bindParam(':total_units', $total_units, PDO::PARAM_STR);
            $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        } else if ($type === 'fee') {
            $sql = "UPDATE fees
                    SET 
                        name = :name,
                        amount = :amount,
                        description = :description
                    WHERE id = :id";

            $stmt = $conn->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $item_name, PDO::PARAM_STR);
            $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        } else {
            throw new Exception('Invalid accommodation or fee type.');
        }


        // Execute the query
        if ($stmt->execute()) {
            // Redirect or return success response
            header("Location: ../../admin/accommodation-fees-management?id=$tour_id&success=1");
            exit;
        } else {
            // header("Location: ../../admin/accommodation-fees-management?success=0");
            throw new Exception('Failed to update the accommodation.');
        }
    } catch (Exception $e) {
        // header("Location: ../../admin/accommodation-fees-management?success=0");
        // Handle errors
        echo 'Error: ' . $e->getMessage();
    }
} else {
    // header("Location: ../../admin/accommodation-fees-management?success=0");
    die('Invalid request method.');
}
