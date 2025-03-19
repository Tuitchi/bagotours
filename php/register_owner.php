<?php

use Google\Service\HangoutsChat\PermissionSetting;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
include '../include/db_conn.php';
session_start();

$errors = [];
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    if (empty($title) || empty($address) || empty($description) || empty($type) || empty($latitude) || empty($longitude) || empty($proof_permits) || empty($proof_images) || empty($tour_images)) {
        echo json_encode(['success' => false, 'errors' => "Please fill in all required fields."]);
        exit();
    }

    // Process proofs and proof images (combine into comma-separated strings)
    $proofs_str = implode(",", $proof_permits); // Combine proofs into a comma-separated string
    $proof_images_filenames = processFiles($proof_images, '../upload/Permits/'); // Process proof images
    $proof_images_str = implode(",", $proof_images_filenames); // Combine proof image filenames

    // Process tour images (combine into comma-separated strings)
    $tour_images_filenames = processFiles($tour_images, '../upload/Tour Images/'); // Process tour images
    $tour_images_str = implode(",", $tour_images_filenames); // Combine tour image filenames

    try {
        // Insert tour details into the database (with proof and image data)
        $sql = "INSERT INTO tours (user_id, title, address, type, description, longitude, latitude, img, proof_title, proof_image, bookable, status)
                VALUES (:user_id, :title, :address, :type, :description, :longitude, :latitude, :img, :proof_title, :proof_image, :bookable, :status)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':address' => $address,
            ':type' => $type,
            ':description' => $description,
            ':longitude' => $longitude,
            ':latitude' => $latitude,
            ':img' => $tour_images_str,  // Store comma-separated tour image filenames
            ':proof_title' => $proofs_str,  // Store comma-separated proof titles
            ':proof_image' => $proof_images_str,  // Store comma-separated proof image filenames
            ':bookable' => $bookable,
            ':status' => 'Pending'  // Set status to 0 for pending approval
        ]);
        $tour_id = $conn->lastInsertId();
        notifyAdmin($conn, $tour_id);
        echo json_encode(['success' => true, 'message' => 'You Registered Successfully. Please wait for your acceptance process.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'errors' => 'Failed to add tour: ' . $e->getMessage()]);
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
                $filenames[] = $filename; // Add to the filenames array
            } else {
                return ['error' => "Failed to upload image."];
            }
        }
    }

    return $filenames;
}
function notifyAdmin($conn, $tour_id)
{
    try {
        $sql = "SELECT id FROM users WHERE role = 'admin' LIMIT 1";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute()) {
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            require_once '../func/func.php';
            $message = "Someone upgraded their account, please check the application form.";
            $url = "pending.php?view=true&id=$tour_id";
            $type = 'upgrade';
            createNotification($conn, $admin['id'], $tour_id, $message, $url, $type);
        } else {
            return json_encode(['success' => false, 'errors' => 'Failed to get admin id.']);
        }
    } catch (PDOException $e) {
        return json_encode(['success' => false, 'errors' => 'Failed to notify admin: ' . $e->getMessage()]);
    }
}