<?php
include '../include/db_conn.php';
session_start();

$user_id = $_SESSION['user_id'];

// Fetch the user's current email
$query = "SELECT email FROM users WHERE id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);
$current_email = $user['email'];  // Store the email
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
                        <!-- Change Email -->
                        <form id="email-form">
                            <label for="new-email">Current Email:</label>
                            <input type="email" id="new-email" name="new_email" value="<?php echo $current_email; ?>"
                                placeholder="Enter New Email" required>
                            <button type="submit" class="btn-save">Change Email</button>
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
                    </div>
                </div>
            </div>
        </main>
    </section>

    <script src="../assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function () {
            // Handle password change form submission
            $('#password-form').submit(function (event) {
                event.preventDefault();

                const currentPassword = $('#current-password').val();
                const newPassword = $('#new-password').val();

                // Disable button and change text to "Changing..."
                const btn = $(this).find('button');
                btn.prop('disabled', true).text('Changing...');

                $.ajax({
                    url: '../php/updatePassword.php',
                    type: 'POST',
                    data: {
                        current_password: currentPassword,
                        new_password: newPassword
                    },
                    dataType: 'json',
                    success: function (response) {
                        btn.prop('disabled', false).text('Change Password');
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message
                            });
                        }
                    },
                    error: function () {
                        btn.prop('disabled', false).text('Change Password');
                        Swal.fire({
                            icon: 'error',
                            title: 'There was an error processing your request.'
                        });
                    }
                });
            });

            // Handle email change form submission
            $('#email-form').submit(function (event) {
                event.preventDefault();

                const newEmail = $('#new-email').val();

                // Disable button and change text to "Changing..."
                const btn = $(this).find('button');
                btn.prop('disabled', true).text('Changing...');

                $.ajax({
                    url: '../php/updateEmail.php',
                    type: 'POST',
                    data: {
                        new_email: newEmail
                    },
                    dataType: 'json',
                    success: function (response) {
                        btn.prop('disabled', false).text('Change Email');
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message
                            });
                        }
                    },
                    error: function () {
                        btn.prop('disabled', false).text('Change Email');
                        Swal.fire({
                            icon: 'error',
                            title: 'There was an error processing your request.'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>