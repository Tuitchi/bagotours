<?php
include '../include/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $query = "UPDATE tours SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $status, $id);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error";
    }

    $stmt->close();
    $conn->close();
}
?>
