<?php
include '../../include/db_conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['editUserId'])) {
        echo json_encode(['success' => false, 'message' => 'User ID is required.']);
        exit();
    }
    $id = $_POST['editUserId'];
    $name = trim($_POST['editName']);
    $email = trim($_POST['editEmail']);
    $phone = trim($_POST['editPhoneNumber']);
    $username = trim($_POST['editUsername']);
    $role = trim($_POST['editRole']);

    if (empty($name) || empty($email) || empty($phone) || empty($username) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
        exit();
    }

    $query = "UPDATE users SET name = :name, email = :email, phone_number = :phone, username = :username, role = :role WHERE id = :id";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'User edited successfully.']);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'There was a problem editing the user.']);
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
