<?php
include '../include/db_conn.php';
session_start();
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/admin.css">
    <title>BaGoTours || Settings</title>
    <style>
        .settings-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 500px;
            margin: 20px auto;
        }

        .settings-container form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .settings-container label {
            font-weight: bold;
        }

        .settings-container input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        .btn-save {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-save:hover {
            background-color: #0056b3;
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
                    <?php include 'includes/breadcrumb.php'; ?>
                </div>
            </div>
            <div class="table-data">
                <div class="order">
                    <h3>Admin Settings</h3>
                    <div class="settings-container">
                        <!-- Change Mapbox API Key -->
                        <form id="mapbox-form">
                            <label for="mapbox-key">Mapbox API Key:</label>
                            <input type="text" id="mapbox-key" name="mapbox_key" placeholder="Enter Mapbox API Key"
                                required>
                            <button type="submit" class="btn-save">Save API Key</button>
                        </form>

                        <!-- Change Password -->
                        <form id="password-form">
                            <label for="current-password">Current Password:</label>
                            <input type="password" id="current-password" name="current_password"
                                placeholder="Current Password" required>
                            <label for="new-password">New Password:</label>
                            <input type="password" id="new-password" name="new_password" placeholder="New Password"
                                required>
                            <button type="submit" class="btn-save">Change Password</button>
                        </form>

                        <!-- Change Email -->
                        <form id="email-form">
                            <label for="new-email">New Email:</label>
                            <input type="email" id="new-email" name="new_email" placeholder="Enter New Email" required>
                            <button type="submit" class="btn-save">Change Email</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </section>




    <script src="../assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/jquery-3.7.1.min.js"></script>

</body>

</html>