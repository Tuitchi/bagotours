<?php
include '../include/db_conn.php'; // Database connection
require_once '../func/func.php'; // Include your custom functions
require '../vendor/autoload.php'; // PHPMailer autoload
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Validate email input
    if (empty($email)) {
        $errors['email'] = "Enter your email";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit();
    }

    // Check if email already exists in the database
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $errors['email'] = "Email already exists";
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit();
    }

    // Generate a unique verification code or token
    $baseUrl = getBaseUrl();
    $verification_code = md5($email . time());
    $verification_link = $baseUrl . "/verify.php?code=" . urlencode($verification_code);
    // Save email and verification code to the database
    try {
        $stmt = $conn->prepare("INSERT INTO users (email, verification_code, is_verified) VALUES (:email, :verification_code, 0)");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':verification_code', $verification_code, PDO::PARAM_STR);
        $stmt->execute();
    } catch (PDOException $e) {
        $errors['database'] = "Database error: " . $e->getMessage();
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit();
    }

    // Send verification email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = "kapitanbato26@gmail.com";
        $mail->Password = "euys laln pmon hcfe"; // Replace with your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('kapitanbato26@gmail.com', 'BaGoTours');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Email Verification";
        $mail->Body = "Click the link below to verify your email: <br><br>
                       <a href='$verification_link'>Verify Email</a>";

        if ($mail->send()) {
            echo json_encode(['success' => true, 'message' => 'Verification email sent. Check your email to verify.']);
        } else {
            $errors['email'] = "Email can't send.";
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit();
        }
    } catch (Exception $e) {
        $errors['email'] = "Mailer Error: " . $mail->ErrorInfo;
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit();
    }
}
function getBaseUrl()
{
    // Detect protocol (HTTP or HTTPS)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';

    // Get the host (e.g., www.example.com)
    $host = $_SERVER['HTTP_HOST'];

    // Get the directory path of the current script
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);

    // Build the base URL
    return rtrim($protocol . $host . $scriptDir, '/');
}
$conn = null;