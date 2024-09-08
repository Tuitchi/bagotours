<?php
include '../include/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    $query = "UPDATE booking SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $status, $booking_id);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement.']);
    }

    mysqli_close($conn);
}
?>
