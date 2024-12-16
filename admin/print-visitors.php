<?php
include '../include/db_conn.php';
session_start();

$user_id = $_SESSION['user_id'];

// Get filters
$tour_id = isset($_GET['tour']) ? $_GET['tour'] : '';
$stmt = $conn->prepare('SELECT title FROM tours WHERE id = :tour_id');
$stmt->bindParam(':tour_id', $tour_id);
$stmt->execute();
$tour_title = $stmt->fetchColumn();

$day = isset($_GET['day']) ? $_GET['day'] : '';
$monthInput = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

$params = [':user_id' => $user_id];

// Base SQL for counts
$sql_bago = "SELECT COUNT(*) AS count FROM visit_records vr
             JOIN tours t ON vr.tour_id = t.id
             JOIN users u ON u.id = t.user_id
             WHERE u.id = :user_id AND vr.city_residence = 'Bago City'";

$sql_nonbago = "SELECT COUNT(*) AS count FROM visit_records vr
                JOIN tours t ON vr.tour_id = t.id
                JOIN users u ON u.id = t.user_id
                WHERE u.id = :user_id AND vr.city_residence != 'Bago City'";

// Apply filters
if (!empty($tour_id)) {
    $sql_bago .= " AND vr.tour_id = :tour_id";
    $sql_nonbago .= " AND vr.tour_id = :tour_id";
    $params[':tour_id'] = $tour_id;
}

if (!empty($day)) {
    $sql_bago .= " AND YEAR(vr.visit_time) = :year AND MONTH(vr.visit_time) = :month AND DAY(vr.visit_time) = :day";
    $sql_nonbago .= " AND YEAR(vr.visit_time) = :year AND MONTH(vr.visit_time) = :month AND DAY(vr.visit_time) = :day";
    $params[':year'] = $year;
    $params[':month'] = $monthInput;
    $params[':day'] = $day;
}

if (!empty($monthInput)) {
    $sql_bago .= " AND YEAR(vr.visit_time) = :year AND MONTH(vr.visit_time) = :month";
    $sql_nonbago .= " AND YEAR(vr.visit_time) = :year AND MONTH(vr.visit_time) = :month";
    $params[':year'] = $year;
    $params[':month'] = $monthInput;
}

if (!empty($year)) {
    $sql_bago .= " AND YEAR(vr.visit_time) = :year";
    $sql_nonbago .= " AND YEAR(vr.visit_time) = :year";
    $params[':year'] = $year;
}

// Execute queries
$stmt_bago = $conn->prepare($sql_bago);
$stmt_bago->execute($params);
$bago_count = $stmt_bago->fetch(PDO::FETCH_ASSOC)['count'];

$stmt_nonbago = $conn->prepare($sql_nonbago);
$stmt_nonbago->execute($params);
$nonbago_count = $stmt_nonbago->fetch(PDO::FETCH_ASSOC)['count'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Count - Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            text-align: center;
            padding: 10px;
        }

        th {
            background-color: #f2f2f2;
        }

        h1,
        h2 {
            text-align: center;
        }
    </style>
</head>

<body>
    <h1>Visitor Count Report</h1>
    <h2>
        <?php echo $tour_title ?>
    </h2>
    <h2>
        <?php
        if (!empty($day)) {
            echo "Date: " . date('F', strtotime($monthInput)) . " " . htmlspecialchars($day) . ", " . htmlspecialchars($year);
        } elseif (!empty($monthInput)) {
            echo "Month: " . date('F', strtotime($monthInput)) . " " . htmlspecialchars($year);
        } elseif (!empty($year)) {
            echo "Year: " . htmlspecialchars($year);
        } else {
            echo "All Dates";
        }
        ?>
    </h2>
    <table>
        <thead>
            <tr>
                <th colspan="2">Visitor Count</th>
            </tr>
            <tr>
                <th>Bago Residents</th>
                <th>Non-Bago Residents</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $bago_count; ?></td>
                <td><?php echo $nonbago_count; ?></td>
            </tr>
        </tbody>
    </table>
    <script>
        window.onload = () => window.print();
    </script>
</body>

</html>