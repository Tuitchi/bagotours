<?php
include '../include/db_conn.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $query = "
    SELECT 
        u.id AS user_id, 
        CONCAT(firstname, ' ', lastname) AS name, 
        b.BID as bookingId, 
        u.email, 
        u.home_address, 
        u.profile_picture, 
        u.phone_number, 
        u.is_trusted,
        b.id AS booking_id, 
        b.start_date, 
        b.end_date, 
        b.people, 
        b.status, 
        b.date_created as date_created, 
        t.id as tour_id, 
        t.title AS tour_title
    FROM booking b
    JOIN users u ON u.id = b.user_id 
    JOIN tours t ON t.id = b.tour_id
    WHERE b.id = :id
";

    try {
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $pending = $stmt->fetch(PDO::FETCH_ASSOC);

            // Now fetch the related accommodations for the booking
            $accommodationQuery = "
            SELECT 
                ba.accommodation_id, 
                a.name AS accommodation_name, 
                a.description AS accommodation_description, 
                ba.units_reserved
            FROM booking_accommodations ba
            JOIN accommodations a ON a.id = ba.accommodation_id
            WHERE ba.booking_id = :booking_id
        ";

            $accommodationStmt = $conn->prepare($accommodationQuery);
            $accommodationStmt->bindParam(':booking_id', $pending['booking_id'], PDO::PARAM_INT);
            $accommodationStmt->execute();

            $accommodations = $accommodationStmt->fetchAll(PDO::FETCH_ASSOC);

            // Add accommodations to the booking result
            $pending['accommodations'] = $accommodations;

            if ($pending['status'] == 0 || $pending['status'] == 1) {
                echo json_encode(['success' => true, 'book' => $pending]);
                exit();
            }
            echo json_encode(['success' => false, 'message' => 'Wait for book to finish.']);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
            exit();
        }
    } catch (PDOException $e) {
        error_log("Error executing query: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error processing request']);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}
