<?php
// Script to update the handle_save_ocr_data function in api.php

$api_file = 'api.php';
$updated_file = 'api_updated_final.php';

echo "Updating API function...\n";

// Read the current api.php file
$content = file_get_contents($api_file);
if ($content === false) {
    echo "✗ Could not read api.php file\n";
    exit(1);
}

// Make the necessary replacements
$replacements = [
    // Update the comment
    "Save OCR extracted data to certificates table with mapped fields" =>
    "Save OCR extracted data to ocr_saved_details table with mapped fields",

    // Update the INSERT statement
    "INSERT INTO certificates (institution_id, student_name, hall_ticket_no, certificate_no, branch, exam_type, exam_month_year, total_marks, total_credits, sgpa, cgpa, date_of_issue, file_hash, original_file_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)" =>
    "INSERT INTO ocr_saved_details (institution_id, student_name, hall_ticket_no, certificate_no, branch, exam_type, exam_month_year, total_marks, total_credits, sgpa, cgpa, date_of_issue, file_hash, original_file_path, status, confidence, university, college, course, medium, pass_status, aggregate, achievement, raw_extracted_fields) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",

    // Update the bind_param format string
    "'issssssiiiddssss'" =>
    "'issssssiiiddssssdsssssss'",

    // Add the new field mappings after the existing ones
    '$raw_extracted_fields = json_encode($extracted_data);' =>
    '$raw_extracted_fields = json_encode($extracted_data);

    // Additional fields for ocr_saved_details table
    $university = $extracted_data[\'University\'] ?? null;
    $college = $extracted_data[\'College\'] ?? null;
    $course = $extracted_data[\'Course\'] ?? null;
    $medium = $extracted_data[\'Medium\'] ?? null;
    $pass_status = $extracted_data[\'PassStatus\'] ?? null;
    $aggregate = $extracted_data[\'Aggregate\'] ?? null;
    $achievement = $extracted_data[\'Achievement\'] ?? null;',

    // Update the response message
    "respond_json(['ok' => true, 'id' => \$conn->insert_id]);" =>
    "respond_json(['ok' => true, 'id' => \$conn->insert_id, 'message' => 'Data saved to ocr_saved_details successfully']);"
];

// Apply replacements
foreach ($replacements as $old => $new) {
    $content = str_replace($old, $new, $content);
}

// Write the updated content to a new file
if (file_put_contents($updated_file, $content) !== false) {
    echo "✓ Updated API function written to $updated_file\n";

    // Verify the changes
    if (strpos($content, 'ocr_saved_details') !== false) {
        echo "✓ Table name updated to ocr_saved_details\n";
    } else {
        echo "✗ Table name not updated\n";
    }

    if (strpos($content, 'university, college, course') !== false) {
        echo "✓ New fields added to INSERT statement\n";
    } else {
        echo "✗ New fields not added\n";
    }

    if (strpos($content, 'raw_extracted_fields') !== false) {
        echo "✓ Raw extracted fields mapping added\n";
    } else {
        echo "✗ Raw extracted fields mapping not added\n";
    }

} else {
    echo "✗ Could not write updated API function\n";
}

echo "\nUpdate completed!\n";
?>
