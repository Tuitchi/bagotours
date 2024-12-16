<?php
include '../include/db_conn.php';
require_once '../func/func.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve POST data
    $user_id = $_POST['user_id'];
    $tour_id = $_POST['tour_id'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $people = $_POST['people'];
    $accommodations = isset($_POST['accommodations']) ? $_POST['accommodations'] : [];
    $BID = generateUniqueBidId($conn);

    // Validate input fields
    if (!empty($user_id) && !empty($tour_id) && !empty($people) && !empty($start) && !empty($end)) {
        try {
            // Insert booking into the 'booking' table
            $stmt = $conn->prepare("INSERT INTO booking (BID, user_id, tour_id, people, start_date, end_date, status) 
                                        VALUES (:BID, :user_id, :tour_id, :people, :start, :end, 0)");


            $stmt->bindParam(':BID', $BID, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
            $stmt->bindParam(':people', $people, PDO::PARAM_INT);
            $stmt->bindParam(':start', $start, PDO::PARAM_STR);
            $stmt->bindParam(':end', $end, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $booking_id = $conn->lastInsertId();

                // Check if any accommodations were selected and insert them
                if (!empty($accommodations)) {
                    $stmt = $conn->prepare("INSERT INTO booking_accommodations (booking_id, accommodation_id, units_reserved) 
                                                VALUES (:booking_id, :accommodation_id, :units_reserved)");

                    foreach ($accommodations as $accommodation_id => $quantity) {
                        // Bind parameters for each accommodation
                        $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
                        $stmt->bindParam(':accommodation_id', $accommodation_id, PDO::PARAM_INT);
                        $stmt->bindParam(':units_reserved', $quantity, PDO::PARAM_INT);
                        $stmt->execute();
                    }
                }

                $stmt = $conn->prepare("SELECT user_id, title FROM tours WHERE id = :tour_id");
                $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // Get user name for the notification
                $user_stmt = $conn->prepare("SELECT CONCAT(firstname, ' ', lastname) AS name FROM users WHERE id = :user_id");
                $user_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $user_stmt->execute();
                $name = $user_stmt->fetchColumn();

                if ($row) {
                    createNotification($conn, $row['user_id'], $tour_id, "$name booked in " . $row['title'] . ".", "booking.php?id=" . $booking_id, "booking");
                }

                echo json_encode(['success' => true, 'message' => 'Booking confirmed successfully.']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to book the tour. Please try again.']);
            }
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'An error occurred while processing your request.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Please fill in all required fields.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
