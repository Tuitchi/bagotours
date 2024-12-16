<?php
$localFile = "cities.json"; // Path to the local cities file
$provinceId = isset($_GET['provinceId']) ? $_GET['provinceId'] : ''; // Get province ID from query

// Validate provinceId
if (empty($provinceId)) {
    echo json_encode(['error' => 'Province code is required']);
    exit;
}

// Check if the local file exists
if (!file_exists($localFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'Local cities data not found.']);
    exit;
}

// Load the cities data from the local file
$data = file_get_contents($localFile);
$citiesData = json_decode($data, true);

// Check if JSON parsing is successful
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid JSON in local cities data.']);
    exit;
}

// Filter cities by the given provinceId
$citiesInProvince = array_filter($citiesData, function ($city) use ($provinceId) {
    return $city['provinceCode'] === $provinceId;
});

// If no cities are found for the given provinceId
if (empty($citiesInProvince)) {
    http_response_code(404);
    echo json_encode(['error' => 'No cities or municipalities found for the given province.']);
    exit;
}

// Sort cities alphabetically by their name
usort($citiesInProvince, function ($a, $b) {
    return strcmp($a['name'], $b['name']);
});

// Return the filtered and sorted cities as JSON
header('Content-Type: application/json');
echo json_encode(array_values($citiesInProvince), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
