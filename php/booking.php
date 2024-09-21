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
        // Prepare the SQL query using PDO
        $stmt = $conn->prepare("INSERT INTO booking (user_id, tours_id, phone_number, date_sched, people, status) 
                                VALUES (:user_id, :tour_id, :phone, :datetime, :people, :status)");

        // Bind the parameters to the statement
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':datetime', $datetime, PDO::PARAM_STR);
        $stmt->bindParam(':people', $people, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);

        // Execute the statement
        if ($stmt->execute()) {
            header("Location: ../user/tour?tours=$tour_id&status=success");
            exit();
        } else {
            header("Location: ../user/tour?tours=$tour_id&status=error");
            exit();
        }
    } catch (PDOException $e) {
        // Handle any errors with a redirect and log the error for debugging
        error_log("Error: " . $e->getMessage());
        header("Location: ../user/tour?tours=$tour_id&status=error");
        exit();
    }
}
