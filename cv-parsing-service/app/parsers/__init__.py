"""
Parser modules for different file types
"""
from .pdf_parser import PDFParser
from .docx_parser import DOCXParser
from .ocr_parser import OCRParser
from .text_extractor import TextExtractor
from .spacy_extractor import SpacyExtractor
from .ollama_parser import OllamaParser

__all__ = ['PDFParser', 'DOCXParser', 'OCRParser', 'TextExtractor', 'SpacyExtractor', 'OllamaParser']
