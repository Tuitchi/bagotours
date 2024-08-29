<?php
include '../include/db_conn.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm-password']);
    $role = "user";
    $pp = "default.png";

    if (empty($username)) {
        $errors['username'] = "Enter your username";
    }
    if (empty($email)) {
        $errors['email'] = "Enter your email";
    }
    if (empty($password)) {
        $errors['password'] = "Enter your password";
    }
    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Confirm your password";
    }
    if ($password != $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }
    
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (email, username, password, role, profile_picture) VALUES (?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssss", $email, $username, $hashed_password, $role, $pp);

        if ($stmt->execute()) {
            session_start();
            $_SESSION['profile-pic'] = $pp;
            echo json_encode(['success' => true, 'redirect' => 'user/home']);
        } else {
            $errors['register'] = "Something went wrong, please try again";
            echo json_encode(['success' => false, 'errors' => $errors]);
        }

        $stmt->close();
    } else {
        $errors['register'] = "Failed to prepare the SQL statement";
        echo json_encode(['success' => false, 'errors' => $errors]);
    }
    
    $conn->close();
    exit();
}
?>
