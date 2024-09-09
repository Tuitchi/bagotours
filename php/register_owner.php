<?php

include '../include/db_conn.php';
session_start();

$user_id = $_SESSION['user_id'];
try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = trim($_POST['title']);
        $address = trim($_POST['purok'] . ", " . $_POST['barangay'] . ", " . $_POST['address']);
        $description = trim($_POST['description']);
        $type = trim($_POST['type']);
        $latitude = trim($_POST['latitude']);
        $longitude = trim($_POST['longitude']);
        $proof = trim($_POST['proof']);
        $status = 0;

        if (empty($title) || empty($address) || empty($description) || empty($type) || empty($latitude) || empty($longitude)) {
            header('Location: ../user/form?status=empty_fields');
            exit;
        }
        if (isset($_FILES['proofImage']) && $_FILES['proofImage']['error'] == 0) {
            $upload_dir = '../upload/Permits/';
            $image = $_FILES['proofImage']['name'];
            $image_tmp = $_FILES['[proofImage]']['tmp_name'];
            $image_name = time() . '_' . basename($image);
            $image_path = $upload_dir . $image_name;

            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    header("Location: ../user/form?status=error");
                    exit();
                }
            }

            if (!move_uploaded_file($image_tmp, $image_path)) {
                throw new Exception('Failed to upload the image.');
            }

            $sql = "INSERT INTO tours (user_id, title, address, type, description, status, longitude, latitude, proof, proofImage) 
                VALUES ('$user_id', '$title', '$address', '$type', '$description', '$status','$longitude', '$latitude', '$proof', '$image_name')";

            if (!mysqli_query($conn, $sql)) {
                throw new Exception('Failed to add tour: ' . mysqli_error($conn));
            }
            header("Location: ../user/form?status=success");
            exit();
        } else {
            throw new Exception('Image upload failed or no image selected.');
        }
    } else {
        throw new Exception('Invalid request method.');
    }
} catch (Exception $e) {
    header("Location: ../user/form?status=error");
    exit();
}
