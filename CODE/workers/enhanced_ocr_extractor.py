#!/usr/bin/env python3
"""
Enhanced OCR Text Extractor for Academic Certificates
Extracts structured information from raw OCR text with improved accuracy
"""

import re
import json
from typing import Dict, Optional, List, Tuple
from datetime import datetime


class EnhancedOCRExtractor:
    def __init__(self):
        """Initialize the OCR extractor with comprehensive patterns"""
        self.patterns = self._initialize_patterns()
        self.month_corrections = {
            'DE!': 'DECEMBER', 'DE': 'DECEMBER', 'NOV': 'NOVEMBER',
            'OCT': 'OCTOBER', 'SEP': 'SEPTEMBER', 'AUG': 'AUGUST',
            'JUL': 'JULY', 'JULYY': 'JULY', 'JULY!': 'JULY',
            'JUN': 'JUNE', 'JUNEE': 'JUNE', 'JUNE!': 'JUNE',
            'MAY': 'MAY', 'APR': 'APRIL', 'MAR': 'MARCH',
            'FEB': 'FEBRUARY', 'JAN': 'JANUARY'
        }
    
    def _initialize_patterns(self) -> Dict[str, List[re.Pattern]]:
        """Initialize comprehensive regex patterns for field extraction"""
        return {
            "Student Name": [
                re.compile(r"(?:STUDENT\s*NAME|CANDIDATE\s*NAME|NAME)\s*[:\-]?\s*([A-Z][A-Z\s\.']{2,30})(?:\s+[A-Z]{2,})?", re.IGNORECASE),
                re.compile(r"(?:STUDENT\s*NAME|CANDIDATE\s*NAME|NAME)\s*[:\-]?\s*([A-Z][A-Z\s\.']{2,30})", re.IGNORECASE),
                re.compile(r"^([A-Z][A-Z\s\.']{2,30})(?=\s+[A-Z]{2,}\s+[A-Z]{2,})", re.IGNORECASE)  # Fallback for name at start
            ],
            "Roll Number": [
                re.compile(r"(?:ROLL\s*(?:NO|NUMBER)?|ROLL\s*NO)\s*[:\-]?\s*([0-9]{5}[A-Z0-9][0-9]{4})", re.IGNORECASE),
                re.compile(r"(?:ROLL\s*(?:NO|NUMBER)?|ROLL\s*NO)\s*[:\-]?\s*([A-Z0-9]{8,12})", re.IGNORECASE),
                re.compile(r"\b(\d{5}[A-Z0-9]\d{4})\b")  # Fallback pattern
            ],
            "Hall Ticket No": [
                re.compile(r"(?:HALL\s*TICKET\s*(?:NO|NUMBER)?|HT\s*NO|HT\s*NUMBER)\s*[:\-]?\s*([0-9]{5}[A-Z0-9][0-9]{4})", re.IGNORECASE),
                re.compile(r"(?:HALL\s*TICKET\s*(?:NO|NUMBER)?|HT\s*NO|HT\s*NUMBER)\s*[:\-]?\s*([A-Z0-9]{8,12})", re.IGNORECASE),
                re.compile(r"\b(\d{5}[A-Z0-9]\d{4})\b")  # Fallback pattern
            ],
            "Seena No": [
                re.compile(r"(?:SEENA\s*NO|SEENA\s*NUMBER)\s*[:\-]?\s*([A-Z0-9]{6,12})", re.IGNORECASE),
                re.compile(r"(?:SEENA\s*NO|SEENA\s*NUMBER)\s*[:\-]?\s*([A-Z0-9]{6,12})", re.IGNORECASE)
            ],
            "Examination Date": [
                re.compile(r"(?:EXAMINATION\s*DATE|EXAM\s*DATE|DATE\s*OF\s*EXAM)\s*[:\-]?\s*([A-Z]+\s+\d{4})", re.IGNORECASE),
                re.compile(r"(?:EXAMINATION\s*DATE|EXAM\s*DATE|DATE\s*OF\s*EXAM)\s*[:\-]?\s*(\d{1,2}[/\-\.]\d{1,2}[/\-\.]\d{2,4})", re.IGNORECASE),
                re.compile(r"(?:MONTH\s*[&]?\s*YEAR\s*OF\s*EXAM|EXAM\s*MONTH\s*[&]?\s*YEAR)\s*[:\-]?\s*([A-Z]+\s+\d{4})", re.IGNORECASE)
            ],
            "University Name": [
                re.compile(r"(?:UNIVERSITY\s*NAME|UNIVERSITY)\s*[:\-]?\s*([A-Z\s]+?)(?=\s+[A-Z]{2,}|$)", re.IGNORECASE),
                re.compile(r"([A-Z\s]*UNIVERSITY[A-Z\s]*?)(?=\s+[A-Z]{2,}|$)", re.IGNORECASE),
                re.compile(r"([A-Z\s]*UNIVERSITY[A-Z\s]*)", re.IGNORECASE)
            ],
            "College Name": [
                re.compile(r"(?:COLLEGE\s*NAME|COLLEGE)\s*[:\-]?\s*([A-Z\s]+?)(?=\s+[A-Z]{2,}|$)", re.IGNORECASE),
                re.compile(r"(?:COLLEGE\s*OF\s*[A-Z\s]+|COLLEGE\s*OF\s*ENGINEERING[A-Z\s]*|[A-Z\s]+\s*COLLEGE[A-Z\s]*?)(?=\s+[A-Z]{2,}|$)", re.IGNORECASE),
                re.compile(r"(?:COLLEGE\s*OF\s*[A-Z\s]+|COLLEGE\s*OF\s*ENGINEERING[A-Z\s]*|[A-Z\s]+\s*COLLEGE[A-Z\s]*)", re.IGNORECASE)
            ],
            "Branch": [
                re.compile(r"(?:BRANCH|STREAM|DEPARTMENT)\s*[:\-]?\s*([A-Z\s&/]+?)(?=\s+[A-Z]{2,}|$)", re.IGNORECASE),
                re.compile(r"(?:BRANCH|STREAM|DEPARTMENT)\s*[:\-]?\s*([A-Z\s&/]+)", re.IGNORECASE),
                re.compile(r"(?:BRANCH|STREAM|DEPARTMENT)\s*[:\-]?\s*([A-Z\s&/]+)", re.IGNORECASE)
            ],
            "CGPA": [
                re.compile(r"(?:CGPA|C\.G\.P\.A)\s*[:\-]?\s*([0-9]+\.?[0-9]*)", re.IGNORECASE),
                re.compile(r"(?:CGPA|C\.G\.P\.A)\s*[:\-]?\s*([0-9]+\.?[0-9]*)", re.IGNORECASE),
                re.compile(r"\b([0-9]+\.[0-9]{2})\b")  # Fallback for decimal pattern
            ],
            "CGPA (Scale 10)": [
                re.compile(r"(?:CGPA|C\.G\.P\.A)\s*\(SCALE\s*10\)\s*[:\-]?\s*([0-9]+\.?[0-9]*)", re.IGNORECASE),
                re.compile(r"(?:CGPA|C\.G\.P\.A)\s*\(SCALE\s*10\)\s*[:\-]?\s*([0-9]+\.?[0-9]*)", re.IGNORECASE)
            ],
            "Achievement": [
                re.compile(r"(?:FIRST\s+CLASS\s+WITH\s+DISTINCTION|FIRST\s+CLASS|SECOND\s+CLASS|THIRD\s+CLASS|DISTINCTION|HONOURS|MERIT)", re.IGNORECASE),
                re.compile(r"(?:FIRST\s+CLASS\s+WITH\s+DISTINCTION|FIRST\s+CLASS|SECOND\s+CLASS|THIRD\s+CLASS|DISTINCTION|HONOURS|MERIT)", re.IGNORECASE),
                re.compile(r"\*\*\*([^*]+)\*\*\*")  # Text between asterisks
            ]
        }
    
    def preprocess_text(self, text: str) -> str:
        """Preprocess OCR text to fix common errors and improve accuracy"""
        if not text or not isinstance(text, str):
            return ""
        
        # Fix common OCR character misreads
        corrections = {
            '|': 'I',  # Pipe to I
            '0': 'O',  # Zero to O (context-dependent)
            '1': 'l',  # One to l (context-dependent)
            '5': 'S',  # Five to S (context-dependent)
            '8': 'B',  # Eight to B (context-dependent)
            '6': 'G',  # Six to G (context-dependent)
            '9': 'g',  # Nine to g (context-dependent)
            '2': 'Z',  # Two to Z (context-dependent)
            '3': 'E',  # Three to E (context-dependent)
            '4': 'A',  # Four to A (context-dependent)
            '7': 'T'   # Seven to T (context-dependent)
        }
        
        processed = text
        
        # Apply character corrections carefully
        for wrong, correct in corrections.items():
            # Only replace in specific contexts to avoid over-correction
            if wrong == '|':
                processed = processed.replace('|', 'I')
            elif wrong == '0' and 'HALL TICKET' in processed.upper():
                # In hall ticket context, 0 might be correct
                pass
            else:
                # Apply other corrections more carefully
                processed = processed.replace(wrong, correct)
        
        # Fix spacing and formatting issues
        processed = re.sub(r'\s+', ' ', processed)  # Multiple spaces to single
        processed = re.sub(r'\n\s+', '\n', processed)  # Remove leading spaces from lines
        processed = re.sub(r'\s+\n', '\n', processed)  # Remove trailing spaces from lines
        
        # Remove common OCR artifacts
        processed = re.sub(r'[^\w\s\.\(\)\-\/|:]', ' ', processed)  # Remove special characters
        processed = re.sub(r'\s+', ' ', processed)  # Clean up spaces
        processed = processed.strip()
        
        return processed
    
    def auto_correct(self, text: str) -> str:
        """Apply auto-corrections to fix common OCR errors"""
        if not text or not isinstance(text, str):
            return ""
        
        corrected = text
        
        # Remove common OCR artifacts from names
        corrected = re.sub(r'\s+ea\s*$', '', corrected, flags=re.IGNORECASE)
        corrected = re.sub(r'\s+ea\s+', ' ', corrected, flags=re.IGNORECASE)
        corrected = re.sub(r'\s+[a-z]{1,2}\s*$', '', corrected, flags=re.IGNORECASE)
        corrected = re.sub(r'\s+[a-z]{1,2}\s+', ' ', corrected, flags=re.IGNORECASE)
        
        # Fix Hall Ticket Number specific corrections
        corrected = re.sub(r'(\d{5})4(\d{4})', r'\1A\2', corrected)  # Fix 4->A
        corrected = re.sub(r'(\d{5})[O0](\d{4})', r'\1A\2', corrected)  # Fix O/0->A
        corrected = re.sub(r'(\d{5})[B8](\d{4})', r'\1A\2', corrected)  # Fix B/8->A
        
        # Apply month corrections
        for wrong, correct in self.month_corrections.items():
            corrected = re.sub(wrong, correct, corrected, flags=re.IGNORECASE)
        
        # University/College name corrections
        corrected = re.sub(r'JAWAHARLAL\s+NEHRU\s+TECHNOLOGICAL\s+UNIVERSITY\s+ANANTAPUR\s+COLLEGE', 
                          'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR COLLEGE', 
                          corrected, flags=re.IGNORECASE)
        
        # Course corrections
        corrected = re.sub(r'B\.?\s*TECH', 'B.Tech', corrected, flags=re.IGNORECASE)
        corrected = re.sub(r'M\.?\s*TECH', 'M.Tech', corrected, flags=re.IGNORECASE)
        corrected = re.sub(r'B\.?\s*SC', 'B.Sc', corrected, flags=re.IGNORECASE)
        corrected = re.sub(r'M\.?\s*SC', 'M.Sc', corrected, flags=re.IGNORECASE)
        
        # Branch corrections
        corrected = re.sub(r'COMPUTER\s+SCIENCE\s+AND\s+ENGINEERING', 
                          'COMPUTER SCIENCE AND ENGINEERING', 
                          corrected, flags=re.IGNORECASE)
        
        # Clean up any remaining artifacts
        corrected = re.sub(r'[^\w\s\.\(\)\-\/|:]', '', corrected)
        corrected = re.sub(r'\s+', ' ', corrected).strip()
        
        return corrected
    
    def validate_hall_ticket_number(self, hall_ticket: str) -> str:
        """Validate and fix Hall Ticket Number format"""
        if not hall_ticket or not isinstance(hall_ticket, str):
            return ""
        
        # Remove any spaces or special characters
        cleaned = re.sub(r'[^0-9A-Z]', '', hall_ticket.upper())
        
        # Check if it's exactly 10 characters
        if len(cleaned) != 10:
            return hall_ticket
        
        # Check if it matches the pattern: 5 digits + letter + 4 digits
        pattern = r'^(\d{5})([A-Z])(\d{4})$'
        match = re.match(pattern, cleaned)
        
        if match:
            return cleaned  # Already correct format
        else:
            # Try to fix common OCR errors
            fixed = cleaned
            # Fix 4->A, O->0, B->8 in the 6th position (letter position)
            fixed = re.sub(r'^(\d{5})[4O0B8](\d{4})$', r'\1A\2', fixed)
            return fixed
    
    def extract_field_value(self, text: str, field_name: str) -> Optional[str]:
        """Extract value for a specific field using multiple patterns"""
        if field_name not in self.patterns:
            return None
        
        for pattern in self.patterns[field_name]:
            match = pattern.search(text)
            if match:
                # Extract the captured group (value)
                groups = match.groups()
                if groups:
                    value = groups[0] if groups[0] else groups[-1]
                    if value and isinstance(value, str):
                        return self.auto_correct(value.strip())
        
        return None
    
    def extract_fields(self, ocr_text: str) -> Dict[str, Optional[str]]:
        """Extract all required fields from OCR text"""
        if not ocr_text or not isinstance(ocr_text, str):
            return self._get_empty_fields()
        
        # Preprocess the text
        processed_text = self.preprocess_text(ocr_text)
        normalized_text = processed_text.replace('\t', ' ').replace('\u00A0', ' ').strip()
        upper_text = normalized_text.upper()
        
        # Initialize result dictionary
        fields = self._get_empty_fields()
        
        # Extract each field
        for field_name in fields.keys():
            value = self.extract_field_value(normalized_text, field_name)
            if value:
                fields[field_name] = value
        
        # Apply fallback detection methods
        fields = self._apply_fallback_detection(fields, normalized_text, upper_text)
        
        # Validate and clean extracted values
        fields = self._validate_and_clean_fields(fields)
        
        return fields
    
    def _get_empty_fields(self) -> Dict[str, Optional[str]]:
        """Return empty fields dictionary"""
        return {
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
    
    def _apply_fallback_detection(self, fields: Dict[str, Optional[str]], 
                                text: str, upper_text: str) -> Dict[str, Optional[str]]:
        """Apply fallback detection methods for missing fields"""
        lines = [line.strip() for line in text.split('\n') if line.strip()]
        
        # Fallback for Hall Ticket No - look for 10-character alphanumeric pattern
        if not fields["Hall Ticket No"]:
            ht_match = re.search(r'\b(\d{5}[A-Z0-9]\d{4})\b', upper_text)
            if ht_match:
                fields["Hall Ticket No"] = self.validate_hall_ticket_number(ht_match.group(1))
        
        # Fallback for Roll Number - look for 10-character alphanumeric pattern
        if not fields["Roll Number"]:
            roll_match = re.search(r'\b(\d{5}[A-Z0-9]\d{4})\b', upper_text)
            if roll_match:
                fields["Roll Number"] = self.validate_hall_ticket_number(roll_match.group(1))
        
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
                fields["Examination Date"] = self.auto_correct(date_match.group(0))
        
        # Fallback for University Name - look for lines containing "UNIVERSITY"
        if not fields["University Name"]:
            # Look for the specific pattern in the sample data
            uni_match = re.search(r'UNIVERSITY\s*NAME[:\-]?\s*([A-Z\s]+?)(?=\s+[A-Z]{2,}|$)', upper_text)
            if uni_match:
                fields["University Name"] = self.auto_correct(uni_match.group(1).strip())
            else:
                # General university pattern
                uni_line = next((line for line in lines if 'UNIVERSITY' in line.upper()), None)
                if uni_line:
                    # Extract just the university part
                    uni_part = re.search(r'([A-Z\s]*UNIVERSITY[A-Z\s]*)', uni_line.upper())
                    if uni_part:
                        fields["University Name"] = self.auto_correct(uni_part.group(1))
        
        # Fallback for College Name - look for lines containing "COLLEGE"
        if not fields["College Name"]:
            # Look for the specific pattern in the sample data
            col_match = re.search(r'COLLEGE\s*NAME[:\-]?\s*([A-Z\s]+?)(?=\s+[A-Z]{2,}|$)', upper_text)
            if col_match:
                fields["College Name"] = self.auto_correct(col_match.group(1).strip())
            else:
                # General college pattern
                col_line = next((line for line in lines if 'COLLEGE' in line.upper()), None)
                if col_line:
                    # Extract just the college part
                    col_part = re.search(r'([A-Z\s]*COLLEGE[A-Z\s]*)', col_line.upper())
                    if col_part:
                        fields["College Name"] = self.auto_correct(col_part.group(1))
        
        # Fallback for Student Name - look for name patterns at the beginning
        if not fields["Student Name"]:
            # Look for the specific pattern in the sample data
            name_match = re.search(r'STUDENT\s*NAME[:\-]?\s*([A-Z][A-Z\s\.\']{2,30})(?=\s+[A-Z]{2,})', upper_text)
            if name_match:
                fields["Student Name"] = self.auto_correct(name_match.group(1).strip())
            else:
                # General name pattern
                name_match = re.search(r'^([A-Z][A-Z\s\.\']{2,50})', text)
                if name_match:
                    potential_name = name_match.group(1).strip()
                    # Check if it looks like a name (not too long, contains letters)
                    if len(potential_name) <= 50 and re.search(r'[A-Za-z]', potential_name):
                        fields["Student Name"] = self.auto_correct(potential_name)
        
        # Fallback for Branch - look for specific pattern
        if not fields["Branch"]:
            branch_match = re.search(r'BRANCH[:\-]?\s*([A-Z\s&/]+?)(?=\s+[A-Z]{2,}|$)', upper_text)
            if branch_match:
                fields["Branch"] = self.auto_correct(branch_match.group(1).strip())
        
        return fields
    
    def _validate_and_clean_fields(self, fields: Dict[str, Optional[str]]) -> Dict[str, Optional[str]]:
        """Validate and clean extracted field values"""
        validated_fields = fields.copy()
        
        # Validate Hall Ticket Number format
        if validated_fields["Hall Ticket No"]:
            validated_fields["Hall Ticket No"] = self.validate_hall_ticket_number(validated_fields["Hall Ticket No"])
        
        # Validate Roll Number format
        if validated_fields["Roll Number"]:
            validated_fields["Roll Number"] = self.validate_hall_ticket_number(validated_fields["Roll Number"])
        
        # Validate CGPA values
        for cgpa_field in ["CGPA", "CGPA (Scale 10)"]:
            if validated_fields[cgpa_field]:
                try:
                    cgpa_value = float(validated_fields[cgpa_field])
                    if cgpa_value < 0 or cgpa_value > 10:
                        validated_fields[cgpa_field] = None
                except (ValueError, TypeError):
                    validated_fields[cgpa_field] = None
        
        # Clean up Student Name
        if validated_fields["Student Name"]:
            name = validated_fields["Student Name"]
            # Remove common OCR artifacts
            name = re.sub(r'\s+[a-z]{1,2}\s*$', '', name, flags=re.IGNORECASE)
            name = re.sub(r'\s+[a-z]{1,2}\s+', ' ', name, flags=re.IGNORECASE)
            validated_fields["Student Name"] = name.strip()
        
        return validated_fields


def extract_fields(ocr_text: str) -> Dict[str, Optional[str]]:
    """
    Main function to extract structured fields from OCR text
    
    Args:
        ocr_text (str): Raw OCR text from certificate
        
    Returns:
        Dict[str, Optional[str]]: Dictionary with extracted field values
    """
    extractor = EnhancedOCRExtractor()
    return extractor.extract_fields(ocr_text)


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
