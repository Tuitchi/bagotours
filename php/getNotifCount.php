<?php
session_start();
require '../include/db_conn.php';
require '../func/func.php';

$userId = $_SESSION['user_id'];
$notificationCount = getNotificationCount($conn,$userId);
$notifications = getNotifications($conn,$userId);

$response = [
    'count' => $notificationCount,
    'notifications' => $notifications
];

echo json_encode($response);
