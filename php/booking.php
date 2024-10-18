<?php
include '../include/db_conn.php';
require_once '../func/func.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $id = $_POST['tour_id'];
    $phone = $_POST['phone'];
    $date = $_POST['date'];
    $people = $_POST['people'];
    $status = '0';

    if (!alreadyBook($conn, $user_id,$id)){
        try {
            $stmt = $conn->prepare("INSERT INTO booking (user_id, tour_id, phone_number, date_sched, people, status) 
                                    VALUES (:user_id, :tour_id, :phone, :datetime, :people, :status)");
    
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':tour_id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':datetime', $date, PDO::PARAM_STR);
            $stmt->bindParam(':people', $people, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    
            if ($stmt->execute()) {
                $booking_id = $conn->lastInsertId();
                try {
                    $stmt = $conn->prepare("SELECT user_id, title FROM tours WHERE id = :tour_id");
                    $stmt->bindParam(':tour_id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $user_stmt = $conn->prepare("SELECT name FROM users WHERE id = $user_id");
                    $user_stmt->execute();
                    $name = $user_stmt->fetchColumn();
                    if ($row) {
                        echo createNotification($conn, $row['user_id'], $id, $name . " booked in " . $row['title'] . ".", "booking.php?id=" . $booking_id, "booking");
                        header("Location: ../user/tour?tours=$id&status=success");
                        exit();
                    } else {
                        header("Location: ../user/tour?tours=$id&status=error");
                        exit();
                    }
                } catch (PDOException $e) {
                    error_log("Error: " . $e->getMessage());
                    header("Location: ../user/tour?tours=$id&status=error");
                    exit();
                }
            } else {
                header("Location: ../user/tour?tours=$id&status=error");
                exit();
            }
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            header("Location: ../user/tour?tours=$id&status=error");
            exit();
        }
    } else {
        header("Location: ../user/tour?tours=$id&status=already_booked");
        exit();
    }
}
