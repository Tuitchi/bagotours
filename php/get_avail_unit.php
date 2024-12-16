<?php
require '../include/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');

    // Get inputs from the AJAX request
    $accommodation_id = $_GET['accommodation_id'] ?? null;
    $start_date = $_GET['start_date'] ?? null;
    $end_date = $_GET['end_date'] ?? null;


    if (strtotime($start_date) > strtotime($end_date)) {
        echo json_encode(['success' => false, 'message' => 'Start date cannot be later than end date.']);
        exit;
    }

    try {
        // Fetch total units and calculate available units for the specified date range
        $stmt = $conn->prepare("
            SELECT 
                a.total_units - COALESCE(SUM(ba.units_reserved), 0) AS available_units
            FROM 
                accommodations a
            LEFT JOIN 
                booking_accommodations ba ON a.id = ba.accommodation_id
            LEFT JOIN 
                booking b ON ba.booking_id = b.id
            WHERE 
                a.id = :accommodation_id
                AND (
                    b.start_date <= :end_date AND b.end_date >= :start_date
                    OR b.start_date IS NULL  -- If no booking exists, treat it as available
                )
            GROUP BY 
                a.id
        ");

        $stmt->execute([
            ':accommodation_id' => $accommodation_id,
            ':start_date' => $start_date,
            ':end_date' => $end_date,
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Ensure available units do not fall below zero
            $available_units = max(0, (int) $result['available_units']);
            echo json_encode(['success' => true, 'available_units' => $available_units]);
        } else {
            // If no matching bookings found, return the total units as available
            $stmt = $conn->prepare("
                SELECT total_units
                FROM accommodations
                WHERE id = :accommodation_id
            ");
            $stmt->execute([
                ':accommodation_id' => $accommodation_id
            ]);
            $accommodation = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($accommodation) {
                // Return total units when no bookings are found
                echo json_encode(['success' => true, 'available_units' => (int) $accommodation['total_units']]);
            } else {
                echo json_encode(['success' => false]);
            }
        }
    } catch (PDOException $e) {
        error_log('Error calculating availability: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
}
?>