<?php
require '../include/db_conn.php';

session_start(); // Make sure session is started

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $sql = "UPDATE users SET role = 'owner' WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            $sql = "SELECT id FROM tours WHERE user_id = ? AND status = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $tours = $result->fetch_assoc();
                $_SESSION['tour_id'] = $tours['id'];
                header("Location: ../owner/home?dsp=intro");
                exit();
            }
        } else {
            header("Location: ../user/form?error=Execution Failed");
            exit();
        }
    } else {
        header("Location: ../user/form?error=Statement Preparation Failed");
        exit();
    }
} else {
    header("Location: ../user/form?error=No User ID");
    exit();
}
