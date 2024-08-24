<?php
include '../include/db_conn.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    if ($id > 0) {
        $sql = 'DELETE FROM users WHERE id = ?';

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
            echo 'User deleted successfully';
        } else {
            echo 'Error preparing statement';
        }
    } else {
        echo 'Invalid user ID';
    }
    
    $conn->close();
} else {
    echo 'No user ID specified';
}
?>
