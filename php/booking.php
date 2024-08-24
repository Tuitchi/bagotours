<?php 
include '../include/db_conn.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $tour_id = $_POST['tour_id'];
    $phone = $_POST['phone'];
    $datetime = $_POST['date'] . ' ' . $_POST['time'];
    $people = $_POST['people'];
    $status = '0';

    $query = "INSERT INTO booking (user_id, tours_id, phone_number, date_sched, people, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iissis", $user_id, $tour_id, $phone, $datetime, $people, $status);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: ../user/tour?tours=$tour_id&status=success");
            exit();
        } else {
            header("Location: ../user/tour?tours=$tour_id&status=error");
            exit();
        }

        mysqli_stmt_close($stmt);
    } else {
        header("Location: ../user/tour?tours=$tour_id&status=error");
        exit();
    }

    mysqli_close($conn);
    exit();
}
?>
