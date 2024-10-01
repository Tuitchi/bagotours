<?php
include '../include/db_conn.php';
session_start();


$pageRole = "admin";
require_once '../php/accValidation.php';

$user_id = $_SESSION['user_id'];
$pp = $_SESSION['profile-pic'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo htmlspecialchars($webIcon, ENT_QUOTES, 'UTF-8'); ?>">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">

    <title>BaGoTours. Inquiries</title>
</head>

<body>

    <!-- SIDEBAR -->
    <?php include 'includes/sidebar.php'; ?>
    <!-- SIDEBAR -->

    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <?php include 'includes/navbar.php'; ?>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Inquiries</h1>
                    <?php include 'includes/breadcrumb.php'; ?>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Received Inquiries</h3>
                        <div class="search-container">
                            <i class='bx bx-search' id="search-icon"></i>
                            <input type="text" id="search-input" placeholder="Search...">
                        </div>
                        <i class='bx bx-filter'></i>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT u.name, u.email, i.subject, i.message, i.status, i.date_created FROM inquiry i JOIN users u ON i.user_id = u.id ORDER BY i.date_created DESC";
                            $stmt = $conn->prepare($query);
                            $stmt->execute();
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($result) {
                                foreach ($result as $row) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['date_created'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No inquiries found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script src="../assets/js/script.js"></script>
</body>

</html>
