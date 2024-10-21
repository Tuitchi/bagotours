<?php
include '../include/db_conn.php';
require_once '../func/func.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $tour_id = $_POST['tour_id'];
    $phone_number = $_POST['phone_number'];
    $date = $_POST['date_sched'];

    $status = '0';

    // Check for required fields
    if (!empty($user_id) && !empty($tour_id) && !empty($phone_number) && !empty($date)) {
        if (!alreadyBook($conn, $user_id, $tour_id)) {
            try {
                $stmt = $conn->prepare("INSERT INTO booking (user_id, tour_id, phone_number, date_sched, status) 
                                        VALUES (:user_id, :tour_id, :phone, :datetime, :status)");

                // Bind parameters with explicit types
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT); // Corrected variable name
                $stmt->bindParam(':phone', $phone_number, PDO::PARAM_STR); // Corrected variable name
                $stmt->bindParam(':datetime', $date, PDO::PARAM_STR);
                $stmt->bindParam(':status', $status, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $booking_id = $conn->lastInsertId();
                    // Fetch tour details for notifications
                    $stmt = $conn->prepare("SELECT user_id, title FROM tours WHERE id = :tour_id");
                    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT); // Use the correct variable
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $user_stmt = $conn->prepare("SELECT name FROM users WHERE id = :user_id");
                    $user_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $user_stmt->execute();
                    $name = $user_stmt->fetchColumn();

                    if ($row) {
                        echo createNotification($conn, $row['user_id'], $tour_id, "$name booked in " . $row['title'] . ".", "booking.php?id=" . $booking_id, "booking");
                        header("Location: ../tour?id=" . base64_encode($tour_id . $salt) . "&status=success");
                        exit();
                    } else {
                        header("Location: ../tour?id=" . base64_encode($tour_id . $salt) . "&status=success");
                        exit();
                    }
                } else {
                    header("Location: ../tour?id=" . base64_encode($tour_id . $salt) . "&status=failed");
                    exit();
                }
            } catch (PDOException $e) {
                error_log("Error: " . $e->getMessage());
                header("Location: ../tour?id=" . base64_encode($tour_id . $salt) . "&status=error");
                exit();
            }
        } else {
            header("Location: ../tour?id=" . base64_encode($tour_id . $salt) . "&status=alreadyBook");
            exit();
        }
    } else {
        // Handle missing input fields
        header("Location: ../tour?id=" . base64_encode($tour_id . $salt) . "&status=inputError");
        exit();
    }
}
?>
