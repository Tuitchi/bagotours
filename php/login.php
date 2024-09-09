<?php
include '../include/db_conn.php';

$errors = [];

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

    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        $errors['username'] = "User not found";
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit();
    }

    $row = $result->fetch_assoc();
    $stmt->close();

    $hash_password = $row['password'];
    if (!password_verify($password, $hash_password)) {
        $errors['password'] = "Invalid username or password";
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit();
    }

    session_start();
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['profile-pic'] = $row['profile_picture'];
    $role = strtolower($row['role']);

    if (isset($_POST['remember'])) {
        setcookie("username", $username, time() + (86400 * 30), "/");
    }

    $sql = "SELECT id FROM tours WHERE user_id = ? AND status = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $tours = $result->fetch_assoc();
        $_SESSION['tour_id'] = $tours['id'];
    }
    $stmt->close();

    $redirect = '';
    if ($role === "admin") {
        $redirect = 'admin/home';
    } elseif ($role === "user") {
        $redirect = 'user/home';
    } elseif ($role === "owner") {
        $redirect = 'owner/home';
    } else {
        echo json_encode(['success' => false, 'errors' => ['role' => 'Invalid role']]);
        exit();
    }

    echo json_encode(['success' => true, 'redirect' => $redirect]);
    exit();
}

$conn->close();
