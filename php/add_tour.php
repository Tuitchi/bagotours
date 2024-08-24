<?php
include '../include/db_conn.php';
session_start();

$response = [];

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize inputs
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $type = mysqli_real_escape_string($conn, $_POST['type']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $status = '1';

        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated.');
        }

        $user_id = $_SESSION['user_id'];

        // Handle the image upload
        if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
            $upload_dir = '../upload/Tour Images/';
            $image = $_FILES['img']['name'];
            $image_tmp = $_FILES['img']['tmp_name'];
            $image_name = time() . '_' . basename($image); // Ensure unique filenames
            $image_path = $upload_dir . $image_name;

            // Ensure the upload directory exists
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    throw new Exception('Failed to create upload directory.');
                }
            }

            // Move the uploaded file to the server
            if (!move_uploaded_file($image_tmp, $image_path)) {
                throw new Exception('Failed to upload the image.');
            }

            // Insert tour into the database
            $sql = "INSERT INTO tours (user_id, title, address, type, description, img, status) 
                    VALUES ('$user_id', '$title', '$address', '$type', '$description', '$image_name', '$status')";

            if (!mysqli_query($conn, $sql)) {
                throw new Exception('Failed to add tour: ' . mysqli_error($conn));
            }

            $response['success'] = true;
            $response['message'] = 'Tour added successfully!';
        } else {
            throw new Exception('Image upload failed or no image selected.');
        }
    } else {
        throw new Exception('Invalid request method.');
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
