<?php
require_once __DIR__ . '/config.php';
// If we got here, run_migrations() already executed from config.php
header('Content-Type: text/plain');
echo "Migrations executed.\n";
$conn = db();
$res = $conn->query("SHOW TABLES");
while ($row = $res->fetch_row()) {
    echo "- " . $row[0] . "\n";
}
?>


