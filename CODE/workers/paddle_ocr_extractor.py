#!/usr/bin/env python3
"""
Paddle OCR Extractor for Certificate Processing
High-accuracy OCR using Baidu's PaddleOCR engine
"""

import argparse
import json
import os
import sys
import tempfile
import numpy as np

try:
    from paddleocr import PaddleOCR
    import cv2
except ImportError as e:
    print(json.dumps({"error": f"PaddleOCR not installed: {str(e)}"}))
    sys.exit(1)

class PaddleOCRExtractor:
    def __init__(self):
        # Initialize PaddleOCR with optimized settings for certificate processing
        self.ocr = PaddleOCR(
            use_angle_cls=True,
            lang='en',
            use_gpu=False,  # Set to True if GPU available
            show_log=False,
            max_text_length=50,  # Good for certificate text
            det_limit_side_len=1920,  # Limit image size for performance
            det_limit_type='min'  # Maintain aspect ratio
        )

    def preprocess_image(self, image_path):
        """Preprocess image for better OCR accuracy"""
        try:
            # Read image
            img = cv2.imread(image_path)

            # Convert to grayscale
            gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)

            # Apply Gaussian blur to reduce noise
            blurred = cv2.GaussianBlur(gray, (3, 3), 0)

            # Apply adaptive thresholding for better text contrast
            thresh = cv2.adaptiveThreshold(
                blurred, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C,
                cv2.THRESH_BINARY, 11, 2
            )

            # Morphological operations to clean up text
            kernel = np.ones((2, 2), np.uint8)
            cleaned = cv2.morphologyEx(thresh, cv2.MORPH_CLOSE, kernel)

            # Save preprocessed image
            preprocessed_path = image_path.replace('.jpg', '_preprocessed.jpg').replace('.png', '_preprocessed.png')
            cv2.imwrite(preprocessed_path, cleaned)

            return preprocessed_path, cleaned

        except Exception as e:
            print(f"Preprocessing error: {e}", file=sys.stderr)
            return image_path, None

    def extract_text(self, image_path):
        """Extract text using PaddleOCR"""
        try:
            # Preprocess image
            processed_path, processed_img = self.preprocess_image(image_path)

            # Perform OCR
            results = self.ocr.ocr(processed_path, cls=True)

            if not results or not results[0]:
                return {
                    "error": "No text detected",
                    "text": "",
                    "confidence": 0.0,
                    "boxes": []
                }

            # Extract text and confidence scores
            extracted_text = []
            confidence_scores = []
            boxes = []

            for line in results[0]:
                if len(line) >= 2:
                    box, (text, confidence) = line
                    extracted_text.append(text)
                    confidence_scores.append(float(confidence))
                    boxes.append(box)

            # Combine all text
            full_text = '\n'.join(extracted_text)

            # Calculate average confidence
            avg_confidence = sum(confidence_scores) / len(confidence_scores) if confidence_scores else 0.0

            # Clean up preprocessed file if created
            if processed_path != image_path and os.path.exists(processed_path):
                try:
                    os.remove(processed_path)
                except:
                    pass

            return {
                "text": full_text,
                "confidence": min(avg_confidence, 1.0),  # Cap at 1.0
                "boxes": boxes,
                "individual_confidences": confidence_scores,
                "detected_lines": len(extracted_text)
            }

        except Exception as e:
            return {
                "error": str(e),
                "text": "",
                "confidence": 0.0,
                "boxes": []
            }

    def extract_structured_fields(self, text):
        """Extract structured fields from OCR text (similar to certificate extractor)"""
        if not text:
            return {}

        fields = {
            "Student Name": None,
            "Roll Number": None,
            "Hall Ticket No": None,
            "University Name": None,
            "College Name": None,
            "Branch": None,
            "CGPA": None,
            "SGPA": None,
            "Result": None,
            "Grade": None,
            "Examination Date": None,
            "Serial Number": None
        }

        lines = text.split('\n')

        for line in lines:
            line_upper = line.upper().strip()

            # Student Name
            if not fields["Student Name"] and any(keyword in line_upper for keyword in ["NAME", "STUDENT", "CANDIDATE"]):
                name = line.strip()
                if len(name) > 2 and not any(word in name.upper() for word in ["NAME", "STUDENT", "CANDIDATE", "ROLL", "NUMBER"]):
                    fields["Student Name"] = name

            # Roll Number
            if not fields["Roll Number"] and "ROLL" in line_upper:
                roll_match = line.split("ROLL")[-1].strip() if "ROLL" in line else ""
                if roll_match and len(roll_match) > 3:
                    fields["Roll Number"] = roll_match

            # Hall Ticket
            if not fields["Hall Ticket No"] and "HALL TICKET" in line_upper:
                ht_match = line.split("HALL TICKET")[-1].strip() if "HALL TICKET" in line else ""
                if ht_match and len(ht_match) > 3:
                    fields["Hall Ticket No"] = ht_match

            # University
            if not fields["University Name"] and "UNIVERSITY" in line_upper:
                uni_match = line.strip()
                if len(uni_match) > 10:
                    fields["University Name"] = uni_match

            # College
            if not fields["College Name"] and "COLLEGE" in line_upper:
                col_match = line.strip()
                if len(col_match) > 10:
                    fields["College Name"] = col_match

            # Branch
            if not fields["Branch"] and any(keyword in line_upper for keyword in ["BRANCH", "COURSE", "ENGINEERING", "SCIENCE"]):
                branch_match = line.strip()
                if len(branch_match) > 5 and branch_match.lower().endswith("engineering"):
                    fields["Branch"] = branch_match
            # Serial Number
            if not fields["Serial Number"]:
                import re
                serial_match = re.search(r'\b([A-Za-z0-9]{2}\d{6})\b', line)
                if serial_match:
                    serial_num = serial_match.group(1)
                    # Correct first two characters if they are digits by mapping to letters
                    def correct_chars(chars):
                        mapping = {
                            '0': 'O',
                            '1': 'I',
                            '2': 'Z',
                            '3': 'E',
                            '4': 'A',
                            '5': 'S',
                            '6': 'G',
                            '7': 'T',
                            '8': 'B',
                            '9': 'P'
                        }
                        corrected = ''
                        for c in chars:
                            if c.isdigit():
                                corrected += mapping.get(c, c)
                            else:
                                corrected += c
                        return corrected
                    corrected_prefix = correct_chars(serial_num[:2])
                    corrected_serial = corrected_prefix + serial_num[2:]
                    fields["Serial Number"] = corrected_serial

            # CGPA
            if not fields["CGPA"] and "CGPA" in line_upper:
                import re
                cgpa_match = re.search(r'CGPA[:\s]*([0-9]+\.[0-9]+)', line_upper)
                if cgpa_match:
                    fields["CGPA"] = cgpa_match.group(1)

            # SGPA
            if not fields["SGPA"] and "SGPA" in line_upper:
                import re
                sgpa_match = re.search(r'SGPA[:\s]*([0-9]+\.[0-9]+)', line_upper)
                if sgpa_match:
                    fields["SGPA"] = sgpa_match.group(1)

            # Result
            if not fields["Result"] and any(keyword in line_upper for keyword in ["PASS", "FAIL", "RESULT"]):
                if "PASS" in line_upper:
                    fields["Result"] = "PASS"
                elif "FAIL" in line_upper:
                    fields["Result"] = "FAIL"

            # Grade
            if not fields["Grade"] and any(grade in line_upper for grade in ["A+", "A", "B+", "B", "C+", "C", "D", "F"]):
                import re
                grade_match = re.search(r'\b([A-F][+-]?)\b', line_upper)
                if grade_match:
                    fields["Grade"] = grade_match.group(1)

            # Serial Number
            if not fields["Serial Number"]:
                import re
                serial_match = re.search(r'\b([A-Za-z]{2}\d{6})\b', line)
                if serial_match:
                    fields["Serial Number"] = serial_match.group(1)

        return fields

