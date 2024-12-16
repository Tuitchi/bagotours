<?php
$localFile = "provinces.json";

if (!file_exists($localFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'Local provinces data not found.']);
    exit;
}

$response = file_get_contents($localFile);

if ($response === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to read local provinces data.']);
    exit;
}

$provinces = json_decode($response, true);

usort($provinces, function ($a, $b) {
    return strcmp($a['name'], $b['name']);
});

header('Content-Type: application/json');
echo json_encode($provinces, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
