#!/usr/bin/env python3
"""
Simple and Effective OCR Text Extractor for Academic Certificates
Focuses on accurate field extraction with better pattern matching
"""

import re
import json
from typing import Dict, Optional


def extract_fields(ocr_text: str) -> Dict[str, Optional[str]]:
    """
    Extract structured fields from OCR text with improved accuracy
    
    Args:
        ocr_text (str): Raw OCR text from certificate
        
    Returns:
        Dict[str, Optional[str]]: Dictionary with extracted field values
    """
    
    # Initialize result dictionary
    fields = {
        "Student Name": None,
        "Roll Number": None,
        "Hall Ticket No": None,
        "Seena No": None,
        "Examination Date": None,
        "University Name": None,
        "College Name": None,
        "Branch": None,
        "CGPA": None,
        "CGPA (Scale 10)": None,
        "Achievement": None
    }
    
    if not ocr_text or not isinstance(ocr_text, str):
        return fields
    
    # Clean and normalize text
    text = ocr_text.strip()
    upper_text = text.upper()
    
    # Split into lines for better processing
    lines = [line.strip() for line in text.split('\n') if line.strip()]
    
    # Process each line to extract fields
    for line in lines:
        line_upper = line.upper()
        
        # Student Name extraction
        if not fields["Student Name"]:
            # Pattern: STUDENT NAME: [NAME]
            match = re.search(r'STUDENT\s*NAME[:\-]?\s*([A-Z][A-Z\s\.\']{2,50})', line_upper)
            if match:
                name = match.group(1).strip()
                # Clean up the name - remove common artifacts
                name = re.sub(r'\s+[A-Z]{2,}\s*$', '', name)  # Remove trailing words like "HALL"
                name = re.sub(r'\s+[a-z]{1,2}\s*$', '', name)  # Remove trailing 1-2 letter artifacts
                fields["Student Name"] = name.strip()
        
        # Hall Ticket No extraction
        if not fields["Hall Ticket No"]:
            # Pattern: HALL TICKET NO: [NUMBER]
            match = re.search(r'HALL\s*TICKET\s*(?:NO|NUMBER)[:\-]?\s*([A-Z0-9]{5,15})', line_upper)
            if match:
                fields["Hall Ticket No"] = match.group(1).strip()
        
        # Roll Number extraction
        if not fields["Roll Number"]:
            # Pattern: ROLL NUMBER: [NUMBER]
            match = re.search(r'ROLL\s*(?:NO|NUMBER)[:\-]?\s*([A-Z0-9]{5,15})', line_upper)
            if match:
                fields["Roll Number"] = match.group(1).strip()
        
        # University Name extraction
        if not fields["University Name"]:
            # Pattern: UNIVERSITY NAME: [NAME]
            match = re.search(r'UNIVERSITY\s*NAME[:\-]?\s*([A-Z\s]+?)(?=\s+[A-Z]{2,}|$)', line_upper)
            if match:
                uni_name = match.group(1).strip()
                # Clean up university name
                uni_name = re.sub(r'\s+[A-Z]{2,}\s*$', '', uni_name)  # Remove trailing words
                fields["University Name"] = uni_name.strip()
        
        # College Name extraction
        if not fields["College Name"]:
            # Pattern: COLLEGE NAME: [NAME]
            match = re.search(r'COLLEGE\s*NAME[:\-]?\s*([A-Z\s]+?)(?=\s+[A-Z]{2,}|$)', line_upper)
            if match:
                col_name = match.group(1).strip()
                # Clean up college name
                col_name = re.sub(r'\s+[A-Z]{2,}\s*$', '', col_name)  # Remove trailing words
                fields["College Name"] = col_name.strip()
        
        # Branch extraction
        if not fields["Branch"]:
            # Pattern: BRANCH: [BRANCH NAME]
            match = re.search(r'BRANCH[:\-]?\s*([A-Z\s&/]+?)(?=\s+[A-Z]{2,}|$)', line_upper)
            if match:
                branch = match.group(1).strip()
                # Clean up branch name
                branch = re.sub(r'\s+[A-Z]{2,}\s*$', '', branch)  # Remove trailing words
                fields["Branch"] = branch.strip()
        
        # CGPA extraction
        if not fields["CGPA"]:
            # Pattern: CGPA: [VALUE]
            match = re.search(r'CGPA[:\-]?\s*([0-9]+\.?[0-9]*)', line_upper)
            if match:
                cgpa_value = match.group(1).strip()
                # Validate CGPA value
                try:
                    cgpa_float = float(cgpa_value)
                    if 0 <= cgpa_float <= 10:
                        fields["CGPA"] = cgpa_value
                except ValueError:
                    pass
        
        # CGPA (Scale 10) extraction
        if not fields["CGPA (Scale 10)"]:
            # Pattern: CGPA (Scale 10): [VALUE]
            match = re.search(r'CGPA\s*\(SCALE\s*10\)[:\-]?\s*([0-9]+\.?[0-9]*)', line_upper)
            if match:
                cgpa_value = match.group(1).strip()
                # Validate CGPA value
                try:
                    cgpa_float = float(cgpa_value)
                    if 0 <= cgpa_float <= 10:
                        fields["CGPA (Scale 10)"] = cgpa_value
                except ValueError:
                    pass
        
        # Seena No extraction
        if not fields["Seena No"]:
            # Pattern: SEENA NO: [NUMBER]
            match = re.search(r'SEENA\s*(?:NO|NUMBER)[:\-]?\s*([A-Z0-9]{5,15})', line_upper)
            if match:
                fields["Seena No"] = match.group(1).strip()
        
        # Examination Date extraction
        if not fields["Examination Date"]:
            # Pattern: EXAMINATION DATE: [DATE] or MONTH YEAR OF EXAM: [DATE]
            match = re.search(r'(?:EXAMINATION\s*DATE|MONTH\s*[&]?\s*YEAR\s*OF\s*EXAM)[:\-]?\s*([A-Z]+\s+\d{4})', line_upper)
            if match:
                fields["Examination Date"] = match.group(1).strip()
        
        # Achievement extraction
        if not fields["Achievement"]:
            # Pattern: Look for achievement words
            achievement_match = re.search(r'(FIRST\s+CLASS\s+WITH\s+DISTINCTION|FIRST\s+CLASS|SECOND\s+CLASS|THIRD\s+CLASS|DISTINCTION|HONOURS|MERIT)', line_upper)
            if achievement_match:
                fields["Achievement"] = achievement_match.group(1).strip()
    
    # Apply fallback detection for missing fields
    fields = apply_fallback_detection(fields, text, upper_text)
    
    # Clean up extracted values
    fields = clean_extracted_values(fields)
    
    return fields


