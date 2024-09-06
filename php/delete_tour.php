<?php
include '../include/db_conn.php';
session_start();

$response = [];

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!isset($_POST['tour_id'])) {
            throw new Exception('Tour ID is required.');
        }

        $tour_id = intval($_POST['tour_id']);

        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated.');
        }
        $sql_images = "SELECT img FROM tours_image WHERE tours_id = '$tour_id'";
        $result_images = mysqli_query($conn, $sql_images);

        if ($result_images) {
            while ($row = mysqli_fetch_assoc($result_images)) {
                $image_path = '../upload/Tour Images/' . $row['img'];
                if (file_exists($image_path)) {
                    if (!unlink($image_path)) {
                        throw new Exception('Failed to delete image file.');
                    }
                }
            }
        }
        $sql_delete_images = "DELETE FROM tours_image WHERE tours_id = '$tour_id'";
        if (!mysqli_query($conn, $sql_delete_images)) {
            throw new Exception('Failed to delete images from database: ' . mysqli_error($conn));
        }
        $sql_delete_tour = "DELETE FROM tours WHERE id = '$tour_id'";
        if (!mysqli_query($conn, $sql_delete_tour)) {
            throw new Exception('Failed to delete tour: ' . mysqli_error($conn));
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