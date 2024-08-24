<?php
include '../include/db_conn.php';
session_start();

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

    if (empty($errors)) {
        $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hash_password = $row['password'];
            $role = strtolower($row['role']);
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['profile-pic'] = $row['profile_picture'];
            

            if (password_verify($password, $hash_password)) {
                if (isset($_POST['remember'])) {
                    setcookie("username", $username, time() + (86400 * 30), "/"); // 86400 = 1 day
                }
                
                $stmt->close();
                $conn->close();

                if ($role === "admin") {
                    header('Location: ../admin/home');
                } elseif ($role === "user") {
                    header('Location: ../user/home');
                } elseif ($role === "owner") {
                    header('Location: ../owners/home');
                } else {
                    header('Location: ../login?error=InvalidRole');
                }
                exit();
            } else {
                $errors['password'] = "Invalid username or password";
            }
        } else {
            $errors['username'] = "User not found";
        }
        $stmt->close();
    }

    $query_string = http_build_query(['username' => $username, 'errors' => $errors]);
    header("Location: ../login.php?$query_string");
    exit();
}

$conn->close();
?>
