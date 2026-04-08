"""
OCR Parser - Extract text from images using Tesseract
"""
from PIL import Image
import pytesseract
import logging

logger = logging.getLogger(__name__)


class OCRParser:
    """Parse images and extract text using OCR"""
    
    @staticmethod
    def extract_text(file_path: str) -> str:
        """
        Extract text from image using OCR
        
        Args:
            file_path: Path to image file
            
        Returns:
            Extracted text
        """
        try:
            # Open image
            image = Image.open(file_path)
            
            # Rotate if needed for better OCR
            if hasattr(image, '_getexif') and image._getexif() is not None:
                try:
                    # Auto-rotate based on EXIF data
                    from PIL import ImageOps
                    image = ImageOps.exif_transpose(image)
                except:
                    pass
            
            # Extract text
            text = pytesseract.image_to_string(image, lang='fra+eng')
            
            if not text.strip():
                logger.warning(f"OCR extracted no text from {file_path}")
            else:
                logger.info(f"Successfully extracted text from image using OCR")
            
            return text
            
        except pytesseract.TesseractNotInstalledError:
            logger.warning("Tesseract not installed - returning empty text")
            return ""
        except Exception as e:
            logger.error(f"Error in OCR extraction: {e}")
            raise
