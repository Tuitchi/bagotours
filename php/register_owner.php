<?php
include '../include/db_conn.php';
session_start();

$errors = [];
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $address = trim($_POST['purok'] . ", " . $_POST['barangay'] . ", " . $_POST['address']);
    $description = trim($_POST['description']);
    $type = trim($_POST['type']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);
    $proofs = $_POST['proof'];
    $proof_images = $_FILES['proofImage'];
    $tourImages = $_FILES['tourImage'];
    $status = 0;

    if (empty($title) || empty($address) || empty($description) || empty($type) || empty($latitude) || empty($longitude) || empty($proofs) || empty($proof_images) || empty($tourImages)) {
        echo json_encode(['success' => false, 'errors' => "Please fill in all required fields."]);
        exit();
    }

    $img = '';
    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../upload/Tour Images/';
        $image = $_FILES['img']['name'];
        $image_tmp = $_FILES['img']['tmp_name'];
        $image_name = time() . '_' . basename($image);
        $image_path = $upload_dir . $image_name;

        if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
            echo json_encode(['success' => false, 'errors' => 'Failed to create upload directory.']);
            exit();
        }

        if (move_uploaded_file($image_tmp, $image_path)) {
            $img = $image_name;
        } else {
            echo json_encode(['success' => false, 'errors' => 'Failed to upload file.']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'errors' => 'File upload error.']);
        exit();
    }

    try {
        $sql = "INSERT INTO tours (user_id, title, address, type, description, status, longitude, latitude, img) 
                VALUES (:user_id, :title, :address, :type, :description, :status, :longitude, :latitude, :img)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':address' => $address,
            ':type' => $type,
            ':description' => $description,
            ':status' => $status,
            ':longitude' => $longitude,
            ':latitude' => $latitude,
            ':img' => $img
        ]);

        $tour_id = $conn->lastInsertId();

        echo insertProof($conn, $tour_id, $proofs, $proof_images);
        echo insertTourImg($conn, $tour_id, $tourImages);
        if(!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit();
        }

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

// Insert proofs function
function insertProof($conn, $tour_id, $proofs, $proofImgs,) {
    foreach ($proofImgs['name'] as $index => $proofimg) {
        if (isset($proofImgs['name'][$index]) && $proofImgs['error'][$index] === UPLOAD_ERR_OK) {
            $upload_dir = '../upload/Permits/';
            $filename = time() . '_' . basename($proofimg);
            $filepath = $upload_dir . $filename;

            if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
                return $errors['proof']="Failed to create permits directory.";
            }

            if (move_uploaded_file($proofImgs['tmp_name'][$index], $filepath)) {
                try {
                    $sql = "INSERT INTO proof (tour_id, proof, proof_image) VALUES (:tour_id, :proof, :proof_image)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([':tour_id' => $tour_id, ':proof' => $proofs, ':proof_image' => $filename]);
                } catch (PDOException $e) {
                    return $errors['proof']="Failed to insert proof: : " . $e->getMessage();
                }
            } else {
                return $errors['proof']="Failed to upload proof image.";
            }
        }
    }
}

// Insert tour images function
function insertTourImg($conn, $tour_id, $tourImages) {
    foreach ($tourImages['name'] as $index => $name) {
        if (isset($tourImages['name'][$index]) && $tourImages['error'][$index] === UPLOAD_ERR_OK) {
            $upload_dir = '../upload/Tour Images/';
            $filename = time() . '_' . basename($name);
            $filepath = $upload_dir . $filename;

            // Create upload directory if it doesn't exist
            if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
                return $errors['tour']="Failed to create tour images directory.";
            }

            // Move uploaded tour image
            if (move_uploaded_file($tourImages['tmp_name'][$index], $filepath)) {
                try {
                    $sql = "INSERT INTO tours_image (tour_id, img) VALUES (:tour_id, :img)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([':tour_id' => $tour_id, ':img' => $filename]);
                } catch (PDOException $e) {
                    return $errors['tour']="Failed to insert tour images: : " . $e->getMessage();
                }
            } else {
                return $errors['tour']="Failed to upload tour image.";
            }
        }
    }
}

// Notify admin function
function notifyAdmin($conn, $tour_id) {
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