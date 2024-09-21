<?php
include '../include/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $tour_id = $_POST['tour_id'];
    $phone = $_POST['phone'];
    $datetime = $_POST['date'] . ' ' . $_POST['time'];
    $people = $_POST['people'];
    $status = '0';

    try {
        // Insert booking
        $stmt = $conn->prepare("INSERT INTO booking (user_id, tours_id, phone_number, date_sched, people, status) 
                                VALUES (:user_id, :tour_id, :phone, :datetime, :people, :status)");

        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':datetime', $datetime, PDO::PARAM_STR);
        $stmt->bindParam(':people', $people, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $booking_id = $conn->lastInsertId();
            try {
                $stmt = $conn->prepare("SELECT user_id FROM tours WHERE id = :tour_id");
                $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    require_once '../func/func.php';
                    $message = "Someone booked a tour.";
                    $url = "view_booking.php?user_id=$user_id&booking_id=$booking_id";
                    $type = 'booking';

                    createNotification($conn, $row['user_id'], $message, $url, $type);

                    header("Location: ../user/tour?tours=$tour_id&status=success$booking_id");
                    exit();
                } else {
                    header("Location: ../user/tour?tours=$tour_id&status=error");
                    exit();
                }
            } catch (PDOException $e) {
                error_log("Error: " . $e->getMessage());
                header("Location: ../user/tour?tours=$tour_id&status=error");
                exit();
            }
        } else {
            header("Location: ../user/tour?tours=$tour_id&status=error");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        header("Location: ../user/tour?tours=$tour_id&status=error");
        exit();
    }
}
