<?php
require_once __DIR__ . '/config.php';
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$type = $_SESSION['user_type'] ?? null;
if ($type !== 'institution') {
    header('Location: login.php');
    exit;
}

// Redirect to document upload page
header('Location: document_upload.php');
exit;
?>