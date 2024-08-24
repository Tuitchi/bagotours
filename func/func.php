<?php
function getTouristSpots($conn) {
    $query = "SELECT id, title, latitude, longitude, type, img, address FROM tours";
    $result = $conn->query($query);

    $touristSpots = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $touristSpots[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude'],
                'type' => $row['type'],
                'image' => $row['img'],
                'address' => $row['address']
            ];
        }
    }

    return json_encode($touristSpots);
}

?>