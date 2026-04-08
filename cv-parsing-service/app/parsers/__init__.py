"""
Parser modules for different file types
"""
from .pdf_parser import PDFParser
from .docx_parser import DOCXParser
from .ocr_parser import OCRParser
from .text_extractor import TextExtractor

__all__ = ['PDFParser', 'DOCXParser', 'OCRParser', 'TextExtractor']
