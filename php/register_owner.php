<?php
include '../include/db_conn.php';
session_start();

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $address = trim($_POST['purok'] . ", " . $_POST['barangay'] . ", " . $_POST['address']);
    $description = trim($_POST['description']);
    $type = trim($_POST['type']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);
    $proof = trim($_POST['proof']);
    $status = 0;

    if (empty($title) || empty($address) || empty($description) || empty($type) || empty($latitude) || empty($longitude)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
        exit;
    }

    if (isset($_FILES['proofImage']) && $_FILES['proofImage']['error'] == 0) {
        $upload_dir = '../upload/Permits/';
        $image = $_FILES['proofImage']['name'];
        $image_tmp = $_FILES['proofImage']['tmp_name'];
        $image_name = time() . '_' . basename($image);
        $image_path = $upload_dir . $image_name;

        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to create upload directory.']);
                exit();
            }
        }

        if (!move_uploaded_file($image_tmp, $image_path)) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload the proof image.']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Proof image upload failed or no image selected.']);
        exit();
    }

    // Handle second image (anotherImage)
    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        $upload_dir = '../upload/Tour Images/';
        $another_image = $_FILES['img']['name'];
        $another_image_tmp = $_FILES['img']['tmp_name'];
        $img = time() . '_' . basename($another_image);
        $another_image_path = $upload_dir . $img;

        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to create upload directory for the second image.']);
                exit();
            }
        }

        if (!move_uploaded_file($another_image_tmp, $another_image_path)) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload the second image.']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Second image upload failed or no image selected.']);
        exit();
    }
    $sql = "INSERT INTO tours (user_id, title, address, type, description, status, longitude, latitude, proof, proof_image, img) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssddsss", $user_id, $title, $address, $type, $description, $status, $longitude, $latitude, $proof, $image_name, $img);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'You Registered Successfully. Please wait for your acceptance process.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add tour: ' . $stmt->error]);
    }

    $stmt->close();
    exit();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}
?>
