<?php
include '../include/db_conn.php';
include '../func/user_func.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['tour_id'];
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
        $stmt->bindParam(':tour_id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            if ($_SESSION['role'] == 'owner') {
                header("Location: view_tour.php?success=TourUpdated");
            } else {
                header("Location: ../admin/view_tour.php?id=$id&success=TourUpdated");
            }
            exit();
        } else {
            if ($_SESSION['role'] == 'owner') {
                header("Location: view_tour.php?error=updateFailed");
            } else {
                header("Location: ../admin/view_tour.php?id=$id&error=updateFailed");
            }
            exit();
        }
    } else {
        if ($_SESSION['role'] == 'owner') {
            header("Location: view_tour.php?error=SQLError");
        } else {
            header("Location: ../admin/view_tour.php?id=$id&error=SQLError");
        }
        exit();
    }
} else {
    if ($_SESSION['role'] == 'owner') {
        header("Location: tour.php?error=NotFound");
    } else {
        header("Location: ../admin/tour.php?error=NotFound");
    }
    exit();
}
