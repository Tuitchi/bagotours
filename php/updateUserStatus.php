<?php
require '../include/db_conn.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $sql = "UPDATE users SET role = 'owner' WHERE id = :user_id";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $sql = "SELECT id FROM tours WHERE user_id = :user_id AND status = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $_SESSION['tour_id'] = $result['id'];
                header("Location: ../owner/home?dsp=intro");
                exit();
            }
        } else {
            header("Location: ../form?error=Execution Failed");
            exit();
        }
    } else {
        header("Location: ../form?error=Statement Preparation Failed");
        exit();
    }
} else {
    header("Location: ../form?error=No User ID");
    exit();
}
