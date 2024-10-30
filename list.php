<?php
session_start();
require 'include/db_conn.php'; // Make sure this file establishes a PDO connection

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

// Prepare to fetch tours from the database
$query = "SELECT * FROM tours"; // Modify this to fit your database structure
$stmt = $conn->prepare($query);

// Handle filter submission
$filter = isset($_POST['filter']) ? $_POST['filter'] : '';

// Apply filter to the query if set
if ($filter) {
    $query .= " WHERE category = :filter"; // Adjust as needed based on your database structure
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':filter', $filter);
}

$stmt->execute();
$tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <style>
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

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
        }

        .filter-form button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        .filter-form button:hover {
            background-color: #0056b3;
        }

        #tour-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
.grid-view p {
    display:none;
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
            /* Ensure padding and border are included in width */
        }

        .tour-item:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .tour-item img {
            width: 100%;
            object-fit: cover;
            border-radius: 8px;
            height: 200px;
            /* Ensure image maintains aspect ratio */
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

        .list-view .tour-item {
            width: 100%;
            /* Full width for list view */
            margin-bottom: 20px;
            /* Add spacing for list view */
        }

        .grid-view .tour-item {
            width: calc(33.333% - 20px);
            /* Default grid view */
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .tour-item {
                width: calc(50% - 20px);
                /* 2 items per row on medium screens */
            }
        }

        @media (max-width: 480px) {
            .tour-item {
                width: 100%;
                /* Full width on small screens */
            }
            .grid-view {
                font-size: 10px;
            }

            .filter-form {
                flex-direction: column;
                /* Stack filters on small screens */
                align-items: center;
            }

            .filter-form select,
            .filter-form button {
                width: 100%;
                /* Full width for inputs on small screens */
                margin: 5px 0;
                /* Add margin for spacing */
            }
        }
    </style>

    <script>
        function toggleView(view) {
            const tourContainer = document.getElementById('tour-container');
            if (view === 'grid') {
                tourContainer.classList.remove('list-view');
                tourContainer.classList.add('grid-view');
            } else {
                tourContainer.classList.remove('grid-view');
                tourContainer.classList.add('list-view');
            }
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
                    <option value="adventure" <?php echo $filter === 'adventure' ? 'selected' : ''; ?>>Adventure</option>
                    <option value="cultural" <?php echo $filter === 'cultural' ? 'selected' : ''; ?>>Cultural</option>
                    <option value="relaxation" <?php echo $filter === 'relaxation' ? 'selected' : ''; ?>>Relaxation
                    </option>
                </select>
                <button type="button" onclick="toggleView('list')">List View</button>
                <button type="button" onclick="toggleView('grid')">Grid View</button>
            </form>

            <div id="tour-container" class="grid-view">
                <?php foreach ($tours as $tour): ?>
                    <div class="tour-item">
                        <a href='tour?id=<?php echo base64_encode($tour['id'] . $salt); ?>' class='card'>
                            <img src='upload/Tour Images/<?php echo htmlspecialchars($tour['img']); ?>'
                                alt='<?php echo htmlspecialchars($tour['title']); ?>'>
                            <h3><?php echo htmlspecialchars($tour['title']); ?></h3>
                            <p>Location: <?php echo htmlspecialchars($tour['address']); ?></p>
                            <p>Description: <?php echo htmlspecialchars($tour['description']); ?></p>

                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php require "include/login-registration.php"; ?>
    <script src="index.js"></script>
</body>

</html>