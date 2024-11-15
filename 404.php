<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo $webIcon ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>
    <link rel="stylesheet" href="user.css">
    <style>
        main {
            margin: 10px 50px;
            text-align: center;
        }
        main img {
            width: 300px;
            margin-bottom: -50px;
        }
        a {
            display: inline-block;
            margin: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <main>
        <h1>404 - Page Not Found</h1>
        <img src="assets/booking-empty.png" alt="">
        <p>Sorry, the page you are looking for does not exist.</p>
        <a class="success" href="home.php">Return to Home</a>
    </main>
</body>
</html>
