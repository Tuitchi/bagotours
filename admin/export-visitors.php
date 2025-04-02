<?php
include '../include/db_conn.php'; // Database connection
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Get user ID
$tour_title = isset($_GET['tour']) ? $_GET['tour'] : 'All Tours';
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');

// Get month name
$monthName = date('F', mktime(0, 0, 0, $month, 1, $year));

// Generate all dates in the selected month
function getDatesInMonth($year, $month) {
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

// Calculate totals
foreach ($dates as $data) {
    foreach ($totals as $key => &$total) {
        $total += (int)$data[$key]; // Accumulate totals
    }
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $tour_title . '_Visitors_' . $monthName . '_' . $year . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Create a file handle for output
$output = fopen('php://output', 'w');

// Add UTF-8 BOM to ensure Excel opens the file correctly with UTF-8 encoding
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add blank row for spacing
fputcsv($output, array_fill(0, 17, ''));

// Title row with clear spacing and centered
fputcsv($output, [
    '                ' . $tour_title . ' - ' . $monthName . ' ' . $year . '                ',
    '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
]);

// Add blank row for spacing
fputcsv($output, array_fill(0, 17, ''));

// Headers with clear distinction - Main group header (centered)
fputcsv($output, [
    '', '', '            PLACE OF RESIDENCE            ', '', '', '', '', '', '', '', '', '', '', '', '    GRAND TOTAL    ', '', ''
]);

// Add blank row for spacing
fputcsv($output, array_fill(0, 17, ''));

// Philippine vs Foreign section header - with proper spacing (centered)
fputcsv($output, [
    '   DATE   ', '   WEEKDAY   ', 
    '        PHILIPPINES        ', '', '', '', '', '', '', '', '', '',
    '      FOREIGN      ', '', '',
    '', ''
]);

// Location categories with clear spacing (centered)
fputcsv($output, [
    '', '', 
    '  BAGO CITY  ', '', '',
    ' NEGROS OCCIDENTAL ', '', '',
    ' OTHER PHILIPPINES ', '', '',
    ' FOREIGN COUNTRIES ', '', '',
    '   TOTALS   ', '', ''
]);

// Gender columns (centered)
fputcsv($output, [
    '', '', 
    ' Male ', ' Female ', ' Total ',
    ' Male ', ' Female ', ' Total ',
    ' Male ', ' Female ', ' Total ',
    ' Male ', ' Female ', ' Total ',
    ' Male ', ' Female ', ' Total '
]);

// Add blank row for spacing
fputcsv($output, array_fill(0, 17, ''));

// Write data rows
foreach ($dates as $data) {
    fputcsv($output, [
        $data['visit_date'],
        $data['weekday'],
        $data['bago_male'],
        $data['bago_female'],
        $data['bago_total'],
        $data['negros_male'],
        $data['negros_female'],
        $data['negros_total'],
        $data['other_male'],
        $data['other_female'],
        $data['other_total'],
        $data['foreign_male'],
        $data['foreign_female'],
        $data['foreign_total'],
        $data['total_male'],
        $data['total_female'],
        $data['grand_total']
    ]);
}

// Add blank row for spacing before totals
fputcsv($output, array_fill(0, 17, ''));

// Write totals row with emphasis
fputcsv($output, [
    'TOTAL', 'ALL DAYS',
    $totals['bago_male'],
    $totals['bago_female'],
    $totals['bago_total'],
    $totals['negros_male'],
    $totals['negros_female'],
    $totals['negros_total'],
    $totals['other_male'],
    $totals['other_female'],
    $totals['other_total'],
    $totals['foreign_male'],
    $totals['foreign_female'],
    $totals['foreign_total'],
    $totals['total_male'],
    $totals['total_female'],
    $totals['grand_total']
]);

// Add blank row at the end
fputcsv($output, array_fill(0, 17, ''));

fclose($output);
exit; 