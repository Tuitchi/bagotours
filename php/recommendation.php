<?php 
require '../include/db_conn.php';

$tours = $conn->prepare("SELECT rr.*, t.title FROM review_rating rr JOIN tours t ON t.id = rr.tour_id");
$tours->execute();

$matrix = [];

while ($tour = $tours->fetch(PDO::FETCH_ASSOC)) {
    $users = $conn->prepare("SELECT username FROM users WHERE id = :user_id");
    $users->execute(['user_id' => $tour['user_id']]);
    $username = $users->fetch(PDO::FETCH_ASSOC);

    $matrix[$username['username']][$tour['title']] = $tour['rating'];
}

echo "<pre>";
print_r($matrix);
echo "</pre>";