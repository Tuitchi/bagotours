<?php
$provinceId = isset($_GET['provinceId']) ? $_GET['provinceId'] : ''; // Get the province code from the query string

if (empty($provinceId)) {
    echo json_encode(['error' => 'Province code is required']);
    exit;
}

$apiUrl = "https://psgc.gitlab.io/api/provinces/$provinceId/cities-municipalities/";

// Initialize cURL session
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(['error' => curl_error($ch)]);
    curl_close($ch);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    http_response_code($httpCode);
    echo json_encode(['error' => "HTTP request failed with status code $httpCode"]);
    exit;
}

$cities = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid JSON response from API']);
    exit;
}

// Adjust based on actual API response structure
if (empty($cities)) {
    http_response_code(404);
    echo json_encode(['error' => 'No cities or municipalities found for the given province.']);
    exit;
}
usort($cities, function ($a, $b) {
    return strcmp($a['name'], $b['name']);
});

// Return the provinces data as JSON
header('Content-Type: application/json');
echo json_encode($cities, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
