<?php
require_once __DIR__ . '/config.php';
if (empty($_SESSION['admin_id'])) { header('Location: admin_login.php'); exit; }
$html = file_get_contents(__DIR__ . '/admin_organization_detail.html');
echo str_replace('</body>', "\n<script src=\"assets/js/admin_detail.js\"></script>\n</body>", $html);
?>



