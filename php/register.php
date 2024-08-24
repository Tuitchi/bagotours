<?php
include '../include/db_conn.php';

session_start();

$errors = [];
$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm-password']);
    $role = "user";
    $pp = "default.png";
    $_SESSION['profile-pic'] = $pp;

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (email, username, password, role, profile_picture) VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $email, $username, $hashed_password, $role, $pp);
        if ($stmt->execute()) {
            $success_message = "Registration successful!";
            header("Location: ../user/home.php");
            exit();
        } else {
            $error_message = "There was an error registering your account. Please try again.";
        }
        $stmt->close();
    }

    $_SESSION['errors'] = $errors;
    $_SESSION['success_message'] = $success_message;
    $_SESSION['error_message'] = $error_message;

    header("Location: ../login.php");
    exit();
}
?>