<?php
include '../include/db_conn.php'; // Database connection
session_start();

$user_id = $_SESSION['user_id']; // Get user ID
$tour_title = $_GET['tour']; // Get tour title
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');

// Generate all dates in the selected month
function getDatesInMonth($year, $month)
{
    $dates = [];
    $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    for ($day = 1; $day <= $num_days; $day++) {
        $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
        $dates[$date] = [
            'visit_date' => date('d/m/Y', strtotime($date)),
            'weekday' => date('l', strtotime($date)),
            'bago_female' => 0, 'bago_male' => 0, 'bago_total' => 0,
            'negros_female' => 0, 'negros_male' => 0, 'negros_total' => 0,
            'other_female' => 0, 'other_male' => 0, 'other_total' => 0,
            'foreign_female' => 0, 'foreign_male' => 0, 'foreign_total' => 0,
            'total_male' => 0, 'total_female' => 0, 'grand_total' => 0
        ];
    }
    return $dates;
}

// Generate all dates
$dates = getDatesInMonth($year, $month);

// SQL query to get visitor counts
$sql = "SELECT 
    DATE_FORMAT(visit_time, '%Y-%m-%d') AS visit_date,
    SUM(CASE WHEN gender = 0 AND city_residence LIKE '%City of Bago%' THEN 1 ELSE 0 END) AS bago_female,
    SUM(CASE WHEN gender = 1 AND city_residence LIKE '%City of Bago%' THEN 1 ELSE 0 END) AS bago_male,
    SUM(CASE WHEN city_residence LIKE '%City of Bago%' THEN 1 ELSE 0 END) AS bago_total,
    
    SUM(CASE WHEN gender = 0 AND city_residence NOT LIKE '%City of Bago%' AND city_residence LIKE '%Negros Occidental%' THEN 1 ELSE 0 END) AS negros_female,
    SUM(CASE WHEN gender = 1 AND city_residence NOT LIKE '%City of Bago%' AND city_residence LIKE '%Negros Occidental%' THEN 1 ELSE 0 END) AS negros_male,
    SUM(CASE WHEN city_residence NOT LIKE '%City of Bago%' AND city_residence LIKE '%Negros Occidental%' THEN 1 ELSE 0 END) AS negros_total,

    SUM(CASE WHEN gender = 0 AND city_residence NOT LIKE '%Negros Occidental%' AND city_residence LIKE '%Philippines%' THEN 1 ELSE 0 END) AS other_female,
    SUM(CASE WHEN gender = 1 AND city_residence NOT LIKE '%Negros Occidental%' AND city_residence LIKE '%Philippines%' THEN 1 ELSE 0 END) AS other_male,
    SUM(CASE WHEN city_residence NOT LIKE '%Negros Occidental%' AND city_residence LIKE '%Philippines%' THEN 1 ELSE 0 END) AS other_total,

    SUM(CASE WHEN gender = 0 AND city_residence NOT LIKE '%Philippines%' THEN 1 ELSE 0 END) AS foreign_female,
    SUM(CASE WHEN gender = 1 AND city_residence NOT LIKE '%Philippines%' THEN 1 ELSE 0 END) AS foreign_male,
    SUM(CASE WHEN city_residence NOT LIKE '%Philippines%' THEN 1 ELSE 0 END) AS foreign_total,

    SUM(CASE WHEN gender = 1 THEN 1 ELSE 0 END) AS total_male,
    SUM(CASE WHEN gender = 0 THEN 1 ELSE 0 END) AS total_female,
    COUNT(*) AS grand_total
FROM visit_records
WHERE YEAR(visit_time) = :year AND MONTH(visit_time) = :month
GROUP BY DATE(visit_time)
ORDER BY DATE(visit_time) ASC";

$stmt = $conn->prepare($sql);
$stmt->execute([':year' => $year, ':month' => $month]);
$visitor_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Merge SQL data with generated dates
foreach ($visitor_data as $row) {
    $date_key = $row['visit_date'];
    if (isset($dates[$date_key])) {
        $dates[$date_key] = array_merge($dates[$date_key], $row);
    }
}

// Initialize total counters
$totals = [
    'bago_female' => 0, 'bago_male' => 0, 'bago_total' => 0,
    'negros_female' => 0, 'negros_male' => 0, 'negros_total' => 0,
    'other_female' => 0, 'other_male' => 0, 'other_total' => 0,
    'foreign_female' => 0, 'foreign_male' => 0, 'foreign_total' => 0,
    'total_male' => 0, 'total_female' => 0, 'grand_total' => 0
];

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
            background-color: yellow;
        }

        h1,
        h2 {
            text-align: center;
        }
    </style>
</head>

<body>
    <h2>
        <?php echo $tour_title ?> : 
        <?php
        if (!empty($month)) {
            echo  $month . " - " . htmlspecialchars($year);
        } elseif (!empty($year)) {
            echo htmlspecialchars($year);
        } else {
            echo "All Dates";
        }
        ?>
    </h2>
    <table>
        <thead>
            <tr>
                <th colspan="2">Date</th>
                <th colspan="12">***Place of Residence</th>
                <th colspan="3" rowspan="3">*Grand Total Number of Visitors</th>
            </tr>
            <tr>
                <th rowspan="3">Day</th>
                <th rowspan="3">Week Day (Mon - Sun)</th>
                <th colspan="9">Philippines</th>
                <th colspan="3" rowspan="2">Foreign Country Residence</th>
            </tr>
            <tr>
                <th colspan="3">This City/ Municipality</th>
                <th colspan="3">This Province</th>
                <th colspan="3">Other Province</th>
            </tr>
            <tr>
                <th>Male</th>
                <th>Female</th>
                <th>Total</th>
                <th>Male</th>
                <th>Female</th>
                <th>Total</th>
                <th>Male</th>
                <th>Female</th>
                <th>Total</th>
                <th>Male</th>
                <th>Female</th>
                <th>Total</th>
                <th>Male</th>
                <th>Female</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dates as $data): ?>
                <tr>
                    <td><?php echo $data['visit_date']; ?></td>
                    <td><?php echo $data['weekday']; ?></td>
                    <?php
                    foreach ($totals as $key => &$total) {
                        $total += (int)$data[$key]; // Accumulate totals
                        echo "<td>{$data[$key]}</td>";
                    }
                    ?>
                </tr>
            <?php endforeach; ?>

            <tr style="font-weight: bold; background-color: #88E788;">
                <td colspan="2">TOTAL</td>
                <td><?php echo $totals['bago_female']; ?></td>
                <td><?php echo $totals['bago_male']; ?></td>
                <td><?php echo $totals['bago_total']; ?></td>
                <td><?php echo $totals['negros_female']; ?></td>
                <td><?php echo $totals['negros_male']; ?></td>
                <td><?php echo $totals['negros_total']; ?></td>
                <td><?php echo $totals['other_female']; ?></td>
                <td><?php echo $totals['other_male']; ?></td>
                <td><?php echo $totals['other_total']; ?></td>
                <td><?php echo $totals['foreign_female']; ?></td>
                <td><?php echo $totals['foreign_male']; ?></td>
                <td><?php echo $totals['foreign_total']; ?></td>
                <td><?php echo $totals['total_male']; ?></td>
                <td><?php echo $totals['total_female']; ?></td>
                <td><?php echo $totals['grand_total']; ?></td>
            </tr>

        </tbody>
    </table>
    <script>
        window.onload = () => window.print();
    </script>
</body>

</html>