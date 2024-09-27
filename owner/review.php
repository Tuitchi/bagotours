<?php
include '../include/db_conn.php';
include '../func/func.php';

session_start();
$status = isset($_GET["status"]) ? $_GET["status"] : '';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?action=Invalid");
    exit();
}
$user_id = $_SESSION['user_id'];
$id = $_SESSION['tour_id'];

$RRs = getAllRR($conn, $id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Mapbox -->
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />
    <title>BaGoTours. Tours</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .btn-submit {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        .btn-submit:hover {
            background-color: #45a049;
        }

        #mapboxModal .modal-content {
            width: 50%;
            height: 80%;
            max-width: 80%;
            max-height: 100%;
        }

        .data {
            display: flex;
            flex-wrap: nowrap;
            gap: 15px;
            align-items: flex-start;
            justify-content: flex-start;
            border-bottom: 1px solid #ccc;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .data .img {
            width: 200px;
            height: 200px;
            overflow: hidden;
            border-radius: 15%;
        }

        .data .img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .data .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .data .content h4 {
            margin: 0 0 10px;
            font-size: 1.5em;
        }

        .data .content p {
            margin: 0 0 5px;
        }

        .data .content .btn-edit,
        .data .content .btn-delete {
            margin-top: 10px;
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        .data .content .btn-delete {
            background-color: #dc3545;
        }

        .content {
            position: relative;
            flex-grow: 1;
            padding-right: 60px;
        }

        .action-buttons {
            font-size: 12px;
            position: absolute;
            bottom: 10px;
            right: 10px;
            display: flex;
            gap: 10px;
        }

        .btn-edit,
        .btn-delete {
            width: 50px;
            height: 30px;
            text-align: center;
            line-height: 30px;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-delete {
            background-color: #dc3545;
        }
    </style>
</head>

<body>
    <?php include 'includes/sidebar.php'; ?>
    <section id="content">
        <?php include 'includes/navbar.php'; ?>
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Review</h1>
                    <?php include 'includes/breadcrumb.php'; ?>
                </div>
            </div>
            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Reviews And Ratings</h3>
                        <div class="search-container">
                            <i class='bx bx-search' id="search-icon"></i>
                            <input type="text" id="search-input" placeholder="Search...">
                        </div>
                        <i class='bx bx-filter'></i>
                    </div>
                    <?php
                    if (!empty($RRs)) {
                        foreach ($RRs as $rr) {
                            echo '<div class="data">';
                            echo '<div class="content">';
                            echo '<h4>' . htmlspecialchars($rr['name'], ENT_QUOTES, 'UTF-8') . '</h4>'; // Use $rr['name']
                            echo '<p><strong>Review</strong></p>';
                            echo '<p style="font-size:13px;">' . htmlspecialchars($rr['review'], ENT_QUOTES, 'UTF-8') . '</p>';
                            echo '<p>Rating: ' . htmlspecialchars($rr['average_rating'], ENT_QUOTES, 'UTF-8') . '</p>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No review found.</p>';
                    }
                    ?>

                </div>
            </div>
        </main>
    </section>

    <script src="../assets/js/script.js"></script>
</body>

</html>