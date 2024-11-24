<?php
include '../../include/db_conn.php';
$user_id = $_SESSION['user_id'];
$id = isset($_GET['tour']) ? $_GET['tour'] : null;
$timeFilter = isset($_GET['time']) ? $_GET['time'] : null;

$whereClause = "";
if ($id) {
    $whereClause .= " AND tour_id = :tourId";
}

switch ($timeFilter) {
    case 'daily':
        $whereClause .= " AND DATE(visit_time) = CURDATE()";
        break;
    case 'monthly':
        $whereClause .= " AND MONTH(visit_time) = MONTH(CURDATE())";
        break;
    case 'yearly':
        $whereClause .= " AND YEAR(visit_time) = YEAR(CURDATE())";
        break;
        
}

$query = "SELECT city_residence, COUNT(*) as count
          FROM visit_records vr JOIN tours t ON vr.tour_id = t.id JOIN users u ON u.id = t.user_id
          WHERE u.id = :user_id" . $whereClause . "
          GROUP BY city_residence";

try {
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    
    if ($id) {
        $stmt->bindParam(':tourId', $id, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $visitorData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = [];
    foreach ($visitorData as $row) {
        $response[] = [$row['city_residence'], (int) $row['count']];
    }

    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
