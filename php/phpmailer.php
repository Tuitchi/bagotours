<?php
require '../include/db_conn.php';

if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];

    try {
        $stmt = $conn->prepare("SELECT t.title as title, u.name as name, u.email as email, b.* FROM booking b JOIN users u ON u.id = b.user_id JOIN tours t ON t.id = b.tour_id WHERE b.id = :id");
        $stmt->bindParam(':id', $booking_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        header("Location: ../admin/booking?process=errorSELECT");
        exit();
    }
} else {
    header("Location: ../admin/booking?process=GET");
    exit();
}


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                    // Disable verbose debug output for production
    $mail->isSMTP();                                       // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                              // Enable SMTP authentication
    $mail->Username   = "kapitanbato26@gmail.com";                // SMTP username
    $mail->Password   = "euys laln pmon hcfe";                          // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;       // Enable implicit TLS encryption
    $mail->Port       = 465;                               // TCP port to connect to

    $mail->setFrom("kapitanbato26@gmail.com", 'Resort Booking');
    $mail->addAddress($row['email'], $row['name']);

    $mail->isHTML(true);
    $mail->Subject = 'Booking Confirmation for ' . $row['title'];

    $actionUrl = 'bagotours.com/bagotours/b.php';  // This will give you the absolute path on the server

    $mail->Body = '
        <html>
            <body>
                <div style=\'font-family: Arial, sans-serif; border: 1px solid #ddd; padding: 20px; width: 300px; text-align: center;\'>
                    <h2>' . $row['title'] . '</h2>
                    <p>Are you sure you\'re going tomorrow?</p>
                    <form action=\'' . $actionUrl . '\' method=\'post\'>
                    <input type=\'hidden\' name=\'booking_id\' value=\'' . $booking_id . '\'>
                        <label style=\'margin-right: 10px;\'>
                            <input type=\'radio\' name=\'confirmation\' value=\'3\'> Yes
                        </label>
                        <label>
                            <input type=\'radio\' name=\'confirmation\' value=\'2\'> No
                        </label>
                        <br><br>
                        <input type=\'submit\' value=\'Submit\' style=\'background-color: #4CAF50; color: white; border: none; padding: 10px 20px; font-size: 16px; cursor: pointer;\'>
                    </form>
                </div>
            </body>
        </html>';



    $mail->AltBody = "Dear " . $row['name'] . ",\nYour booking for " . $row['title'] . " has been approved.\nBooking Details:\nResort: " . $row['title'] . "\nDate: " . $row['date_sched'] . "\nNumber of People: " . $row['people'] . "";

    $mail->send();
    header("Location: ../admin/booking?process=success");
    exit();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    header("Location: ../admin/booking?process=phpmailerError");
    exit();
}
