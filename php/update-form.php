<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
include '../include/db_conn.php';
session_start();

$errors = [];
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tour_id = $_POST['tour_id'];
    $title = trim($_POST['title']);
    $address = trim($_POST['address']);
    $description = trim($_POST['description']);
    $type = trim($_POST['type']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);
    $proof_permits = [
        'Building Permit',
        'Business Permit',
        'Environmental Compliance Certificate (ECC)',
        'Barangay Clearance',
        'Fire Safety Inspection Certificate'
    ];
    $proof_images = $_FILES['proof-images'];
    $tour_images = $_FILES['tour-images'];
    $bookable = $_POST['bookable'];

    if (empty($tour_id) || empty($title) || empty($address) || empty($description) || empty($type) || empty($latitude) || empty($longitude)) {
        echo json_encode(['success' => false, 'errors' => "Please fill in all required fields."]);
        exit();
    }

    // Process proofs and proof images only if files are uploaded
    $proofs_str = implode(",", $proof_permits);
    $proof_images_str = '';
    if (!empty($proof_images['name'][0])) {
        $proof_images_filenames = processFiles($proof_images, '../upload/Permits/');
        $proof_images_str = implode(",", $proof_images_filenames);
    }

    // Process tour images only if files are uploaded
    $tour_images_str = '';
    if (!empty($tour_images['name'][0])) {
        $tour_images_filenames = processFiles($tour_images, '../upload/Tour Images/');
        $tour_images_str = implode(",", $tour_images_filenames);
    }

    try {
        // Update tour details in the database
        $sql = "UPDATE tours 
                SET title = :title,
                    address = :address,
                    type = :type,
                    description = :description,
                    longitude = :longitude,
                    latitude = :latitude,
                    bookable = :bookable,
                    status = 'Pending',
                    expiry = NULL";

        // Append to query if images are provided
        if ($tour_images_str) {
            $sql .= ", img = :img";
        }
        if ($proof_images_str) {
            $sql .= ", proof_image = :proof_image";
        }

        $sql .= " WHERE id = :tour_id AND user_id = :user_id";

        $stmt = $conn->prepare($sql);

        // Bind parameters
        $params = [
            ':title' => $title,
            ':address' => $address,
            ':type' => $type,
            ':description' => $description,
            ':longitude' => $longitude,
            ':latitude' => $latitude,
            ':bookable' => $bookable,
            ':tour_id' => $tour_id,
            ':user_id' => $user_id,
        ];

        if ($tour_images_str) {
            $params[':img'] = $tour_images_str;
        }
        if ($proof_images_str) {
            $params[':proof_image'] = $proof_images_str;
        }

        $stmt->execute($params);

        notifyAdmin($conn, $tour_id, $user_id);
        echo json_encode(['success' => true, 'message' => 'Tour updated successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'errors' => 'Failed to update tour: ' . $e->getMessage()]);
    }

    exit();
} else {
    echo json_encode(['success' => false, 'errors' => 'Invalid request method.']);
    exit();
}

// Helper function to process the file uploads and return the filenames
function processFiles($files, $upload_dir)
{
    $filenames = [];

    foreach ($files['name'] as $index => $name) {
        if (isset($files['name'][$index]) && $files['error'][$index] === UPLOAD_ERR_OK) {
            $filename = time() . '_' . basename($name);
            $filepath = $upload_dir . $filename;

            // Create the upload directory if it doesn't exist
            if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
                return ['error' => "Failed to create upload directory."];
            }

            // Move the uploaded file
            if (move_uploaded_file($files['tmp_name'][$index], $filepath)) {
                $filenames[] = $filename;
            } else {
                return ['error' => "Failed to upload image."];
            }
        }
    }

    return $filenames;
}
function notifyAdmin($conn, $tour_id, $user_id)
{
    // Get user email
    $stmt = $conn->prepare("SELECT email FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    if (!$stmt->execute()) {
        return json_encode(['success' => false, 'errors' => 'Failed to get user email.']);
    }
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    try {
        // Get admin ID
        $sql = "SELECT id FROM users WHERE role = 'admin' LIMIT 1";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute()) {
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            require_once '../func/func.php';

            // Construct the message
            $message = $user['email'] . " has resubmitted the application form. Kindly review the form.";  // Using user email or other info

            $url = "pending.php?view=true&id=$tour_id";
            $type = 'upgrade';

            // Create notification
            createNotification($conn, $admin['id'], $tour_id, $message, $url, $type);
        } else {
            return json_encode(['success' => false, 'errors' => 'Failed to get admin id.']);
        }
    } catch (PDOException $e) {
        return json_encode(['success' => false, 'errors' => 'Failed to notify admin: ' . $e->getMessage()]);
    }
}
