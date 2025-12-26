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
$html = file_get_contents(__DIR__ . '/institute_dashboard.html');
echo $html;
?>



