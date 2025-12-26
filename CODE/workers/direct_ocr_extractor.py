#!/usr/bin/env python3
"""
Direct OCR Text Extractor for Academic Certificates
Uses direct string manipulation for maximum accuracy
"""

import re
import json
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
            if "STUDENT NAME" in line_upper:
                # Find the position of "STUDENT NAME"
                start_pos = line_upper.find("STUDENT NAME")
                if start_pos != -1:
                    # Find the colon or dash after "STUDENT NAME"
                    colon_pos = line.find(":", start_pos)
                    dash_pos = line.find("-", start_pos)
                    if colon_pos != -1:
                        value_start = colon_pos + 1
                    elif dash_pos != -1:
                        value_start = dash_pos + 1
                    else:
                        value_start = start_pos + len("STUDENT NAME")
                    
                    # Extract the value
                    value = line[value_start:].strip()
                    if value:
                        # Clean up the name
                        name = clean_name(value)
                        if name:
                            fields["Student Name"] = name
        
        # Hall Ticket No extraction
        if not fields["Hall Ticket No"]:
            if "HALL TICKET" in line_upper:
                # Find the position of "HALL TICKET"
                start_pos = line_upper.find("HALL TICKET")
                if start_pos != -1:
                    # Find the colon or dash after "HALL TICKET"
                    colon_pos = line.find(":", start_pos)
                    dash_pos = line.find("-", start_pos)
                    if colon_pos != -1:
                        value_start = colon_pos + 1
                    elif dash_pos != -1:
                        value_start = dash_pos + 1
                    else:
                        value_start = start_pos + len("HALL TICKET")
                    
                    # Extract the value
                    value = line[value_start:].strip()
                    if value:
                        fields["Hall Ticket No"] = value
        
        # Roll Number extraction
        if not fields["Roll Number"]:
            if "ROLL NUMBER" in line_upper:
                # Find the position of "ROLL NUMBER"
                start_pos = line_upper.find("ROLL NUMBER")
                if start_pos != -1:
                    # Find the colon or dash after "ROLL NUMBER"
                    colon_pos = line.find(":", start_pos)
                    dash_pos = line.find("-", start_pos)
                    if colon_pos != -1:
                        value_start = colon_pos + 1
                    elif dash_pos != -1:
                        value_start = dash_pos + 1
                    else:
                        value_start = start_pos + len("ROLL NUMBER")
                    
                    # Extract the value
                    value = line[value_start:].strip()
                    if value:
                        fields["Roll Number"] = value
        
        # University Name extraction
        if not fields["University Name"]:
            if "UNIVERSITY NAME" in line_upper:
                # Find the position of "UNIVERSITY NAME"
                start_pos = line_upper.find("UNIVERSITY NAME")
                if start_pos != -1:
                    # Find the colon or dash after "UNIVERSITY NAME"
                    colon_pos = line.find(":", start_pos)
                    dash_pos = line.find("-", start_pos)
                    if colon_pos != -1:
                        value_start = colon_pos + 1
                    elif dash_pos != -1:
                        value_start = dash_pos + 1
                    else:
                        value_start = start_pos + len("UNIVERSITY NAME")
                    
                    # Extract the value
                    value = line[value_start:].strip()
                    if value:
                        # Clean up university name
                        uni_name = clean_university_name(value)
                        if uni_name:
                            fields["University Name"] = uni_name
        
        # College Name extraction
        if not fields["College Name"]:
            if "COLLEGE NAME" in line_upper:
                # Find the position of "COLLEGE NAME"
                start_pos = line_upper.find("COLLEGE NAME")
                if start_pos != -1:
                    # Find the colon or dash after "COLLEGE NAME"
                    colon_pos = line.find(":", start_pos)
                    dash_pos = line.find("-", start_pos)
                    if colon_pos != -1:
                        value_start = colon_pos + 1
                    elif dash_pos != -1:
                        value_start = dash_pos + 1
                    else:
                        value_start = start_pos + len("COLLEGE NAME")
                    
                    # Extract the value
                    value = line[value_start:].strip()
                    if value:
                        # Clean up college name
                        col_name = clean_college_name(value)
                        if col_name:
                            fields["College Name"] = col_name
        
        # Branch extraction
        if not fields["Branch"]:
            if "BRANCH" in line_upper:
                # Find the position of "BRANCH"
                start_pos = line_upper.find("BRANCH")
                if start_pos != -1:
                    # Find the colon or dash after "BRANCH"
                    colon_pos = line.find(":", start_pos)
                    dash_pos = line.find("-", start_pos)
                    if colon_pos != -1:
                        value_start = colon_pos + 1
                    elif dash_pos != -1:
                        value_start = dash_pos + 1
                    else:
                        value_start = start_pos + len("BRANCH")
                    
                    # Extract the value
                    value = line[value_start:].strip()
                    if value:
                        # Clean up branch name
                        branch = clean_branch_name(value)
                        if branch:
                            fields["Branch"] = branch
        
        # CGPA extraction
        if not fields["CGPA"]:
            if "CGPA" in line_upper and "SCALE" not in line_upper:
                # Find the position of "CGPA"
                start_pos = line_upper.find("CGPA")
                if start_pos != -1:
                    # Find the colon or dash after "CGPA"
                    colon_pos = line.find(":", start_pos)
                    dash_pos = line.find("-", start_pos)
                    if colon_pos != -1:
                        value_start = colon_pos + 1
                    elif dash_pos != -1:
                        value_start = dash_pos + 1
                    else:
                        value_start = start_pos + len("CGPA")
                    
                    # Extract the value
                    value = line[value_start:].strip()
                    if value:
                        # Validate CGPA value
                        try:
                            cgpa_float = float(value)
                            if 0 <= cgpa_float <= 10:
                                fields["CGPA"] = value
                        except ValueError:
                            pass
        
        # CGPA (Scale 10) extraction
        if not fields["CGPA (Scale 10)"]:
            if "CGPA" in line_upper and "SCALE" in line_upper:
                # Find the position of "CGPA"
                start_pos = line_upper.find("CGPA")
                if start_pos != -1:
                    # Find the colon or dash after "CGPA"
                    colon_pos = line.find(":", start_pos)
                    dash_pos = line.find("-", start_pos)
                    if colon_pos != -1:
                        value_start = colon_pos + 1
                    elif dash_pos != -1:
                        value_start = dash_pos + 1
                    else:
                        value_start = start_pos + len("CGPA")
                    
                    # Extract the value
                    value = line[value_start:].strip()
                    if value:
                        # Validate CGPA value
                        try:
                            cgpa_float = float(value)
                            if 0 <= cgpa_float <= 10:
                                fields["CGPA (Scale 10)"] = value
                        except ValueError:
                            pass
        
        # Seena No extraction
        if not fields["Seena No"]:
            if "SEENA" in line_upper:
                # Find the position of "SEENA"
                start_pos = line_upper.find("SEENA")
                if start_pos != -1:
                    # Find the colon or dash after "SEENA"
                    colon_pos = line.find(":", start_pos)
                    dash_pos = line.find("-", start_pos)
                    if colon_pos != -1:
                        value_start = colon_pos + 1
                    elif dash_pos != -1:
                        value_start = dash_pos + 1
                    else:
                        value_start = start_pos + len("SEENA")
                    
                    # Extract the value
                    value = line[value_start:].strip()
                    if value:
                        fields["Seena No"] = value
        
        # Examination Date extraction
        if not fields["Examination Date"]:
            if "EXAMINATION DATE" in line_upper or "MONTH" in line_upper and "YEAR" in line_upper:
                # Find the position of the date keyword
                start_pos = line_upper.find("EXAMINATION DATE")
                if start_pos == -1:
                    start_pos = line_upper.find("MONTH")
                
                if start_pos != -1:
                    # Find the colon or dash after the keyword
                    colon_pos = line.find(":", start_pos)
                    dash_pos = line.find("-", start_pos)
                    if colon_pos != -1:
                        value_start = colon_pos + 1
                    elif dash_pos != -1:
                        value_start = dash_pos + 1
                    else:
                        value_start = start_pos + len("EXAMINATION DATE")
                    
                    # Extract the value
                    value = line[value_start:].strip()
                    if value:
                        fields["Examination Date"] = value
        
        # Achievement extraction
        if not fields["Achievement"]:
            achievement_keywords = ["FIRST CLASS WITH DISTINCTION", "FIRST CLASS", "SECOND CLASS", "THIRD CLASS", "DISTINCTION", "HONOURS", "MERIT"]
            for keyword in achievement_keywords:
                if keyword in line_upper:
                    fields["Achievement"] = keyword
                    break
    
    # Apply fallback detection for missing fields
    fields = apply_fallback_detection(fields, text, upper_text)
    
    return fields


