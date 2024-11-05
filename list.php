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

.tour-item img {
    width: 100%;
    object-fit: cover;
    border-radius: 8px;
    height: 200px;
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
        width: calc(50% - 20px); /* 2 items per row for medium screens */
        margin-bottom: 15px;
    }

    .filter-form select,
    .filter-form button {
        font-size: 0.9em;
    }
}

@media (max-width: 480px) {
    .tour-item {
        width: calc(50% - 20px); /* Ensures only 2 items per row in grid view */
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
                <img src='upload/Tour Images/<?php echo htmlspecialchars($tour['img']); ?>' alt='<?php echo htmlspecialchars($tour['title']); ?>'>
                <h3><?php echo htmlspecialchars($tour['title']); ?></h3>
                <p>
                    <?php
                    $description = htmlspecialchars($tour['description']);
                    $sentences = explode('.', $description); // Split the description by sentences
                    $preview = implode('.', array_slice($sentences, 0, 2)) . '.'; // Take only the first two sentences
                    echo $preview;
                    ?>
                  
                </p>
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