import pytesseract
from PIL import Image, ImageEnhance, ImageFilter
import sys
import os
import cv2
import numpy as np

# Set path if not automatically detected
pytesseract.pytesseract.tesseract_cmd = r"C:\Program Files\Tesseract-OCR\tesseract.exe"

def preprocess_image(img):
    # Convert PIL Image to OpenCV format
    img_cv = cv2.cvtColor(np.array(img), cv2.COLOR_RGB2BGR)
    
    # Convert to grayscale
    gray = cv2.cvtColor(img_cv, cv2.COLOR_BGR2GRAY)
    
    # Apply threshold to get black and white image
    _, binary = cv2.threshold(gray, 150, 255, cv2.THRESH_BINARY_INV + cv2.THRESH_OTSU)
    
    # Noise removal
    kernel = np.ones((1, 1), np.uint8)
    opening = cv2.morphologyEx(binary, cv2.MORPH_OPEN, kernel, iterations=1)
    
    # Convert back to PIL Image
    return Image.fromarray(opening)

def extract_text_from_image(image_path):
    try:
        # Check if file exists
        if not os.path.exists(image_path):
            return f"Error: File {image_path} does not exist"
            
        # Open the image
        img = Image.open(image_path)
        
        # Try direct extraction first
        text = pytesseract.image_to_string(img)
        
        # If no text was found, try preprocessing
        if not text.strip():
            # Try with PIL enhancements
            enhancer = ImageEnhance.Contrast(img)
            enhanced_img = enhancer.enhance(2.0)  # Increase contrast
            enhanced_img = enhanced_img.filter(ImageFilter.SHARPEN)  # Sharpen
            text = pytesseract.image_to_string(enhanced_img)
            
            # If still no text, try OpenCV preprocessing
            if not text.strip():
                try:
                    preprocessed = preprocess_image(img)
                    text = pytesseract.image_to_string(preprocessed)
                except Exception as e:
                    # If OpenCV processing fails, continue with other methods
                    pass
                    
            # Try with different PSM modes if still no text
            if not text.strip():
                # PSM 6 - Assume a single uniform block of text
                custom_config = r'--oem 3 --psm 6'
                text = pytesseract.image_to_string(img, config=custom_config)
                
                # If still no text, try PSM 4 - Assume a single column of text
                if not text.strip():
                    custom_config = r'--oem 3 --psm 4'
                    text = pytesseract.image_to_string(img, config=custom_config)
        
        # If we still have no text, provide a helpful message
        if not text.strip():
            return "No text could be extracted from this image. The image may not contain readable text, or the text might be in a format that's difficult for OCR to recognize."
            
        return text
    except Exception as e:
        return f"Error extracting text: {str(e)}"

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Please provide an image path")
        sys.exit(1)
    
    image_path = sys.argv[1]
    extracted_text = extract_text_from_image(image_path)
    print(extracted_text)