def convert_pdf_to_image(pdf_path):
    """Convert PDF to image for OCR processing"""
    try:
        from pdf2image import convert_from_path
        pages = convert_from_path(pdf_path, dpi=300)  # Higher DPI for better OCR
        if not pages:
            return None

        # Save first page as image
        temp_file = tempfile.NamedTemporaryFile(delete=False, suffix='.png')
        pages[0].save(temp_file.name, 'PNG', quality=95)
        return temp_file.name
    except Exception as e:
        print(f"PDF conversion error: {e}", file=sys.stderr)
        return None

def main():
    parser = argparse.ArgumentParser(description='Extract text using PaddleOCR')
    parser.add_argument("--image", required=True, help="Path to image or PDF file")
    parser.add_argument("--extract-fields", action="store_true", help="Extract structured fields")
    args = parser.parse_args()

    try:
        extractor = PaddleOCRExtractor()

        src = args.image
        ext = os.path.splitext(src)[1].lower()

        # Convert PDF if needed
        if ext == '.pdf':
            img_path = convert_pdf_to_image(src)
            if not img_path:
                print(json.dumps({"error": "PDF conversion failed"}))
                return
        else:
            img_path = src

        # Extract text
        result = extractor.extract_text(img_path)

        # Extract structured fields if requested
        if args.extract_fields and result.get("text"):
            fields = extractor.extract_structured_fields(result["text"])
            result["extracted_fields"] = fields

        # Clean up converted PDF
        if ext == '.pdf' and img_path != src:
            try:
                os.remove(img_path)
            except:
                pass

        print(json.dumps(result, ensure_ascii=False))

    except Exception as e:
        error_result = {
            "error": str(e),
            "text": "",
            "confidence": 0.0
        }
        print(json.dumps(error_result))

if __name__ == "__main__":
    main()
