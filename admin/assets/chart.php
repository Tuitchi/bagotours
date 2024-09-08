<?php
require_once '../../include/db_conn.php';
header('Content-Type: application/json');

$query = "SELECT `type`, COUNT(*) as count FROM `tours` WHERE status=1 GROUP BY `type`";
if ($stmt = $conn->prepare($query)) {
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = [$row['type'], (int)$row['count']];
    }

    echo json_encode($data);
} else {
    echo json_encode([]);
}

$conn->close();
?>