def apply_fallback_detection(fields: Dict[str, Optional[str]], 
                           text: str, upper_text: str) -> Dict[str, Optional[str]]:
    """Apply fallback detection methods for missing fields"""
    
    # Fallback for Hall Ticket No - look for 10-character alphanumeric pattern
    if not fields["Hall Ticket No"]:
        ht_match = re.search(r'\b(\d{5}[A-Z0-9]\d{4})\b', upper_text)
        if ht_match:
            fields["Hall Ticket No"] = ht_match.group(1)
    
    # Fallback for Roll Number - look for 10-character alphanumeric pattern
    if not fields["Roll Number"]:
        roll_match = re.search(r'\b(\d{5}[A-Z0-9]\d{4})\b', upper_text)
        if roll_match:
            fields["Roll Number"] = roll_match.group(1)
    
    # Fallback for CGPA - look for decimal pattern
    if not fields["CGPA"]:
        cgpa_match = re.search(r'\b([0-9]+\.[0-9]{2})\b', text)
        if cgpa_match:
            cgpa_value = float(cgpa_match.group(1))
            if 0 <= cgpa_value <= 10:
                fields["CGPA"] = cgpa_match.group(1)
    
    # Fallback for Examination Date - look for month year pattern
    if not fields["Examination Date"]:
        date_match = re.search(r'(JANUARY|FEBRUARY|MARCH|APRIL|MAY|JUNE|JULY|AUGUST|SEPTEMBER|OCTOBER|NOVEMBER|DECEMBER)\s+\d{4}', upper_text)
        if date_match:
            fields["Examination Date"] = date_match.group(0)
    
    # Fallback for University Name - look for lines containing "UNIVERSITY"
    if not fields["University Name"]:
        uni_match = re.search(r'([A-Z\s]*UNIVERSITY[A-Z\s]*)', upper_text)
        if uni_match:
            uni_name = uni_match.group(1).strip()
            # Clean up university name
            uni_name = re.sub(r'\s+[A-Z]{2,}\s*$', '', uni_name)
            fields["University Name"] = uni_name.strip()
    
    # Fallback for College Name - look for lines containing "COLLEGE"
    if not fields["College Name"]:
        col_match = re.search(r'([A-Z\s]*COLLEGE[A-Z\s]*)', upper_text)
        if col_match:
            col_name = col_match.group(1).strip()
            # Clean up college name
            col_name = re.sub(r'\s+[A-Z]{2,}\s*$', '', col_name)
            fields["College Name"] = col_name.strip()
    
    # Fallback for Student Name - look for name patterns
    if not fields["Student Name"]:
        name_match = re.search(r'^([A-Z][A-Z\s\.\']{2,50})', text)
        if name_match:
            potential_name = name_match.group(1).strip()
            # Check if it looks like a name
            if len(potential_name) <= 50 and re.search(r'[A-Za-z]', potential_name):
                # Clean up name
                potential_name = re.sub(r'\s+[A-Z]{2,}\s*$', '', potential_name)
                potential_name = re.sub(r'\s+[a-z]{1,2}\s*$', '', potential_name)
                fields["Student Name"] = potential_name.strip()
    
    # Fallback for Branch - look for branch patterns
    if not fields["Branch"]:
        branch_match = re.search(r'([A-Z\s]*SCIENCE\s+AND\s+ENGINEERING[A-Z\s]*)', upper_text)
        if branch_match:
            branch = branch_match.group(1).strip()
            # Clean up branch name
            branch = re.sub(r'\s+[A-Z]{2,}\s*$', '', branch)
            fields["Branch"] = branch.strip()
    
    return fields


