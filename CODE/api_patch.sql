-- Patch to update the handle_save_ocr_data function to use ocr_saved_details table
-- This patch should be applied to the existing api.php file

-- Replace the comment
UPDATE api_functions SET comment = 'Save OCR extracted data to ocr_saved_details table with mapped fields'
WHERE function_name = 'handle_save_ocr_data';

-- Replace the INSERT statement
UPDATE api_functions SET sql_query = 'INSERT INTO ocr_saved_details (institution_id, student_name, hall_ticket_no, certificate_no, branch, exam_type, exam_month_year, total_marks, total_credits, sgpa, cgpa, date_of_issue, file_hash, original_file_path, status, confidence, university, college, course, medium, pass_status, aggregate, achievement, raw_extracted_fields) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
WHERE function_name = 'handle_save_ocr_data';

-- Add new fields mapping
INSERT INTO api_field_mappings (function_name, field_name, source_field, data_type) VALUES
('handle_save_ocr_data', 'university', 'University', 'string'),
('handle_save_ocr_data', 'college', 'College', 'string'),
('handle_save_ocr_data', 'course', 'Course', 'string'),
('handle_save_ocr_data', 'medium', 'Medium', 'string'),
('handle_save_ocr_data', 'pass_status', 'PassStatus', 'string'),
('handle_save_ocr_data', 'aggregate', 'Aggregate', 'string'),
('handle_save_ocr_data', 'achievement', 'Achievement', 'string'),
('handle_save_ocr_data', 'raw_extracted_fields', 'extracted_data', 'json');
