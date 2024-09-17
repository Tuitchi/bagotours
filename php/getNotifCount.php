<?php
session_start();
require '../include/db_conn.php';
require '../func/func.php';

$userId = $_SESSION['user_id'];
$notificationCount = getNotificationCount($userId);
$notifications = getNotifications($userId);

$response = [
    'count' => $notificationCount,
    'notifications' => $notifications
];

echo json_encode($response);
