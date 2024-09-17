<?php
include '../include/db_conn.php';

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $firstname = htmlspecialchars(trim($_POST['firstname']));
    $lastname = htmlspecialchars(trim($_POST['lastname']));
    $name = $firstname . " " . $lastname;
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $uname = htmlspecialchars(trim($_POST['username']));
    $pwd = trim($_POST['pwd']);
    $confirm_password = trim($_POST['con-pwd']);
    $role = "user";
    $pp = "default.png";

    include '../func/user_func.php';
    $emailAlreadyUsed = emailAlreadyUsed($conn, $email);
    $usernameAlreadyUsed = usernameAlreadyUsed($conn, $uname);

    if (empty($firstname) || empty($lastname)) {
        $errors['name'] = "Enter your first and last name.";
    }
    if (empty($uname)) {
        $errors['uname'] = "Enter your username.";
    } elseif ($usernameAlreadyUsed) {
        $errors['uname'] = "Username already in use.";
    }
    if (empty($email)) {
        $errors['email'] = "Enter your email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    } elseif ($emailAlreadyUsed) {
        $errors['email'] = "Email already in use.";
    }
    if (strlen(trim($pwd)) === 0) {
        $errors['pwd'] = "Enter your password.";
    } elseif (strlen($pwd) < 8) {
        $errors['pwd'] = "Password must be at least 8 characters long.";
    }
    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Confirm your password.";
    } elseif ($pwd != $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors, 'uname' => $uname]);
        exit();
    }

    $hashed_password = password_hash($pwd, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, username, password, role, profile_picture) VALUES (?,?,?,?,?,?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssss", $name, $email, $uname, $pwd, $role, $pp);

        if ($stmt->execute()) {
            session_start();
            $_SESSION['profile-pic'] = $pp;
            $_SESSION['user_id'] = $conn->insert_id;
            echo json_encode(['success' => true, 'redirect' => 'user/home']);
        } else {
            error_log("MySQL error: " . $stmt->error);
            $errors['register'] = "Something went wrong, please try again.";
            echo json_encode(['success' => false, 'errors' => $errors]);
        }
        $stmt->close();
    } else {
        $errors['register'] = "Failed to prepare the SQL statement.";
        echo json_encode(['success' => false, 'errors' => $errors]);
    }

    $conn->close();
    exit();
}
