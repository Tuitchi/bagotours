<?php
session_start();
require '../include/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $email = $_POST['email'];
    $country = htmlspecialchars(trim($_POST['country']));
    $province = htmlspecialchars(trim($_POST['province'] ?? ''));
    $city = htmlspecialchars(trim($_POST['city'] ?? ''));

    // Constructing home address from provided fields
    $home_address = trim(($city ? $city . ', ' : '') . ($province ? $province . ', ' : '') . $country);

    $password = $_POST['pwd'] ?? null;
    $confirm_password = $_POST['con-pwd'] ?? null;

    if (is_null($email)) {
        $errors['email'] = "Email is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }
    if (empty($country)) {
        $errors['country'] = "Country is required.";
    }

    if (empty($password) || strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    if (empty($errors)) {
        try {
            // Check if the email already exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $emailExists = $stmt->fetchColumn();

            if ($emailExists > 0) {
                $errors['email'] = "Email is already taken.";
            } else {
                $device_id = md5($email);
                setcookie('device_id', $device_id, time() + (10 * 365 * 24 * 60 * 60), "/");
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert new user into the database
                $stmt = $conn->prepare("INSERT INTO users (email, home_address, password, role, device_id) VALUES (:email, :home_address, :password, 'user',:device_id)");
                $stmt->execute([
                    'email' => $email,
                    'home_address' => $home_address,
                    'password' => $hashed_password,
                    'device_id' => $device_id,
                ]);
                $_SESSION['user_id'] = $conn->lastInsertId();
                $_SESSION['role'] = 'user';
                $_SESSION['profile-pic'] = 'upload/Profile Pictures/default.png';
                echo json_encode(['success' => true, 'message' => 'Registration successful!', 'redirect' => '']);
                exit;
            }
        } catch (PDOException $e) {
            $errors['database'] = "Database error: " . $e->getMessage();
        }
    }
    // Respond with errors if any
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

