<?php
// Include your database connection file
require_once '../include/db_conn.php'; // Update with your correct path

header('Content-Type: application/json');

// Initialize response array
$response = ['success' => false, 'message' => '', 'errors' => []];

try {
    // Check if the request is POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Fetch the form data
        $id = trim($_POST['id'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $province = trim($_POST['province'] ?? '');
        $city = trim($_POST['city'] ?? '');

        // Simple validation
        if (empty($id)) {
            $response['errors']['address'] = 'User ID is missing.';
        }
        if (empty($country)) {
            $response['errors']['address'] = 'Country is required.';
        }
        if (empty($province) && $country == 'Philippines') {
            $response['errors']['address'] = 'Province is required.';
            if (empty($city)) {
                $response['errors']['address'] = 'City/Municipality is required.';
            }
        }


        // If there are no validation errors, proceed to update
        if (empty($response['errors'])) {
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
                $response['success'] = true;
                $response['message'] = 'Address updated successfully.';
                $response['reload'] = true;
            } else {
                $response['errors']['address'] = 'Failed to update address. Please try again.';
            }
        }
    } else {
        $response['errors']['address'] = 'Invalid request method.';
    }
} catch (PDOException $e) {
    // Log the error
    error_log('Database Error: ' . $e->getMessage());
    $response['errors']['address'] = 'An internal error occurred. Please try again later.';
} catch (Exception $e) {
    // Log general errors
    error_log('General Error: ' . $e->getMessage());
    $response['errors']['address'] = 'Something went wrong. Please try again.';
}

// Return the JSON response
echo json_encode($response);
