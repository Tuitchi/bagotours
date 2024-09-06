<?php
include '../../include/db_conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['userName']);
    $email = trim($_POST['userEmail']);
    $password = trim($_POST['userPassword']);
    $role = trim($_POST['userRole']);

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
        exit();
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (name, email, password, role, date_created) VALUES (?, ?, ?, ?, NOW())";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'User added successfully.']);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'There was a problem adding the user.']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing statement.']);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}
?>
