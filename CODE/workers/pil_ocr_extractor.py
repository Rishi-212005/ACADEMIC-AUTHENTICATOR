import argparse
import json
import os
import sys
import tempfile

try:
    import pytesseract
    from PIL import Image
except Exception as e:
    pytesseract = None
    Image = None
    print(json.dumps({"error": f"Required libraries not installed: {str(e)}"}))
    sys.exit(1)

def ocr_image(path):
    # Set path if not automatically detected
    if os.name == 'nt':
        tesseract_path = r"C:\Program Files\Tesseract-OCR\tesseract.exe"
        if os.path.exists(tesseract_path):
            pytesseract.pytesseract.tesseract_cmd = tesseract_path
    
    try:
        # Open the image
        img = Image.open(path)
        
        # Extract text
        text = pytesseract.image_to_string(img)
        
        # Calculate confidence based on text length
        confidence = min(0.95, 0.6 + len(text.strip()) / 5000.0)
        
        return {
            "text": text,
            "confidence": confidence
        }
    except Exception as e:
        return {
            "error": str(e),
            "text": "",
            "confidence": 0
        }

def convert_pdf_to_image(pdf_path):
    try:
        # Try Pillow's PDF handling via Ghostscript/poppler if available
        # Many Windows systems may not have it; we attempt a best-effort fallback.
        from pdf2image import convert_from_path  # optional dependency
        pages = convert_from_path(pdf_path, dpi=200)
        if not pages:
            return None
        # Merge first page only for now (can extend to multiple pages)
        tmp = tempfile.NamedTemporaryFile(delete=False, suffix='.png')
        pages[0].save(tmp.name, 'PNG')
        return tmp.name
    except Exception:
        return None

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--image", required=True, help="Path to the image or PDF file")
    args = parser.parse_args()
    
    src = args.image
    ext = os.path.splitext(src)[1].lower()
    if ext == '.pdf':
        img_path = convert_pdf_to_image(src)
        if not img_path:
            print(json.dumps({"error": "PDF to image conversion failed. Please upload an image or install pdf2image/poppler."}))
            return
        result = ocr_image(img_path)
        try:
            os.remove(img_path)
        except Exception:
            pass
    else:
        result = ocr_image(src)
    print(json.dumps(result))

if __name__ == "__main__":
    main()