def clean_extracted_values(fields: Dict[str, Optional[str]]) -> Dict[str, Optional[str]]:
    """Clean and validate extracted field values"""
    
    # Clean Student Name
    if fields["Student Name"]:
        name = fields["Student Name"]
        # Remove common OCR artifacts
        name = re.sub(r'\s+[a-z]{1,2}\s*$', '', name, flags=re.IGNORECASE)
        name = re.sub(r'\s+[A-Z]{2,}\s*$', '', name)  # Remove trailing words
        name = re.sub(r'\s+', ' ', name)  # Clean up spaces
        fields["Student Name"] = name.strip()
    
    # Clean University Name
    if fields["University Name"]:
        uni = fields["University Name"]
        uni = re.sub(r'\s+[A-Z]{2,}\s*$', '', uni)  # Remove trailing words
        uni = re.sub(r'\s+', ' ', uni)  # Clean up spaces
        fields["University Name"] = uni.strip()
    
    # Clean College Name
    if fields["College Name"]:
        col = fields["College Name"]
        col = re.sub(r'\s+[A-Z]{2,}\s*$', '', col)  # Remove trailing words
        col = re.sub(r'\s+', ' ', col)  # Clean up spaces
        fields["College Name"] = col.strip()
    
    # Clean Branch
    if fields["Branch"]:
        branch = fields["Branch"]
        branch = re.sub(r'\s+[A-Z]{2,}\s*$', '', branch)  # Remove trailing words
        branch = re.sub(r'\s+', ' ', branch)  # Clean up spaces
        fields["Branch"] = branch.strip()
    
    return fields


def main():
    """Test the extractor with sample data"""
    # Sample OCR text for testing
    sample_ocr_text = """
    STUDENT NAME: BATHULA CHIRANJEEVI aaie
    HALL TICKET NO: JAWAHARLAL
    ROLL NUMBER: JAWAHARLAL
    UNIVERSITY NAME: JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR COLLEGE ENGINEERING
    COLLEGE NAME: JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR COLLEGE ENGINEERING
    BRANCH: COMPUTER SCIENCE AND ENGINEERING MONTH YEAR EXAM
    """
    
    # Extract fields
    result = extract_fields(sample_ocr_text)
    
    # Print results
    print("Extracted Fields:")
    print("=" * 50)
    for field, value in result.items():
        print(f"{field}: {value}")
    
    # Print as JSON
    print("\nJSON Output:")
    print("=" * 50)
    print(json.dumps(result, indent=2, ensure_ascii=False))


if __name__ == "__main__":
    main()