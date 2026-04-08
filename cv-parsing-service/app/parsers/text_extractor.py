"""
Text Extractor - Route to correct parser based on file type
"""
import os
import logging
from .pdf_parser import PDFParser
from .docx_parser import DOCXParser
from .ocr_parser import OCRParser

logger = logging.getLogger(__name__)


class TextExtractor:
    """Extract text from different file types"""
    
    @staticmethod
    def extract(file_path: str) -> str:
        """
        Extract text from file based on extension
        
        Args:
            file_path: Path to file
            
        Returns:
            Extracted text
        """
        extension = os.path.splitext(file_path)[1].lower().lstrip('.')
        
        try:
            if extension == 'pdf':
                return PDFParser.extract_text(file_path)
            elif extension in ['docx', 'doc']:
                return DOCXParser.extract_text(file_path)
            elif extension in ['jpg', 'jpeg', 'png']:
                return OCRParser.extract_text(file_path)
            else:
                raise ValueError(f"Unsupported file type: {extension}")
                
        except Exception as e:
            logger.error(f"Text extraction failed for {file_path}: {e}")
            raise
