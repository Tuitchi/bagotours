<?php
include '../include/db_conn.php';
include '../func/user_func.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tour_id = $_POST['tour_id'];
    $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');
    $type = htmlspecialchars($_POST['type'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    $status = (int)$_POST['status'];

    if (empty($title) || empty($address) || empty($type) || empty($description)) {
        header("Location: edit_tour.php?error=EmptyFields");
        exit();
    }

    $sql = "UPDATE tours SET title = :title, address = :address, type = :type, description = :description, status = :status WHERE id = :tour_id";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: view_tour.php?success=TourUpdated");
        } else {
            header("Location: edit_tour.php?error=UpdateFailed");
        }
    } else {
        header("Location: edit_tour.php?error=SQLPrepareFailed");
    }
} else {
    header("Location: tour.php");
    exit();
}
