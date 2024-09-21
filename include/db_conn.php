<?php
$DATABASE_HOSTNAME = "localhost";
$DATABASE_USERNAME = "root";
$DATABASE_PASSWORD = "";
$DATABASE_NAME = "tourism";

try {
    $conn = new PDO("mysql:host=$DATABASE_HOSTNAME;dbname=$DATABASE_NAME", $DATABASE_USERNAME, $DATABASE_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT file FROM system_info WHERE type = 'Tab Icon' LIMIT 1;";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $GLOBALS['webIcon'] = $row['file'];
    } else {
        throw new Exception("No file found for 'Tab Icon'.");
    }
} catch (PDOException $e) {
    // Log PDO-specific error messages
    error_log("PDO error: " . $e->getMessage());
} catch (Exception $e) {
    // Log general exceptions
    error_log($e->getMessage());
}
