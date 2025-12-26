<?php
$html = file_get_contents(__DIR__ . '/login.html');
$inject = "\n<script src=\"assets/js/login.js\"></script>\n";
echo str_replace('</body>', $inject . '</body>', $html);
?>


