<?php
$DATABASE_HOSTNAME = "localhost";
$DATABASE_USERNAME = "root";
$DATABASE_PASSWORD = "";
$DATABASE_NAME = "tourism";

$conn = new mysqli($DATABASE_HOSTNAME, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    $sql = "SELECT file FROM system_info WHERE type = 'Tab Icon' LIMIT 1;";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $GLOBALS['webIcon'] = $row['file'];
}