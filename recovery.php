<?php session_start();
require 'include/db_conn.php';
if (isset($_SESSION['user_id'])) {
    header('Location: home');
    exit();
}
if (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    header('Location: home');
    exit();
}
$stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = :token AND expires_at < NOW()");
$stmt->bindParam(':token', $token, PDO::PARAM_STR);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    echo "Invalid or expired token.";
    exit();
}

$user = $stmt->fetch(PDO::FETCH_ASSOC);
$userID = $user['user_id'];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($new_password) || strlen($new_password) < 6) {
        $errors['new_password'] = "Enter new password.";
    }
    if ( strlen($new_password) < 6) {
        $errors['new_password'] = "Password must be at least 6 characters.";
    }
    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Confirm your password.";
    }
    if ($new_password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :userID");
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = :token");
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();

        header('Location: home?message=Password reset successfully. You can now log in.');
        exit();
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
    <style>
        .modal-content {
            padding: 0;
            margin:auto;
            height: 500px;
        }
        .searchbar {
            display: none;
        }
    </style>
</head>

<body>
    <?php include 'nav/topnav.php' ?>

    <div class="main-container">
        <div class="modal-content">

            <div class="form-container">
                <h2>Reset your Password</h2>
                <p>Enter your new password, and make sure it’s strong.</p>
                <form action="" method="POST">
                    <input id="new_password" name="new_password" type="password" placeholder="New password" />
                    <div id="new_password-error" class="error-message"><?php echo isset($errors['new_password']) ? $errors['new_password'] : ''; ?></div>

                    <input id="confirm_password" name="confirm_password" type="password" placeholder="Confirm password" />
                    <div id="confirm_password-error" class="error-message"><?php echo isset($errors['confirm_password']) ? $errors['confirm_password'] : ''; ?></div>

                    <button type="submit">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
    <?php require "include/login-registration.php"; ?>
    <script src="index.js"></script>
</body>

</html>
