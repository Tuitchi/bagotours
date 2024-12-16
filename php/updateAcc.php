<?php
include_once '../include/db_conn.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
    $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $gender = $_POST['gender'] ?? null;
    $country = $_POST['country'] ?? null;
    $province = $_POST['province'] ?? null;
    $city = $_POST['city'] ?? null;
    $home_address = trim(
        ($city ? $city . ', ' : '') .
        ($province ? $province . ', ' : '') .
        $country
    );

    $profile_picture = $_FILES['profilePicture']['name'];
    $profile_picture_temp = $_FILES['profilePicture']['tmp_name'];

    $target_dir = "../upload/Profile Pictures/";
    $target_file = $target_dir . basename($profile_picture);
    $db_pp = "upload/Profile Pictures/" . basename($profile_picture);
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
        $stmt->execute([$user_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $db_pp = $row['profile_picture'];
    }

    unset($_SESSION['profile-pic']);
    $_SESSION['profile-pic'] = $db_pp;

    $stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ?, phone_number = ?, profile_picture = ?,home_address = ? WHERE id = ?");
    if ($stmt->execute([$firstname, $lastname, $phone, $db_pp, $home_address, $user_id])) {
        $_SESSION['status'] = 'success';
        header("Location: ../manage-acc");
        exit();
    } else {
        $_SESSION['status'] = 'error';
        header("Location: ../manage-acc");
        exit();
    }
}
