<?php
include '../include/db_conn.php';
session_start();

$response = [];

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!isset($_GET['id'])) {
            throw new Exception('Tour ID is required.');
        }

        $id = intval($_GET['id']);

        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated.');
        }
        // Delete the tour from the `tours` table
        $sql_delete_tour = "DELETE FROM tours WHERE id = :tour_id";
        $stmt_delete_tour = $conn->prepare($sql_delete_tour);
        $stmt_delete_tour->bindParam(':tour_id', $id, PDO::PARAM_INT);

        if (!$stmt_delete_tour->execute()) {
            throw new Exception('Failed to delete tour.');
        }

        $response['success'] = true;
        $response['message'] = 'Tour deleted successfully!';
    } else {
        throw new Exception('Invalid request method.');
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
