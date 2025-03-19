<?php
require "../include/db_conn.php";
$maxLoad = 40000;

$apiType = "map_load";

try {
    $currentMonthYear = date('Y-m');

    // Fetch the current total loads for the API type and month
    $stmt = $conn->prepare("SELECT total_loads FROM map_usage WHERE month_year = :month_year AND api_type = :api_type");
    $stmt->execute([
        ':month_year' => $currentMonthYear,
        ':api_type' => $apiType
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalLoads = $result ? (int)$result['total_loads'] : 0;

    // Check if the load limit has been reached
    if ($totalLoads >= $maxLoad) {
        echo json_encode(['allowMap' => false]);
        exit;
    }

    // Update or insert total loads
    if ($result) {
        $stmt = $conn->prepare("UPDATE map_usage SET total_loads = total_loads + 1 WHERE month_year = :month_year AND api_type = :api_type");
        $stmt->execute([
            ':month_year' => $currentMonthYear,
            ':api_type' => $apiType
        ]);
    } else {
        $stmt = $conn->prepare("INSERT INTO map_usage (month_year, api_type, total_loads) VALUES (:month_year, :api_type, 1)");
        $stmt->execute([
            ':month_year' => $currentMonthYear,
            ':api_type' => $apiType
        ]);
    }

    echo json_encode(['allowMap' => true]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
