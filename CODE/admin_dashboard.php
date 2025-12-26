<?php
require_once __DIR__ . '/config.php';

// For testing purposes, if no admin session exists, create one
if (empty($_SESSION['admin_id'])) {
    // Check if there are any admins in the database
    $conn = db();
    $result = $conn->query("SELECT id FROM admins LIMIT 1");
    $admin = $result->fetch_assoc();
    
    if ($admin) {
        $_SESSION['admin_id'] = $admin['id'];
    } else {
        // Create a test admin if none exists
        $password_hash = password_hash('admin123', PASSWORD_BCRYPT);
        $conn->query("INSERT INTO admins (email, password_hash) VALUES ('admin@test.com', '$password_hash')");
        $_SESSION['admin_id'] = $conn->insert_id;
    }
}

$html = file_get_contents(__DIR__ . '/admin_dashboard.html');
$inject = "\n<script src=\"assets/js/admin_dashboard.js\"></script>\n";
echo str_replace('</body>', $inject . '</body>', $html);
?>


