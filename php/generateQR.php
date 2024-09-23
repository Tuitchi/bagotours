<?php

require '../include/db_conn.php';
require '../vendor/autoload.php';

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

if (!extension_loaded('gd')) {
    echo json_encode(['success' => false, 'message' => 'GD extension is not enabled.']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedValue = $_POST["tour"];
    list($title, $tour_id) = explode('|', $selectedValue);
    require_once '../func/func.php';

    $qrCodePath = '../upload/QRcodes/' . $title . '.png';
    $url = 'http://bagotours.com/bagotours/visit?tour_id=' . $tour_id;

    if (empty($selectedValue)) {
        echo json_encode(['success' => false, 'message' => 'Please select an option.']);
        exit();
    }

    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $writer = new PngWriter();

        $qrCode = QrCode::create($url)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $label = Label::create($title)
            ->setTextColor(new Color(255, 0, 0));

        $logoPath = __DIR__ . '/../assets/icons/websiteIcon.png';
        if (file_exists($logoPath)) {
            $logo = Logo::create($logoPath)
                ->setResizeToWidth(50)
                ->setPunchoutBackground(true);
        } else {
            $logo = null;
        }
        if (validateQR($conn, $tour_id)) {
            echo json_encode(['success' => false, 'message' => 'This tour produces a QR code already.']);
            exit();
        } else {
            try {
                $result = $writer->write($qrCode, $logo, $label);
                $result->saveToFile($qrCodePath);

                $sql = "INSERT INTO qrcode(`tour_id`, `title`, `qr_code_path`, created_at, updated_at)
                        VALUES (:tour_id, :title, :qr_code_path, NOW(), NOW())";

                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bindParam(':tour_id', $tour_id);
                    $stmt->bindParam(':title', $title);
                    $stmt->bindParam(':qr_code_path', $qrCodePath);
                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'QR code generated successfully.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'There was a problem generating a QR code.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error preparing statement.']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error generating QR code: ' . $e->getMessage()]);
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid URL: ' . $url]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
