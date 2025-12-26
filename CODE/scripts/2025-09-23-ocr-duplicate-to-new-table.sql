
    CREATE TABLE IF NOT EXISTS ocr_saved_certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    certificate_id INT NULL, -- original certificates.id for traceability
    institution_id INT NOT NULL,
    student_name VARCHAR(255) NULL,
    hall_ticket_no VARCHAR(50) NULL,
    certificate_no VARCHAR(100) NULL,
    branch VARCHAR(255) NULL,
    exam_type VARCHAR(100) NULL,
    exam_month_year VARCHAR(50) NULL,
    total_marks INT NULL,
    total_credits DECIMAL(7,2) NULL,
    sgpa DECIMAL(4,2) NULL,
    cgpa DECIMAL(4,2) NULL,
    date_of_issue DATE NULL,
    file_hash CHAR(64) NULL,
    original_file_path VARCHAR(255) NULL,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_institution_id (institution_id),
    KEY idx_certificate_no (certificate_no),
    KEY idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



    DROP TRIGGER IF EXISTS trg_certificates_after_insert_copy;
    DELIMITER $$
    CREATE TRIGGER trg_certificates_after_insert_copy
    AFTER INSERT ON certificates
    FOR EACH ROW
    BEGIN
    INSERT INTO ocr_saved_certificates (
        certificate_id,
        institution_id,
        student_name,
        hall_ticket_no,
        certificate_no,
        branch,
        exam_type,
        exam_month_year,
        total_marks,
        total_credits,
        sgpa,
        cgpa,
        date_of_issue,
        file_hash,
        original_file_path,
        status
    ) VALUES (
        NEW.id,
        NEW.institution_id,
        NEW.student_name,
        NEW.hall_ticket_no,
        NEW.certificate_no,
        NEW.branch,
        NEW.exam_type,
        NEW.exam_month_year,
        NEW.total_marks,
        NEW.total_credits,
        NEW.sgpa,
        NEW.cgpa,
        NEW.date_of_issue,
        NEW.file_hash,
        NEW.original_file_path,
        NEW.status
    );
    END$$
    DELIMITER ;
