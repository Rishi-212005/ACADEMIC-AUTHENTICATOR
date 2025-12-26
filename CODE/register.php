<?php
// Serve existing registration HTML and inject bridge JS without altering markup
$html = file_get_contents(__DIR__ . '/registration.html');
$inject = "\n<script src=\"assets/js/register.js\"></script>\n";
echo str_replace('</body>', $inject . '</body>', $html);
?>


