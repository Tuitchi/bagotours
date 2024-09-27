<?php
require_once '../../include/db_conn.php';
header('Content-Type: application/json');

$query = "SELECT `type`, COUNT(*) as count FROM `tours` WHERE status = 1 GROUP BY `type`";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$data = [];
foreach ($result as $row) {
    $data[] = [$row['type'], (int)$row['count']];
}

echo json_encode($data);
