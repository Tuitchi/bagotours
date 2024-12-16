<?php
include '../include/db_conn.php';
require_once '../func/func.php';
session_start();

$errors = [];

if (isset($_COOKIE['device_id'])){
    unset($_COOKIE['device_id']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username)) {
        $errors['username'] = "Enter your username or email";
    }
    if (empty($password)) {
        $errors['password'] = "Enter your password";
    }

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit();
    }

    try {
        $sql = "SELECT * FROM users WHERE username = :username OR email = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            $errors['password'] = "Invalid username or password";
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit();
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $hash_password = $row['password'];
        if (!password_verify($password, $hash_password)) {
            $errors['password'] = "Invalid username or password";
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit();
        }
        $device_id = $row['device_id'];
        if (empty($row['device_id'])) {
            $device_id = md5($row['email'] . $row['username']);
            $stmt = $conn->prepare("UPDATE users SET device_id = ? WHERE id=?");
            $stmt->execute([$device_id, $row['id']]);
        }
        setcookie('device_id', $device_id, time() + (10 * 365 * 24 * 60 * 60), "/");

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['profile-pic'] = $row['profile_picture'];
        $role = strtolower($row['role']);

        if ($role == 'admin' || $role == 'owner') {
            $sql = "SELECT id FROM tours WHERE user_id = :user_id AND status = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $tours = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }


        $redirect = '';
        if ($role === "admin") {
            $redirect = 'admin/home';
        } elseif ($role === "user") {
            $redirect = '';
        } elseif ($role === "owner") {
            $redirect = 'owner/home';
        } else {
            echo json_encode(['success' => false, 'errors' => ['role' => 'Invalid role']]);
            exit();
        }
        echo json_encode(['success' => true, 'redirect' => $redirect]);
        $_SESSION['loginSuccess'] = true;
        exit();
    } catch (PDOException $e) {
        $errors['database'] = "Database error: " . $e->getMessage();
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit();
    }
}

$conn = null;
