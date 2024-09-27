<?php
include '../include/db_conn.php';
session_start();

$response = [];

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!isset($_POST['tour_id'])) {
            throw new Exception('Tour ID is required.');
        }

        $id = intval($_POST['tour_id']);

        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated.');
        }

        // Fetch all images associated with the tour using PDO
        $sql_images = "SELECT img FROM tours_image WHERE tours_id = :tour_id";
        $stmt_images = $conn->prepare($sql_images);
        $stmt_images->bindParam(':tour_id', $id, PDO::PARAM_INT);
        $stmt_images->execute();
        $images = $stmt_images->fetchAll(PDO::FETCH_ASSOC);

        // Delete image files from the server
        foreach ($images as $row) {
            $image_path = '../upload/Tour Images/' . $row['img'];
            if (file_exists($image_path)) {
                if (!unlink($image_path)) {
                    throw new Exception('Failed to delete image file: ' . $image_path);
                }
            }
        }

        // Delete the images from the `tours_image` table
        $sql_delete_images = "DELETE FROM tours_image WHERE tours_id = :tour_id";
        $stmt_delete_images = $conn->prepare($sql_delete_images);
        $stmt_delete_images->bindParam(':tour_id', $id, PDO::PARAM_INT);

        if (!$stmt_delete_images->execute()) {
            throw new Exception('Failed to delete images from database.');
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
