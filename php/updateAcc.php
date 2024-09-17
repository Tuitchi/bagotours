<?php
include_once '../include/db_conn.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = isset($_POST['fullName']) ? trim($_POST['fullName']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

    $profile_picture = $_FILES['profilePicture']['name'];
    $profile_picture_temp = $_FILES['profilePicture']['tmp_name'];

    $target_dir = "../upload/Profile Pictures/";
    $target_file = $target_dir . basename($profile_picture);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if ($profile_picture != "") {
        $check = getimagesize($profile_picture_temp);
        if ($check === false) {
            $uploadOk = 0;
        }

        if ($_FILES["profilePicture"]["size"] > 5000000) {
            $uploadOk = 0;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (!move_uploaded_file($profile_picture_temp, $target_file)) {
                $uploadOk = 0;
            }
        }
    }

    if ($uploadOk == 0 || empty($profile_picture)) {
        $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $profile_picture = $row['profile_picture'];
    }
    unset($_SESSION['profile-pic']);
    $_SESSION['profile-pic'] = $profile_picture;

    $stmt = $conn->prepare("UPDATE users SET name = ?, username = ?, email = ?, phone_number = ?, profile_picture = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $fullName, $username, $email, $phone, $profile_picture, $user_id);

    if ($stmt->execute()) {
        $_SESSION['status'] = 'success';
        header("Location: ../user/acc.php?update=success");
        exit();
    } else {
        $_SESSION['status'] = 'error';
        header("Location: ../user/acc.php?update=error");
        exit();
    }
}
