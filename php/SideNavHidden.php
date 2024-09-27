<?php
session_start();
if (isset($_POST['sidebar_hidden'])) {
    $_SESSION['sidebar_hidden'] = $_POST['sidebar_hidden'];
    echo $_SESSION['sidebar_hidden'];
}
