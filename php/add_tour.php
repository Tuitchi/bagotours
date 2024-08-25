<?php
include '../include/db_conn.php';
session_start();
try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $type = mysqli_real_escape_string($conn, $_POST['type']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $longitude = mysqli_real_escape_string($conn, $_POST['longitude']);
        $latitude = mysqli_real_escape_string($conn, $_POST['latitude']);
        $status = '1';

        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not authenticated.');
        }

        $user_id = $_SESSION['user_id'];

        if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
            $upload_dir = '../upload/Tour Images/';
            $image = $_FILES['img']['name'];
            $image_tmp = $_FILES['img']['tmp_name'];
            $image_name = time() . '_' . basename($image);
            $image_path = $upload_dir . $image_name;

            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    header("Location: ../admin/tours?status=error");
                    exit();
                }
            }

            if (!move_uploaded_file($image_tmp, $image_path)) {
                throw new Exception('Failed to upload the image.');
            }

            $sql = "INSERT INTO tours (user_id, title, address, type, description, img, status, longitude, latitude) 
                    VALUES ('$user_id', '$title', '$address', '$type', '$description', '$image_name', '$status','$longitude', '$latitude')";

            if (!mysqli_query($conn, $sql)) {
                throw new Exception('Failed to add tour: ' . mysqli_error($conn));
            }
            header("Location: ../admin/tours?status=success");
            exit();
        } else {
            throw new Exception('Image upload failed or no image selected.');
        }
    } else {
        throw new Exception('Invalid request method.');
    }
} catch (Exception $e) {
    header("Location: ../admin/tours?status=error");
    exit();
}
