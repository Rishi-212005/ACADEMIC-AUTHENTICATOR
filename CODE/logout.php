<?php
require_once __DIR__ . '/config.php';

// Destroy the session
session_unset();
session_destroy();

// Redirect to login page
header('Location: login.php');
exit;
?>

