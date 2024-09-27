<?php
include '../include/db_conn.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id > 0) {
        try {
            // Fetch the QR code path
            $sql1 = 'SELECT qr_code_path FROM qrcode WHERE id = :id';
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
            if ($stmt1->execute()) {
                $row = $stmt1->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    $qr_code_path = $row['qr_code_path'];
                    unlink($qr_code_path);
                } else {
                    echo json_encode(['success' => false, 'message' => 'QR code not found.']);
                    exit();
                }
            } else {
                error_log('Error fetching QR code data: ' . implode(' ', $stmt1->errorInfo()));
                echo json_encode(['success' => false, 'message' => 'Error fetching QR code data.']);
                exit();
            }
        } catch (PDOException $e) {
            error_log('Error preparing statement for fetching QR code: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error preparing statement for fetching QR code.']);
            exit();
        }

        // Prepare to delete the QR code entry from the database
        $sql = 'DELETE FROM qrcode WHERE id = :id';

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'QR code deleted successfully.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Unable to delete QR code. QR code may not exist.']);
                }
            } else {
                error_log('Error executing QR code deletion: ' . implode(' ', $stmt->errorInfo()));
                echo json_encode(['success' => false, 'message' => 'Error executing QR code deletion.']);
            }
        } catch (PDOException $e) {
            error_log('Error preparing statement for deleting QR code: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error preparing statement for deleting QR code.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid QR code ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No QR code ID specified.']);
}
