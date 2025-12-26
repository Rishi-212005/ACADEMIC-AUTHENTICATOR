#!/usr/bin/env python3
"""
Certificate OCR Text Extractor
Extracts structured information from academic certificate OCR text with high accuracy
"""

import re
import json
import sys
from typing import Dict, Optional


def extract_fields(ocr_text: str) -> Dict[str, Optional[str]]:
    """
    Extract structured fields from OCR text using direct string manipulation
    
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
        "SGPA": None,
        "Result": None,
        "Grade": None,
        "Achievement": None,
        "SerialNo": None,
        "AggregateInWords": None
    }
    
    if not ocr_text or not isinstance(ocr_text, str):
        return fields
    
    # Clean and normalize text
    text = ocr_text.strip()
    upper_text = text.upper()
    
    # Split into lines for better processing
    lines = [line.strip() for line in text.split('\n') if line.strip()]
    
    # Debug: Print first few lines to understand the document format (uncomment for debugging)
    # print(f"DEBUG: Processing {len(lines)} lines of text", file=sys.stderr)
    # for i, line in enumerate(lines[:10]):  # Print first 10 lines for debugging
    #     print(f"DEBUG: Line {i+1}: {line}", file=sys.stderr)
    
    # Process each line to extract fields - one field per line to prevent mixing
    for line in lines:
        line_upper = line.upper()
        field_found = False
        
        # Student Name extraction - try multiple patterns
        if not field_found and not fields["Student Name"]:
            name_patterns = ["STUDENT NAME", "NAME", "CANDIDATE NAME", "STUDENT", "CANDIDATE"]
            for pattern in name_patterns:
                if pattern in line_upper:
                    value = extract_value_after_keyword(line, pattern)
                    if value:
                        name = clean_name(value)
                        if name and len(name) > 2:  # Ensure it's a meaningful name
                            fields["Student Name"] = name
                            field_found = True
                            break
        
        # Hall Ticket No extraction - try multiple patterns
        if not field_found and not fields["Hall Ticket No"]:
            hall_patterns = ["HALL TICKET", "HALL TICKET NO", "HALL TICKET NUMBER", "TICKET NO", "TICKET NUMBER"]
            for pattern in hall_patterns:
                if pattern in line_upper:
                    value = extract_value_after_keyword(line, pattern)
                    if value:
                        fields["Hall Ticket No"] = value.strip()
                        field_found = True
                        break
        
        # Roll Number extraction - try multiple patterns
        if not field_found and not fields["Roll Number"]:
            roll_patterns = ["ROLL NUMBER", "ROLL NO", "ROLL", "REGISTRATION NUMBER", "REG NO"]
            for pattern in roll_patterns:
                if pattern in line_upper:
                    value = extract_value_after_keyword(line, pattern)
                    if value:
                        fields["Roll Number"] = value.strip()
                        field_found = True
                        break
        
        # University Name extraction - try multiple patterns with more flexibility
        if not field_found and not fields["University Name"]:
            # First try to find any line containing "UNIVERSITY" without specific keywords
            if "UNIVERSITY" in line_upper and len(line) > 10 and ":" not in line:
                # Extract the entire line if it contains "UNIVERSITY" and looks like a university name
                # Only if it doesn't have a colon (which would indicate a field label)
                potential_uni = line.strip()
                # Clean it up - be more careful
                potential_uni = re.sub(r'^[^A-Za-z]*', '', potential_uni)  # Remove leading non-letters
                # Don't remove everything after first non-letter - university names can have numbers
                potential_uni = re.sub(r'\s+', ' ', potential_uni).strip()
                
                if len(potential_uni) > 10 and "UNIVERSITY" in potential_uni.upper():
                    # This is a full university name line, use it as-is
                    fields["University Name"] = potential_uni
                    # print(f"DEBUG: Found university name in primary extraction: {potential_uni}", file=sys.stderr)
                    field_found = True
            
            # If still not found, try specific patterns
            if not field_found:
                uni_patterns = ["UNIVERSITY NAME", "UNIVERSITY", "INSTITUTION", "INSTITUTE"]
                for pattern in uni_patterns:
                    if pattern in line_upper:
                        value = extract_value_after_keyword(line, pattern)
                        if value:
                            uni_name = clean_university_name(value)
                            if uni_name and len(uni_name) > 5:  # Ensure it's a meaningful university name
                                fields["University Name"] = uni_name
                                field_found = True
                                break
        
        # College Name extraction - try multiple patterns
        if not field_found and not fields["College Name"]:
            college_patterns = ["COLLEGE NAME", "COLLEGE", "SCHOOL", "INSTITUTION"]
            for pattern in college_patterns:
                if pattern in line_upper:
                    value = extract_value_after_keyword(line, pattern)
                    if value:
                        col_name = clean_college_name(value)
                        if col_name and len(col_name) > 5:  # Ensure it's a meaningful college name
                            fields["College Name"] = col_name
                                # print(f"DEBUG: Found college name in primary extraction: {col_name}", file=sys.stderr)
                            field_found = True
                            break
        
        # Branch extraction - try multiple patterns
        if not field_found and not fields["Branch"]:
            branch_patterns = ["BRANCH", "COURSE", "DEGREE", "PROGRAM", "STREAM", "DEPARTMENT"]
            for pattern in branch_patterns:
                if pattern in line_upper:
                    value = extract_value_after_keyword(line, pattern)
                    if value:
                        # For branch, we want to capture more text including "ENGINEERING"
                        # Don't remove "ENGINEERING" from the end
                        branch = value.strip()
                        # Only remove obvious non-branch words
                        branch = re.sub(r'\s+(MONTH|YEAR|EXAM)\s*$', '', branch, flags=re.IGNORECASE)
                        branch = re.sub(r'\s+', ' ', branch)  # Clean up spaces
                        branch = branch.strip()
                        if branch and len(branch) > 3:  # Ensure it's a meaningful branch name
                            fields["Branch"] = branch
                            field_found = True
                            break
        
        # CGPA extraction
        if not field_found and not fields["CGPA"] and "CGPA" in line_upper and "SCALE" not in line_upper:
            value = extract_value_after_keyword(line, "CGPA")
            if value:
                # Validate CGPA value
                try:
                    cgpa_float = float(value)
                    if 0 <= cgpa_float <= 10:
                        fields["CGPA"] = value.strip()
                        field_found = True
                except ValueError:
                    pass
        
        # CGPA (Scale 10) extraction
        if not field_found and not fields["CGPA (Scale 10)"] and "CGPA" in line_upper and "SCALE" in line_upper:
            value = extract_value_after_keyword(line, "CGPA")
            if value:
                # Validate CGPA value
                try:
                    cgpa_float = float(value)
                    if 0 <= cgpa_float <= 10:
                        fields["CGPA (Scale 10)"] = value.strip()
                        field_found = True
                except ValueError:
                    pass
        
        # SGPA extraction
        if not field_found and not fields["SGPA"] and "SGPA" in line_upper:
            value = extract_value_after_keyword(line, "SGPA")
            if value:
                # Validate SGPA value
                try:
                    sgpa_float = float(value)
                    if 0 <= sgpa_float <= 10:
                        fields["SGPA"] = value.strip()
                        field_found = True
                except ValueError:
                    pass
        
        # Result extraction (PASS/FAIL/COMPLETED)
        if not field_found and not fields["Result"]:
            result_patterns = ["RESULT", "STATUS", "PASS", "FAIL", "COMPLETED", "PROMOTED"]
            for pattern in result_patterns:
                if pattern in line_upper:
                    value = extract_value_after_keyword(line, pattern)
                    if value:
                        # Clean up result value
                        result = value.strip().upper()
                        if result in ["PASS", "FAIL", "COMPLETED", "PROMOTED", "PASSED", "FAILED"]:
                            fields["Result"] = result
                            field_found = True
                            break
        
        # Grade extraction (A+, A, B+, B, C+, C, D, F, etc.)
        if not field_found and not fields["Grade"]:
            grade_patterns = ["GRADE", "LETTER GRADE", "FINAL GRADE"]
            for pattern in grade_patterns:
                if pattern in line_upper:
                    value = extract_value_after_keyword(line, pattern)
                    if value:
                        # Clean up grade value
                        grade = value.strip().upper()
                        # Check if it looks like a valid grade
                        if re.match(r'^[A-F][+-]?$', grade) or grade in ["A+", "A", "A-", "B+", "B", "B-", "C+", "C", "C-", "D+", "D", "D-", "F"]:
                            fields["Grade"] = grade
                            field_found = True
                            break
        
        # Seena No extraction
        if not field_found and not fields["Seena No"] and "SEENA" in line_upper:
            value = extract_value_after_keyword(line, "SEENA")
            if value:
                fields["Seena No"] = value.strip()
                field_found = True
        
        # Examination Date extraction
        if not field_found and not fields["Examination Date"] and ("EXAMINATION DATE" in line_upper or ("MONTH" in line_upper and "YEAR" in line_upper)):
            if "EXAMINATION DATE" in line_upper:
                value = extract_value_after_keyword(line, "EXAMINATION DATE")
            else:
                value = extract_value_after_keyword(line, "MONTH")
            if value:
                fields["Examination Date"] = value.strip()
                field_found = True
        
        # Achievement extraction
        if not field_found and not fields["Achievement"]:
            achievement_keywords = ["FIRST CLASS WITH DISTINCTION", "FIRST CLASS", "SECOND CLASS", "THIRD CLASS", "DISTINCTION", "HONOURS", "MERIT"]
            for keyword in achievement_keywords:
                if keyword in line_upper:
                    fields["Achievement"] = keyword
                    field_found = True
                    break
        
        # Serial No extraction - pattern: 2 capital letters followed by numbers
        if not field_found and not fields["SerialNo"]:
            serial_patterns = ["SERIAL NO", "SERIAL NUMBER", "S.NO", "SERIAL", "SER NO", "SER NUMBER", "SERIMUNO", "SERIMUNO NO"]
            for pattern in serial_patterns:
                if pattern in line_upper:
                    value = extract_value_after_keyword(line, pattern)
                    if value:
                        # Clean and validate serial number format (2 letters + numbers)
                        serial = re.sub(r'[^A-Z0-9]', '', value.upper())
                        # Pattern: exactly 2 letters followed by numbers
                        if len(serial) >= 3 and re.match(r'^[A-Z]{2}[0-9]+$', serial):
                            fields["SerialNo"] = serial
                            field_found = True
                            break
        
        # Aggregate Marks extraction - pattern: *** marks in text***
        if not field_found and not fields["AggregateInWords"]:
            # Look for text between triple asterisks
            asterisk_match = re.search(r'\*{3,}([^*]+)\*{3,}', line)
            if asterisk_match:
                aggregate_text = asterisk_match.group(1).strip()
                if aggregate_text and len(aggregate_text) > 2:
                    fields["AggregateInWords"] = aggregate_text
                    field_found = True
    
    # Apply fallback detection for missing fields
    fields = apply_fallback_detection(fields, text, upper_text)
    
    # Debug: Print extracted fields (uncomment for debugging)
    # print(f"DEBUG: Extracted fields: {fields}", file=sys.stderr)
    
    return fields


def extract_value_after_keyword(line: str, keyword: str) -> Optional[str]:
    """Extract value after a keyword in a line with much more precise isolation"""
    upper_line = line.upper()
    start_pos = upper_line.find(keyword.upper())
    if start_pos == -1:
        return None
    
    # Find the colon or dash after the keyword
    colon_pos = line.find(":", start_pos)
    dash_pos = line.find("-", start_pos)
    if colon_pos != -1:
        value_start = colon_pos + 1
    elif dash_pos != -1:
        value_start = dash_pos + 1
    else:
        value_start = start_pos + len(keyword)
    
    # Extract the value - get everything after the keyword until end of line
    value = line[value_start:].strip()
    
    # Much more aggressive stopping at field boundaries
    field_boundaries = [
        "STUDENT NAME", "ROLL NUMBER", "HALL TICKET", "SEENA NO", "EXAMINATION DATE",
        "UNIVERSITY NAME", "COLLEGE NAME", "CGPA", "ACHIEVEMENT",
        "NAME:", "ROLL:", "HALL:", "UNIVERSITY:", "COLLEGE:", "BRANCH:", "COURSE:",
        "EXAMINATION", "MONTH", "YEAR", "DISTINCTION", "FIRST CLASS", "SECOND CLASS"
    ]
    
    # For certain keywords, be more specific about boundaries
    if keyword.upper() in ["COLLEGE NAME", "COLLEGE"]:
        # For college names, don't stop at "BRANCH" unless it's clearly a field label
        field_boundaries = [b for b in field_boundaries if b not in ["BRANCH", "BRANCH:"]]
        field_boundaries.extend(["BRANCH:", "COURSE:", "DEGREE:"])
    elif keyword.upper() in ["BRANCH", "COURSE", "DEGREE", "PROGRAM"]:
        # For branch names, don't stop at "COLLEGE" unless it's clearly a field label
        field_boundaries = [b for b in field_boundaries if b not in ["COLLEGE NAME", "COLLEGE:"]]
        field_boundaries.extend(["COLLEGE:", "UNIVERSITY:"])
    
    # Only look for boundaries that are actually in the current line, not in subsequent lines
    # This prevents cutting off text when the boundary appears in the next line
    if keyword.upper() in ["COLLEGE NAME", "COLLEGE", "BRANCH", "COURSE", "DEGREE", "PROGRAM"]:
        # Filter out boundaries that are not in the current line
        line_boundaries = []
        for boundary in field_boundaries:
            if boundary.upper() in upper_line:
                line_boundaries.append(boundary)
        field_boundaries = line_boundaries
    
    # Find the earliest occurrence of any field boundary
    earliest_stop = len(value)
    for boundary in field_boundaries:
        if boundary.upper() != keyword.upper():
            stop_pos = value.upper().find(boundary.upper())
            if stop_pos != -1 and stop_pos < earliest_stop:
                # Only stop if the boundary is actually a field label (followed by colon or at start of line)
                if (stop_pos == 0 or value[stop_pos-1] in [' ', '\n', '\t']) and (
                    stop_pos + len(boundary) >= len(value) or 
                    value[stop_pos + len(boundary)] in [':', ' ', '\n', '\t']
                ):
                    earliest_stop = stop_pos
    
    # Cut off at the field boundary
    if earliest_stop < len(value):
        # print(f"DEBUG: Cutting off at boundary for {keyword}: '{value[:earliest_stop]}' (was: '{value}')", file=sys.stderr)
        value = value[:earliest_stop].strip()
    
    # Field-specific validation and cleaning
    if keyword.upper() in ["STUDENT NAME", "NAME"]:
        # For names, only keep alphabetic characters and spaces
        value = re.sub(r'[^A-Za-z\s]', '', value)
        value = re.sub(r'\s+', ' ', value).strip()
        # Must be at least 2 words for a proper name
        if len(value.split()) < 2:
            return None
            
    elif keyword.upper() in ["ROLL NUMBER", "ROLL"]:
        # For roll numbers, only keep alphanumeric characters
        value = re.sub(r'[^A-Za-z0-9]', '', value)
        # Must be at least 3 characters and contain numbers
        if len(value) < 3 or not re.search(r'\d', value):
            return None
            
    elif keyword.upper() in ["HALL TICKET", "HALL TICKET NO"]:
        # For hall ticket, only keep alphanumeric characters
        value = re.sub(r'[^A-Za-z0-9]', '', value)
        # Must be at least 3 characters
        if len(value) < 3:
            return None
            
    elif keyword.upper() in ["UNIVERSITY NAME", "UNIVERSITY"]:
        # For university names, be very careful about what to remove
        # Only remove obvious non-university words at the very end
        value = re.sub(r'\s+(HALL|TICKET|NO|NUMBER|ROLL|NAME|MONTH|YEAR|EXAM|CGPA|ACHIEVEMENT|DISTINCTION|FIRST|SECOND|THIRD)\s*$', '', value, flags=re.IGNORECASE)
        value = re.sub(r'\s+', ' ', value).strip()
        # Must be at least 5 characters and contain letters
        if len(value) < 5 or not re.search(r'[A-Za-z]', value):
            return None
            
    elif keyword.upper() in ["COLLEGE NAME", "COLLEGE"]:
        # For college names, be very careful about what to remove
        # Only remove obvious non-college words at the very end
        value = re.sub(r'\s+(HALL|TICKET|NO|NUMBER|ROLL|NAME|MONTH|YEAR|EXAM|CGPA|ACHIEVEMENT|DISTINCTION|FIRST|SECOND|THIRD)\s*$', '', value, flags=re.IGNORECASE)
        value = re.sub(r'\s+', ' ', value).strip()
        # Must be at least 5 characters and contain letters
        if len(value) < 5 or not re.search(r'[A-Za-z]', value):
            return None
            
    elif keyword.upper() in ["BRANCH", "COURSE", "DEGREE", "PROGRAM"]:
        # For branch names, stop at obvious non-branch words
        value = re.sub(r'\s+(HALL|TICKET|NO|NUMBER|ROLL|UNIVERSITY|COLLEGE|NAME|MONTH|YEAR|EXAM|CGPA|ACHIEVEMENT|DISTINCTION|FIRST|SECOND|THIRD).*$', '', value, flags=re.IGNORECASE)
        value = re.sub(r'\s+', ' ', value).strip()
        # Must be at least 3 characters and contain letters
        if len(value) < 3 or not re.search(r'[A-Za-z]', value):
            return None
    
    # Final cleanup
    value = re.sub(r'\s+', ' ', value).strip()
    
    return value if value else None


def clean_name(name: str) -> str:
    """Clean student name by removing common OCR artifacts"""
    if not name:
        return ""
    
    # Remove trailing words that are likely not part of the name
    name = re.sub(r'\s+(HALL|TICKET|NO|NUMBER|ROLL|UNIVERSITY|COLLEGE|NAME|BRANCH|MONTH|YEAR|EXAM)\s*$', '', name, flags=re.IGNORECASE)
    name = re.sub(r'\s+[a-z]{1,2}\s*$', '', name)  # Remove trailing 1-2 letter artifacts
    name = re.sub(r'\s+', ' ', name)  # Clean up spaces
    name = name.strip()
    
    # Additional cleaning for common OCR errors
    name = re.sub(r'\s+ea\s*$', '', name, flags=re.IGNORECASE)
    name = re.sub(r'\s+[a-z]{1,2}\s+', ' ', name, flags=re.IGNORECASE)
    
    return name


def clean_university_name(uni_name: str) -> str:
    """Clean university name by removing common OCR artifacts"""
    if not uni_name:
        return ""
    
    # Remove trailing words that are likely not part of the university name
    uni_name = re.sub(r'\s+(HALL|TICKET|NO|NUMBER|ROLL|COLLEGE|NAME|BRANCH|MONTH|YEAR|EXAM|CGPA|ACHIEVEMENT)\s*$', '', uni_name, flags=re.IGNORECASE)
    uni_name = re.sub(r'\s+', ' ', uni_name)  # Clean up spaces
    uni_name = uni_name.strip()
    
    # Additional cleaning for common OCR errors - but be more careful
    uni_name = re.sub(r'\s+[a-z]{1,2}\s*$', '', uni_name, flags=re.IGNORECASE)
    
    # Don't truncate university names - they should be long
    # Only remove obvious artifacts, not legitimate parts of the name
    
    return uni_name


def clean_college_name(col_name: str) -> str:
    """Clean college name by removing common OCR artifacts"""
    if not col_name:
        return ""
    
    # Remove trailing words that are likely not part of the college name
    col_name = re.sub(r'\s+(HALL|TICKET|NO|NUMBER|ROLL|UNIVERSITY|NAME|MONTH|YEAR|EXAM)\s*$', '', col_name, flags=re.IGNORECASE)
    # Don't remove "COLLEGE" or "BRANCH" from the end as they might be part of the college name
    col_name = re.sub(r'\s+', ' ', col_name)  # Clean up spaces
    col_name = col_name.strip()
    
    # Additional cleaning for common OCR errors
    col_name = re.sub(r'\s+[a-z]{1,2}\s*$', '', col_name, flags=re.IGNORECASE)
    
    return col_name


def clean_branch_name(branch: str) -> str:
    """Clean branch name by removing common OCR artifacts"""
    if not branch:
        return ""
    
    # Remove trailing words that are likely not part of the branch name
    branch = re.sub(r'\s+(HALL|TICKET|NO|NUMBER|ROLL|UNIVERSITY|COLLEGE|NAME|BRANCH|MONTH|YEAR|EXAM)\s*$', '', branch, flags=re.IGNORECASE)
    branch = re.sub(r'\s+[A-Z]{2,}\s*$', '', branch)  # Remove trailing words
    branch = re.sub(r'\s+', ' ', branch)  # Clean up spaces
    branch = branch.strip()
    
    # Additional cleaning for common OCR errors
    branch = re.sub(r'\s+[a-z]{1,2}\s*$', '', branch, flags=re.IGNORECASE)
    
    return branch


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
    
    # Fallback for SGPA - look for decimal pattern
    if not fields["SGPA"]:
        sgpa_match = re.search(r'\bSGPA[:\s]*([0-9]+\.[0-9]{1,2})\b', upper_text)
        if sgpa_match:
            sgpa_value = float(sgpa_match.group(1))
            if 0 <= sgpa_value <= 10:
                fields["SGPA"] = sgpa_match.group(1)
    
    # Fallback for Result - look for PASS/FAIL patterns
    if not fields["Result"]:
        result_patterns = [
            r'\b(PASS|PASSED|FAIL|FAILED|COMPLETED|PROMOTED)\b',
            r'\bRESULT[:\s]*(PASS|PASSED|FAIL|FAILED|COMPLETED|PROMOTED)\b',
            r'\bSTATUS[:\s]*(PASS|PASSED|FAIL|FAILED|COMPLETED|PROMOTED)\b'
        ]
        for pattern in result_patterns:
            result_match = re.search(pattern, upper_text)
            if result_match:
                fields["Result"] = result_match.group(1)
                break
    
    # Fallback for Grade - look for letter grade patterns
    if not fields["Grade"]:
        grade_patterns = [
            r'\bGRADE[:\s]*([A-F][+-]?)\b',
            r'\b([A-F][+-]?)\s*GRADE\b',
            r'\bFINAL[:\s]*GRADE[:\s]*([A-F][+-]?)\b'
        ]
        for pattern in grade_patterns:
            grade_match = re.search(pattern, upper_text)
            if grade_match:
                grade = grade_match.group(1).upper()
                if re.match(r'^[A-F][+-]?$', grade):
                    fields["Grade"] = grade
                    break
    
    # Fallback for Examination Date - look for month year pattern
    if not fields["Examination Date"]:
        date_match = re.search(r'(JANUARY|FEBRUARY|MARCH|APRIL|MAY|JUNE|JULY|AUGUST|SEPTEMBER|OCTOBER|NOVEMBER|DECEMBER)\s+\d{4}', upper_text)
        if date_match:
            fields["Examination Date"] = date_match.group(0)
    
    # Fallback for University Name - look for lines containing "UNIVERSITY" with multiple patterns
    if not fields["University Name"]:
        # Try multiple patterns to find university name - prioritize longer matches
        uni_patterns = [
            r'([A-Z\s]*JAWAHARLAL[A-Z\s]*UNIVERSITY[A-Z\s]*)',  # Most specific first
            r'([A-Z\s]*NEHRU[A-Z\s]*UNIVERSITY[A-Z\s]*)',
            r'([A-Z\s]*TECHNOLOGICAL[A-Z\s]*UNIVERSITY[A-Z\s]*)',
            r'([A-Z\s]*UNIVERSITY[A-Z\s]*)',
            r'([A-Z\s]*INSTITUTE[A-Z\s]*)'
        ]
        
        for pattern in uni_patterns:
            uni_match = re.search(pattern, upper_text)
            if uni_match:
                uni_name = uni_match.group(1).strip()
                # Clean up university name
                uni_name = clean_university_name(uni_name)
                if uni_name and len(uni_name) > 5:
                    # For university names, prefer longer, more complete names
                    if not fields["University Name"] or len(uni_name) > len(fields["University Name"]):
                        fields["University Name"] = uni_name
                        break  # Use the first good match, don't override with shorter ones
    
    # Fallback for College Name - look for lines containing "COLLEGE" or institution names
    if not fields["College Name"]:
        # Try to find college name patterns
        college_patterns = [
            r'([A-Z\s]*COLLEGE[A-Z\s]*)',
            r'([A-Z\s]*INSTITUTE[A-Z\s]*)',
            r'([A-Z\s]*SCHOOL[A-Z\s]*)',
            r'([A-Z\s]*TECHNOLOGICAL[A-Z\s]*)'
        ]
        
        for pattern in college_patterns:
            col_match = re.search(pattern, upper_text)
            if col_match:
                col_name = col_match.group(1).strip()
                # Clean up college name
                col_name = clean_college_name(col_name)
                if col_name and len(col_name) > 5 and re.search(r'[A-Za-z]', col_name):
                    # For college names, prefer longer, more complete names
                    if not fields["College Name"] or len(col_name) > len(fields["College Name"]):
                        fields["College Name"] = col_name
                        break  # Use the first good match, don't override with shorter ones
    
    # Fallback for Student Name - look for name patterns
    if not fields["Student Name"]:
        name_match = re.search(r'^([A-Z][A-Z\s\.\']{2,50})', text)
        if name_match:
            potential_name = name_match.group(1).strip()
            # Check if it looks like a name
            if len(potential_name) <= 50 and re.search(r'[A-Za-z]', potential_name):
                # Clean up name
                potential_name = clean_name(potential_name)
                if potential_name:
                    fields["Student Name"] = potential_name
    
    # Fallback for Branch - look for branch patterns
    if not fields["Branch"]:
        # Try multiple patterns for branch detection
        branch_patterns = [
            r'([A-Z\s]*SCIENCE\s+AND\s+ENGINEERING[A-Z\s]*)',
            r'([A-Z\s]*SCIENCE\s+AND\s+[A-Z\s]*)',
            r'([A-Z\s]*ENGINEERING[A-Z\s]*)',
            r'([A-Z\s]*SCIENCE[A-Z\s]*)'
        ]
        
        for pattern in branch_patterns:
            branch_match = re.search(pattern, upper_text)
            if branch_match:
                branch = branch_match.group(1).strip()
                # Clean up branch name
                branch = clean_branch_name(branch)
                if branch and len(branch) > 5:  # Ensure it's a meaningful branch name
                    fields["Branch"] = branch
                    break
    
    # Fallback for Serial No - look for pattern anywhere in text
    if not fields["SerialNo"]:
        # Try multiple patterns for serial numbers, prioritizing 2 letters + numbers
        serial_patterns = [
            r'\b([A-Z]{2}[0-9]+)\b',  # Exactly 2 letters followed by numbers
            r'\b([A-Z]{2}[0-9]{4,})\b',  # Exactly 2 letters followed by 4+ numbers
            r'\b([A-Z]{2}[0-9]{2,})\b',  # Exactly 2 letters followed by 2+ numbers
            r'\b([A-Z]{1,2}[0-9]{4,})\b',  # 1-2 letters followed by 4+ numbers
            r'\b([A-Z0-9]{6,})\b'  # Any alphanumeric sequence of 6+ characters
        ]
        for pattern in serial_patterns:
            serial_match = re.search(pattern, upper_text)
            if serial_match:
                potential_serial = serial_match.group(1)
                # Validate it looks like a serial number
                if len(potential_serial) >= 3 and re.search(r'[A-Z]', potential_serial) and re.search(r'[0-9]', potential_serial):
                    fields["SerialNo"] = potential_serial
                    break
    
    # Additional aggressive search for serial numbers - look for any 2-letter + number pattern
    if not fields["SerialNo"]:
        # Search for any pattern that looks like 2 letters followed by numbers
        aggressive_patterns = [
            r'([A-Z]{2}[0-9]{2,})',  # 2 letters + 2+ numbers (no word boundaries)
            r'([A-Z]{2}[0-9]+)',     # 2 letters + any numbers (no word boundaries)
        ]
        for pattern in aggressive_patterns:
            matches = re.findall(pattern, upper_text)
            for match in matches:
                if len(match) >= 4:  # At least 4 characters total
                    fields["SerialNo"] = match
                    break
            if fields["SerialNo"]:
                break
    
    # Fallback for Aggregate Marks - look for text between asterisks anywhere in text
    if not fields["AggregateInWords"]:
        aggregate_match = re.search(r'\*{3,}([^*]+)\*{3,}', text)
        if aggregate_match:
            aggregate_text = aggregate_match.group(1).strip()
            if aggregate_text and len(aggregate_text) > 2:
                fields["AggregateInWords"] = aggregate_text
    
    return fields


def main():
    """Main function that handles command line arguments or runs test"""
    import sys
    import argparse
    
    parser = argparse.ArgumentParser(description='Extract structured fields from OCR text')
    parser.add_argument('--text', help='Path to text file containing OCR text')
    parser.add_argument('--stdin', action='store_true', help='Read OCR text from stdin')
    
    args = parser.parse_args()
    
    if args.text:
        # Read from file
        try:
            with open(args.text, 'r', encoding='utf-8') as f:
                ocr_text = f.read()
        except Exception as e:
            print(json.dumps({'error': f'Failed to read file: {str(e)}'}), file=sys.stderr)
            sys.exit(1)
    elif args.stdin:
        # Read from stdin
        ocr_text = sys.stdin.read()
    else:
        # No input provided - show usage
        print(json.dumps({'error': 'No input provided. Use --text <file> or --stdin'}), file=sys.stderr)
        sys.exit(1)
    
    # Extract fields
    result = extract_fields(ocr_text)
    
    # Output as JSON
    print(json.dumps(result, ensure_ascii=False))


if __name__ == "__main__":
    main()
