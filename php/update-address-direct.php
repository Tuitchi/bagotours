<?php
// Include your database connection file
require_once '../include/db_conn.php';

// Initialize error array
$errors = [];

try {
    // Check if the request is POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Fetch the form data
        $id = trim($_POST['id'] ?? '');
        $tour_id = trim($_POST['tour_id'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $province = trim($_POST['province'] ?? '');
        $city = trim($_POST['city'] ?? '');

        // Simple validation
        if (empty($id)) {
            $errors[] = 'User ID is missing.';
        }
        if (empty($country)) {
            $errors[] = 'Country is required.';
        }
        if (empty($province) && $country == 'Philippines') {
            $errors[] = 'Province is required.';
        }
        if (empty($city) && $country == 'Philippines') {
            $errors[] = 'City/Municipality is required.';
        }

        // If there are no validation errors, proceed to update
        if (empty($errors)) {
            // Format the address based on country selection
            if ($country == 'Philippines') {
                $homeAddress = "$city, $province, $country";
            } else {
                // For non-Philippines addresses, only use country
                $homeAddress = $country;
            }

            $sql = "UPDATE users 
                    SET home_address = :home_address
                    WHERE id = :id";

            $stmt = $conn->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':home_address', $homeAddress, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Execute the query
            if ($stmt->execute()) {
                // Success - redirect back to the visit page
                header("Location: ../visit.php?tour_id=$tour_id&success=1");
                exit;
            } else {
                $errors[] = 'Failed to update address. Please try again.';
            }
        }
    } else {
        $errors[] = 'Invalid request method.';
    }
} catch (PDOException $e) {
    // Log the error
    error_log('Database Error: ' . $e->getMessage());
    $errors[] = 'An internal error occurred. Please try again later.';
} catch (Exception $e) {
    // Log general errors
    error_log('General Error: ' . $e->getMessage());
    $errors[] = 'Something went wrong. Please try again.';
}

// If we reached here, there were errors
// Redirect back with error messages
$error_string = urlencode(implode(", ", $errors));
header("Location: ../visit.php?tour_id=$tour_id&error=$error_string");
exit; 