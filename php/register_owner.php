<?php

include '../include/db_conn.php';
session_start();

$user_id = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $address = trim($_POST['address']);
    $description = trim($_POST['description']);
    $type = trim($_POST['type']);
    $status = 0;

    if (isset($_FILES['proofImage']) && $_FILES['proofImage']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['proofImage']['tmp_name'];
        $fileName = $_FILES['proofImage']['name'];
        $fileSize = $_FILES['proofImage']['size'];
        $fileType = $_FILES['proofImage']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadFileDir = '../upload/Profile_Pictues/';
            $destPath = $uploadFileDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                echo 'File is successfully uploaded.';
            } else {
                echo 'There was some error moving the file to upload directory.';
            }
        } else {
            echo 'Upload failed. Allowed file types: ' . implode(', ', $allowedExtensions);
        }
    } else {
        echo 'No file uploaded or there was an upload error.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO tours (user_id, title, address, description,type, status) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("isssss", $user_id, $title, $address, $description, $type, $status);

        if ($stmt->execute()) {
            echo "success";
        } else {

            $stmt->close();
            $conn->close();
            exit();
        }
        $stmt->close();
        $conn->close();
    }
}
