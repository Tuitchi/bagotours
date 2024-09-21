<?php
// Include necessary files and start the session
include '../include/db_conn.php';
include '../func/user_func.php';
session_start();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the tour ID from the form
    $tour_id = $_POST['tour_id'];

    // Get the new values from the form
    $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');
    $type = htmlspecialchars($_POST['type'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    $status = (int)$_POST['status']; // Cast the status to an integer

    // Validate inputs (this can be extended further)
    if (empty($title) || empty($address) || empty($type) || empty($description)) {
        // Redirect back with an error message if validation fails
        header("Location: edit_tour.php?error=EmptyFields");
        exit();
    }

    // Prepare the SQL update statement
    $sql = "UPDATE tours SET title = ?, address = ?, type = ?, description = ?, status = ? WHERE id = ?";

    // Use a prepared statement to prevent SQL injection
    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameters to the SQL query
        $stmt->bind_param('ssssii', $title, $address, $type, $description, $status, $tour_id);

        // Execute the query
        if ($stmt->execute()) {
            // Success, redirect back to the view page with a success message
            header("Location: view_tour.php?success=TourUpdated");
        } else {
            // If the execution failed, redirect with an error message
            header("Location: edit_tour.php?error=UpdateFailed");
        }

        // Close the statement
        $stmt->close();
    } else {
        // Redirect with an error if the statement couldn't be prepared
        header("Location: edit_tour.php?error=SQLPrepareFailed");
    }
} else {
    // Redirect to the main page if accessed without submitting the form
    header("Location: tour.php");
    exit();
}
?>
