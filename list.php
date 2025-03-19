<?php
session_start();
require 'include/db_conn.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

// Prepare to fetch tours from the database
$query = "SELECT *, (SELECT AVG(rr.rating) FROM review_rating rr WHERE rr.tour_id = tours.id) AS rating FROM tours WHERE status IN ('Active', 'Temporarily Closed')";

$stmt = $conn->prepare($query);

// Handle type submission
$type = isset($_POST['filter']) ? $_POST['filter'] : '';

// Apply type to the query if set
if ($type) {
    $query .= " AND type = :type";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':type', $type);
}

$stmt->execute();
$tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo $webIcon ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BagoTours</title>
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <style>

        .img .rating {
            position: absolute;
            top: 2px;
            right: 5px;
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: bold;
            z-index: 10;
        }
        .img .rating .star {
            color: yellow;
            font-size: 1em;
            margin-left: 3px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 10px;
            font-size: .7em;
            font-weight: 600;
            color: #333;
            text-decoration: none;
            color: #333;
            transition: color 0.3s;
            transition: text-decoration 0.3s;

        }

        /* Filter Form */
        .filter-form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-form select,
        .filter-form button {
            margin: 5px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            font-size: 1em;
        }

        .filter-form button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .filter-form button:hover {
            background-color: #0056b3;
        }

        /* Tour Container and Item */
        #tour-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .tour-item {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 10px;
            margin: 10px;
            width: calc(33.333% - 20px);
            background-color: #fff;
            transition: transform 0.2s;
            box-sizing: border-box;
        }

        .tour-item:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .tour-item .img {
            position: relative;

            width: 100%;
            height: 200px;
        }

        .tour-item img {
            width: 100%;
            object-fit: cover;
            border-radius: 8px;
            height: 200px;
        }

        .img .bookable {
            position: absolute;
            bottom: 10px;
            right: 10px;
        }

        .tour-item h3 {
            margin: 10px 0;
            font-size: 1em;
        }

        .tour-item p {
            margin: 5px 0;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: justify;
        }



        /* List View */
        .list-view .tour-item {
            width: 100%;
            margin-bottom: 20px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .tour-item {
                width: calc(50% - 20px);
                /* 2 items per row for medium screens */
                margin-bottom: 15px;
            }

            .filter-form select,
            .filter-form button {
                font-size: 0.9em;
            }
        }

        @media (max-width: 480px) {
            .tour-item {
                width: calc(50% - 20px);
                /* Ensures only 2 items per row in grid view */
                margin-bottom: 15px;
            }

            .tour-item img {
                height: 150px;
            }

            .grid-view p {
                display: none;
            }

            .filter-form {
                flex-direction: column;
                align-items: center;
            }

            .filter-form select,
            .filter-form button {
                width: 100%;
                margin: 5px 0;
                padding: 8px;
            }

            .tour-item h3 {
                font-size: 0.9em;
            }
        }
    </style>

    <script>
        function toggleView(view) {
            const tourContainer = document.getElementById('tour-container');
            tourContainer.className = view === 'grid' ? 'grid-view' : 'list-view';
        }
    </script>
</head>

<body>
    <?php include 'nav/topnav.php'; ?>
    <div class="main-container">
        <?php include 'nav/sidenav.php'; ?>
        <div class="main">
            <h2>Tours</h2>

            <form method="POST" class="filter-form">
                <select name="filter" onchange="this.form.submit()">
                    <option value="">All Tours</option>
                    <?php
                    $stmt = $conn->prepare('SELECT DISTINCT type FROM tours');
                    $stmt->execute();
                    $types = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    foreach ($types as $typeOption) {
                        echo '<option value="' . htmlspecialchars($typeOption) . '"' .
                            ($type === $typeOption ? ' selected' : '') .
                            '>' . htmlspecialchars($typeOption) . '</option>';
                    }
                    ?>
                </select>
                <button type="button" onclick="toggleView('list')">List View</button>
                <button type="button" onclick="toggleView('grid')">Grid View</button>
            </form>

            <div id="tour-container" class="grid-view">
                <?php foreach ($tours as $tour):
                    $tour_images = explode(',', $tour['img']);
                    $main_image = $tour_images[0]; ?>
                    <div class="tour-item">
                        <a href='tour?id=<?php echo base64_encode($tour['id'] . $salt); ?>' class='card'>

                            <div class="img">
                                <span class="rating">
                                    <?php
                                    // Format the rating to 1 decimal place
                                    echo htmlspecialchars(number_format($tour['rating'] ?? 0, 1));
                                    ?> <span class="star">★</span>
                                </span>
                                <img src='upload/Tour Images/<?php echo $main_image; ?>'
                                    alt='<?php echo htmlspecialchars($tour['title']); ?>'>
                                <?php if ($tour['status'] == 'Temporarily Closed') {
                                    echo "<img src='assets/icons/closed.png' class='bookable closed'>";
                                } else {
                                    if ($tour['bookable']) {
                                        echo "<img src='assets/icons/booking.png' class='bookable'>";
                                    } else {
                                        echo "<img src='assets/icons/free.png' class='bookable'>";
                                    }
                                } ?>
                            </div>
                            <h3><?php echo htmlspecialchars($tour['title']); ?></h3>
                            <p>
                                <?php
                                echo htmlspecialchars($tour['address']);
                                ?>
                            </p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="pagination" id="pagination"></div>
        </div>
    </div>

    <?php require "include/login-registration.php"; ?>
    <script src="index.js"></script>
</body>

</html>