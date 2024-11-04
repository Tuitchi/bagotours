<?php
require '../include/db_conn.php'; // Include your database connection
require_once '../func/func.php'; // Include any additional functions
require '../vendor/autoload.php'; // Include Composer's autoloader for PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();

$errors = [];

// Handle the POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    if (empty($email)) {
        $errors['email'] = "Enter your email address";
    }

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit();
    }

    // Check if the email exists in the users table
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $token = generateResetToken($conn, $user['id']);
        if (sendPasswordResetEmail($email, $token)) {
            echo json_encode(['success' => true, 'message' => 'Password reset link sent to your email.']);
        } else {
            echo json_encode(['success' => false, 'errors' => ['email' => 'Failed to send email.']]);
        }
    } else {
        echo json_encode(['success' => false, 'errors' => ['email' => 'Email not found.']]);
    }
}

function generateResetToken($conn, $userId)
{
    $token = bin2hex(random_bytes(16)); // Generate a random token
    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token valid for 1 hour

    // Insert the token into the password_resets table
    $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->bindParam(':expires_at', $expiresAt, PDO::PARAM_STR);
    $stmt->execute();

    return $token;
}

function sendPasswordResetEmail($email, $token)
{
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // Disable debug output
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host = 'smtp.gmail.com';                            // Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   // Enable SMTP authentication
        $mail->Username = "kapitanbato26@gmail.com";              // SMTP username
        $mail->Password = "euys laln pmon hcfe";                  // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;        // Enable implicit TLS encryption
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('kapitanbato26@gmail.com', 'BaGoTours');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $resetLink = "http://bagodigitaltourism.bccbsis.com/recovery?token=$token";
        $mail->Body = "Click here to reset your password: <a href='$resetLink'>$resetLink</a>";

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}

$conn = null;
