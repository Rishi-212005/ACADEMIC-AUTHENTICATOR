<?php
require_once 'config.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS ocr_saved_details (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        institution_id INT(10) UNSIGNED NOT NULL,
        student_name VARCHAR(255) DEFAULT NULL,
        hall_ticket_no VARCHAR(64) DEFAULT NULL,
        certificate_no VARCHAR(64) DEFAULT NULL,
        branch VARCHAR(128) DEFAULT NULL,
        exam_type VARCHAR(64) DEFAULT NULL,
        exam_month_year VARCHAR(32) DEFAULT NULL,
        total_marks INT(11) DEFAULT NULL,
        total_credits DECIMAL(6,2) DEFAULT NULL,
        sgpa DECIMAL(4,2) DEFAULT NULL,
        cgpa DECIMAL(4,2) DEFAULT NULL,
        date_of_issue DATE DEFAULT NULL,
        file_hash CHAR(64) DEFAULT NULL,
        original_file_path VARCHAR(512) DEFAULT NULL,
        status VARCHAR(32) NOT NULL DEFAULT 'pending',
        file_name VARCHAR(255) GENERATED ALWAYS AS (SUBSTRING_INDEX(original_file_path, '/', -1)) STORED,
        confidence DECIMAL(4,3) DEFAULT NULL,
        university VARCHAR(255) DEFAULT NULL,
        college VARCHAR(255) DEFAULT NULL,
        course VARCHAR(255) DEFAULT NULL,
        medium VARCHAR(64) DEFAULT NULL,
        pass_status VARCHAR(64) DEFAULT NULL,
        aggregate VARCHAR(64) DEFAULT NULL,
        achievement VARCHAR(255) DEFAULT NULL,
        raw_extracted_fields LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (JSON_VALID(raw_extracted_fields)),
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
        PRIMARY KEY (id),
        KEY idx_institution_id (institution_id),
        KEY idx_file_hash (file_hash),
        KEY idx_created_at (created_at),
        FOREIGN KEY (institution_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $conn = db();
    if ($conn->query($sql) === TRUE) {
        echo "Table ocr_saved_details created successfully\n";
    } else {
        echo "Error creating table: " . $conn->error . "\n";
    }

    // Test if table exists
    $result = $conn->query("SHOW TABLES LIKE 'ocr_saved_details'");
    if ($result->num_rows > 0) {
        echo "Table ocr_saved_details exists and is ready to use\n";
    } else {
        echo "Table ocr_saved_details was not created\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
