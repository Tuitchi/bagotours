<?php 

include '../include/db_conn.php';
session_start();

session_regenerate_id();
$errors = [];
$success_msg = "";
$fail_msg = "";
$user_id = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $title = trim($_POST['title']);
    $address = trim($_POST['address']);
    $description = trim($_POST['description']);
    $status = 0;

    if (empty($title)) {
        $errors['title'] = "Please enter a title.";
    }
    if (empty($address)) {
        $errors['address'] = "Please enter an address.";
    }
    if (empty($description)) {
        $errors['description'] = "Please enter a description.";
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO tours (user_id, title, address, description, status) VALUES (?,?,?,?,?)");
        $stmt->bind_param("issss", $user_id, $title, $address, $description, $status);
        
        if ($stmt->execute()) {
            $success_msg = "Tour added successfully.";
        } else {
            $fail_msg = "An error occurred while adding the tour. Please try again.";
        
            $stmt->close();
            $conn->close();
            exit();
        }
        $stmt->close();
        $conn->close();
    }
}


?>
