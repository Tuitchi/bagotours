<?php
include '../../include/db_conn.php';
$user_id = $_GET['id'];
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

$query = "SELECT 
            CASE
                WHEN vr.city_residence LIKE '%City of Bago%' THEN 'Bago Residence'
                ELSE 'Non-Bago Residence'
            END AS city_residence,
            COUNT(*) AS count
          FROM visit_records vr 
          JOIN tours t ON vr.tour_id = t.id 
          JOIN users u ON u.id = t.user_id
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
    $nonBagoCount = 0;

    foreach ($visitorData as $row) {
        if ($row['city_residence'] === 'Bago Residence') {
            $response[] = [$row['city_residence'], (int) $row['count']];
        } else {
            // Add to Non-Bago Residence count
            $nonBagoCount += (int) $row['count'];
        }
    }

    // Add Non-Bago Residence entry if there are any non-Bago cities
    if ($nonBagoCount > 0) {
        $response[] = ['Non-Bago Residence', $nonBagoCount];
    }

    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
