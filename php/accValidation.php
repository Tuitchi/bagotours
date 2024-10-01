<?php
if (isset($_SESSION['user_id'])) {
    if(!empty($_SESSION['role'])) {
        if ($_SESSION['role'] !== $pageRole) {
            session_unset();
            session_destroy();
            header("Location: ../index.php");
            exit;
        }
    } else {
        session_unset();
        session_destroy();
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
