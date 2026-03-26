<?php
include_once('include/auth.php');

if (!$user) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!validateCSRFToken($_POST['csrf_token'])) {
        http_response_code(401);
        exit("Invalid CSRF token");
    }
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

?>