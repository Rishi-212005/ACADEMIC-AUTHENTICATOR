<?php
// Database and app configuration
session_start();

$DB_HOST = 'sql308.infinityfree.com';
$DB_USER = 'if0_40761453';
$DB_PASS = 'academicA2025';
$DB_NAME = 'if0_40761453_acedamicA';

function db(): mysqli {
    static $conn = null;
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
    if ($conn === null) {
        $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS);
        if ($conn->connect_error) {
            http_response_code(500);
            die('Database connection failed');
        }
        $conn->query("CREATE DATABASE IF NOT EXISTS `$DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $conn->select_db($DB_NAME);
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

function respond_json($data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function run_migrations(): void {
    $sqls = [
        // admins table
        "CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        // registrations table for orgs/institutions
        "CREATE TABLE IF NOT EXISTS registrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type ENUM('organization','institution') NOT NULL,
            name VARCHAR(255) NOT NULL,
            org_type VARCHAR(100) NULL,
            inst_type VARCHAR(100) NULL,
            inst_code VARCHAR(100) NULL,
            inst_university VARCHAR(255) NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50),
            website VARCHAR(255),
            address_line1 VARCHAR(255),
            address_line2 VARCHAR(255),
            city VARCHAR(100),
            state VARCHAR(100),
            district VARCHAR(100),
            pincode VARCHAR(20),
            country VARCHAR(100),
            password_hash VARCHAR(255) NOT NULL,
            document_path VARCHAR(255),
            status ENUM('pending','approved','rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        // users table for approved accounts (both types)
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type ENUM('organization','institution') NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            phone VARCHAR(50),
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        // otps table
        "CREATE TABLE IF NOT EXISTS otps (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            code VARCHAR(10) NOT NULL,
            expires_at DATETIME NOT NULL,
            used TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX(user_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        // admin otps
        "CREATE TABLE IF NOT EXISTS admin_otps (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NOT NULL,
            code VARCHAR(10) NOT NULL,
            expires_at DATETIME NOT NULL,
            used TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX(admin_id),
            FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        // OCR extracted data storage
        "CREATE TABLE IF NOT EXISTS ocr_extracted_data (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            extracted_data JSON NOT NULL,
            confidence DECIMAL(3,2) NOT NULL,
            file_path VARCHAR(255) NOT NULL,
            file_name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX(user_id),
            INDEX(created_at),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        // Organization validations storage
        "CREATE TABLE IF NOT EXISTS organization_validations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            organization_id INT NOT NULL,
            student_name VARCHAR(255) DEFAULT NULL,
            hall_ticket_no VARCHAR(64) DEFAULT NULL,
            certificate_no VARCHAR(64) DEFAULT NULL,
            college_name VARCHAR(255) DEFAULT NULL,
            university_name VARCHAR(255) DEFAULT NULL,
            branch VARCHAR(128) DEFAULT NULL,
            cgpa DECIMAL(4,2) DEFAULT NULL,
            sgpa DECIMAL(4,2) DEFAULT NULL,
            pass_status VARCHAR(64) DEFAULT NULL,
            aggregate VARCHAR(255) DEFAULT NULL,
            confidence DECIMAL(5,2) DEFAULT NULL,
            validation_score DECIMAL(5,2) DEFAULT NULL,
            validation_status VARCHAR(20) DEFAULT NULL,
            extracted_fields JSON DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX(organization_id),
            INDEX(hall_ticket_no),
            INDEX(certificate_no),
            INDEX(created_at),
            FOREIGN KEY (organization_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    ];
    $conn = db();
    foreach ($sqls as $sql) {
        $conn->query($sql);
    }
    // apply additive migrations for extra registration fields if missing (compat with MySQL 5.7+)
    ensure_column('registrations', 'org_type', "ALTER TABLE registrations ADD COLUMN org_type VARCHAR(100) NULL");
    ensure_column('registrations', 'inst_type', "ALTER TABLE registrations ADD COLUMN inst_type VARCHAR(100) NULL");
    ensure_column('registrations', 'inst_code', "ALTER TABLE registrations ADD COLUMN inst_code VARCHAR(100) NULL");
    ensure_column('registrations', 'inst_university', "ALTER TABLE registrations ADD COLUMN inst_university VARCHAR(255) NULL");
    // seed admin
    $email = 'admin@SIH';
    $password = 'PASSWORD@SIH';
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT IGNORE INTO admins(email, password_hash) VALUES(?, ?)");
    $stmt->bind_param('ss', $email, $hash);
    $stmt->execute();
}

run_migrations();

function ensure_column(string $table, string $column, string $alterSql): void {
    $conn = db();
    $stmt = $conn->prepare("SELECT COUNT(*) c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->bind_param('ss', $table, $column);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if (empty($res['c'])) {
        $conn->query($alterSql);
    }
}
?>


