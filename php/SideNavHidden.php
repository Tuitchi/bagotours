<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sidebar_hidden'])) {
    $_SESSION['sidebar_hidden'] = $_POST['sidebar_hidden'];
    echo "Sidebar state updated successfully!";
} else {
    http_response_code(400);
    echo "Invalid request!";
}
