<?php
$apiUrl = "https://psgc.gitlab.io/api/provinces";

// Initialize cURL session
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects

// Execute the cURL request
$response = curl_exec($ch);

// Handle cURL errors
if (curl_errno($ch)) {
    http_response_code(500); // Set HTTP status code to 500 for server error
    echo json_encode(['error' => curl_error($ch)]);
    curl_close($ch);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($httpCode !== 200) {
    http_response_code($httpCode);
    echo json_encode(['error' => "HTTP request failed with status code $httpCode"]);
    curl_close($ch);
    exit;
}

curl_close($ch);

$provinces = json_decode($response, true);

usort($provinces, function ($a, $b) {
    return strcmp($a['name'], $b['name']);
});

// Return the provinces data as JSON
header('Content-Type: application/json');
echo json_encode($provinces, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
