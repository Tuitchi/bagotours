<?php
session_start();

// Ensure the request is a POST request and contains the sidebar_hidden value
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sidebar_hidden'])) {
    // Save the sidebar state in the session
    $_SESSION['sidebar_hidden'] = $_POST['sidebar_hidden'];

    // Optionally, you can store this in the database if needed
    // You can also echo a success message
    echo "Sidebar state updated successfully!";
} else {
    // Handle invalid requests
    http_response_code(400);
    echo "Invalid request!";
}
