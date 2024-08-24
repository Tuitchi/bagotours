<?php 
include '../include/db_conn.php';
session_start();

session_regenerate_id();

// if (!isset($_SESSION['id'])) {
//     header("Location: ../login.php?action=Invalid");
//     exit();
// }
// $user_id = $_SESSION['user_id'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Homepage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .navbar {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .navbar .logo {
            font-size: 24px;
            font-weight: bold;
        }
        .navbar .nav-links {
            display: flex;
            align-items: center;
        }
        .navbar .nav-links a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            font-size: 16px;
        }
        .navbar .nav-links a:hover {
            text-decoration: underline;
        }
        .navbar .profile {
            position: relative;
            display: inline-block;
        }
        .navbar .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
        }
        .navbar .profile .dropdown-menu {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            background-color: white;
            color: black;
            min-width: 150px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }
        .navbar .profile .dropdown-menu a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
        }
        .navbar .profile .dropdown-menu a:hover {
            background-color: #ddd;
        }
        .content {
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <i class="fas fa-plane-departure"></i> BaGoTours.
        </div>
        <div class="nav-links">
            <a href="">Home</a>
            <a href="#">Destination</a>
            <a href="#">Booking</a>
            <a href="#">Search</a>
        </div>
        <div class="profile">
            <img id="profilePic" src="../upload/Profile Pictures/default.jpg" alt="Profile Picture">
            <div id="dropdownMenu" class="dropdown-menu">
                <a href="#">Profile</a>
                <a href="#">Settings</a>
                <a href="../php/logout.php">Logout</a>
            </div>
        </div>
    </div>
    <div class="content">
        <h1>Welcome to BaGoTours</h1>
        <p>Explore the best destinations and book your trips easily!</p>
    </div>

    <script>
        document.getElementById('profilePic').addEventListener('click', function() {
            var dropdownMenu = document.getElementById('dropdownMenu');
            if (dropdownMenu.style.display === 'block') {
                dropdownMenu.style.display = 'none';
            } else {
                dropdownMenu.style.display = 'block';
            }
        });

        // Close the dropdown if the user clicks outside of it
        window.onclick = function(event) {
            if (!event.target.matches('#profilePic')) {
                var dropdowns = document.getElementsByClassName('dropdown-menu');
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.style.display === 'block') {
                        openDropdown.style.display = 'none';
                    }
                }
            }
        }
    </script>
</body>
</html>