def clean_name(name: str) -> str:
    """Clean student name by removing common OCR artifacts"""
    if not name:
        return ""
    
    # Remove trailing words that are likely not part of the name
    name = re.sub(r'\s+[A-Z]{2,}\s*$', '', name)  # Remove trailing words like "HALL"
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
    uni_name = re.sub(r'\s+[A-Z]{2,}\s*$', '', uni_name)  # Remove trailing words
    uni_name = re.sub(r'\s+', ' ', uni_name)  # Clean up spaces
    uni_name = uni_name.strip()
    
    # Additional cleaning for common OCR errors
    uni_name = re.sub(r'\s+[a-z]{1,2}\s*$', '', uni_name, flags=re.IGNORECASE)
    
    return uni_name


def clean_college_name(col_name: str) -> str:
    """Clean college name by removing common OCR artifacts"""
    if not col_name:
        return ""
    
    # Remove trailing words that are likely not part of the college name
    col_name = re.sub(r'\s+[A-Z]{2,}\s*$', '', col_name)  # Remove trailing words
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
            uni_name = clean_university_name(uni_name)
            if uni_name:
                fields["University Name"] = uni_name
    
    # Fallback for College Name - look for lines containing "COLLEGE"
    if not fields["College Name"]:
        col_match = re.search(r'([A-Z\s]*COLLEGE[A-Z\s]*)', upper_text)
        if col_match:
            col_name = col_match.group(1).strip()
            # Clean up college name
            col_name = clean_college_name(col_name)
            if col_name:
                fields["College Name"] = col_name
    
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
        branch_match = re.search(r'([A-Z\s]*SCIENCE\s+AND\s+ENGINEERING[A-Z\s]*)', upper_text)
        if branch_match:
            branch = branch_match.group(1).strip()
            # Clean up branch name
            branch = clean_branch_name(branch)
            if branch:
                fields["Branch"] = branch
    
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
