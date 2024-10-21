<?php session_start();
require 'include/db_conn.php';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}
if (isset($_GET['q'])){
    try {
        $search_query = "%". $_GET['q']. "%";
        $stmt = $conn->prepare("SELECT * FROM tours WHERE title LIKE :search_query");
        $stmt->bindParam(':search_query', $search_query);
        $stmt->execute();
        $tour = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Connection failed: ". $e->getMessage();
        die();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BagoTours</title>
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
    <?php include 'nav/topnav.php' ?>
    <div class="main-container">
        <?php include 'nav/sidenav.php' ?>
        <div class="main">

        </div>
    </div>

    <?php require "include/login-registration.php"; ?>
    <script src="index.js"></script>
    </body>

</html>