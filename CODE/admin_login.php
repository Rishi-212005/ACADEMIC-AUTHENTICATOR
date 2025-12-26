<?php
require_once __DIR__ . '/config.php';
$html = file_get_contents(__DIR__ . '/admin_login.html');
$inject = "\n<script src=\"assets/js/admin_login.js\"></script>\n";
echo str_replace('</body>', $inject . '</body>', $html);
?>


